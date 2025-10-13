<?php
/**
 * Newsletter Migration Script - Auto-run on theme load
 *
 * Migrates child pages of "Newsletters" pages to Newsletter custom post type
 * Runs automatically when theme loads to ensure data migration on deployment
 *
 * @package NovaraMedia
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

// Auto-run migration on theme activation and admin init
add_action( 'after_switch_theme', 'nm_auto_migrate_newsletters' );
add_action( 'admin_init', 'nm_auto_migrate_newsletters_check' );

/**
 * Run migration automatically on theme switch
 */
function nm_auto_migrate_newsletters() {
  nm_migrate_newsletters( false ); // Run live migration
}

/**
 * Check and run migration once per theme version on admin pages
 */
function nm_auto_migrate_newsletters_check() {
  // Only run once per theme version using option to track
  $migration_version = get_option( 'nm_newsletter_migration_version', '0' );
  $current_version = wp_get_theme()->get( 'Version' ) ?: '1.0.0';

  if ( version_compare( $migration_version, $current_version, '<' ) ) {
    nm_migrate_newsletters( false ); // Run live migration
    update_option( 'nm_newsletter_migration_version', $current_version );
  }
}

/**
 * Main migration function
 *
 * @param bool $dry_run Whether to perform a dry run (default: true)
 * @return array Migration results
 */
function nm_migrate_newsletters( $dry_run = true ) {
  $results = array(
    'found_pages' => 0,
    'migrated' => 0,
    'errors' => array(),
    'log' => array()
  );

  // Log function - simplified for auto-run
  $log = function( $message ) use ( &$results ) {
    $results['log'][] = $message;
    // Silent logging for auto-run - logs are stored in results array
  };

  $log( $dry_run ? 'Starting DRY RUN migration...' : 'Starting LIVE migration...' );

  // Find all pages titled "Newsletters"
  $newsletter_parent_pages = get_posts(
    array(
      'post_type' => 'page',
      'post_status' => array( 'publish', 'private', 'draft' ),
      'title' => 'Newsletters',
      'numberposts' => -1,
    )
  );

  if ( empty( $newsletter_parent_pages ) ) {
    // Try alternative search method using meta_query for title
    $newsletter_parent_pages = get_posts(
      array(
        'post_type' => 'page',
        'post_status' => array( 'publish', 'private', 'draft' ),
        'numberposts' => -1,
        'meta_query' => array(
          array(
            'key' => '_wp_page_template',
            'compare' => 'EXISTS'
          )
        )
      )
  );

    // Filter by title manually since get_posts title parameter can be unreliable
    $newsletter_parent_pages = array_filter( $newsletter_parent_pages, function( $page ) {
      return strtolower( trim( $page->post_title ) ) === 'newsletters';
    } );
  }

  $log( 'Found ' . count( $newsletter_parent_pages ) . ' parent "Newsletters" pages' );

  if ( empty( $newsletter_parent_pages ) ) {
    $log( 'No "Newsletters" parent pages found. Exiting.' );
    return $results;
  }

  // Process each parent page
  foreach ( $newsletter_parent_pages as $parent_page ) {
    $log( 'Processing parent page: "' . $parent_page->post_title . '" (ID: ' . $parent_page->ID . ')' );

    // Find all child pages
    $child_pages = get_posts( array(
      'post_type' => 'page',
      'post_parent' => $parent_page->ID,
      'post_status' => array( 'publish', 'private', 'draft' ),
      'numberposts' => -1,
      'orderby' => 'date',
      'order' => 'DESC'
    ) );

    $log( 'Found ' . count( $child_pages ) . ' child pages' );
    $results['found_pages'] += count( $child_pages );

    // Process each child page
    foreach ( $child_pages as $child_page ) {
      $log( 'Processing child page: "' . $child_page->post_title . '" (ID: ' . $child_page->ID . ')' );

      try {
        // Check if newsletter post already exists with same title and date
        $existing_newsletter = get_posts( array(
          'post_type' => 'newsletter',
          'title' => $child_page->post_title,
          'date_query' => array(
            array(
              'year'  => date( 'Y', strtotime( $child_page->post_date ) ),
              'month' => date( 'm', strtotime( $child_page->post_date ) ),
              'day'   => date( 'd', strtotime( $child_page->post_date ) ),
            ),
          ),
          'numberposts' => 1,
        ) );

        if ( ! empty( $existing_newsletter ) ) {
          $log( 'Newsletter already exists, skipping: ' . $child_page->post_title );
          continue;
        }

        if ( ! $dry_run ) {
          // Create new newsletter post
          $newsletter_data = array(
            'post_title'     => $child_page->post_title,
            'post_content'   => $child_page->post_content,
            'post_excerpt'   => $child_page->post_excerpt,
            'post_status'    => $child_page->post_status,
            'post_date'      => $child_page->post_date,
            'post_date_gmt'  => $child_page->post_date_gmt,
            'post_modified'  => $child_page->post_modified,
            'post_modified_gmt' => $child_page->post_modified_gmt,
            'post_type'      => 'newsletter',
            'post_author'    => $child_page->post_author,
            'menu_order'     => $child_page->menu_order,
          );

          $newsletter_id = wp_insert_post( $newsletter_data );

          if ( is_wp_error( $newsletter_id ) ) {
            $error = 'Failed to create newsletter for page "' . $child_page->post_title . '": ' . $newsletter_id->get_error_message();
            $results['errors'][] = $error;
            $log( 'ERROR: ' . $error );
            continue;
          }

          $log( 'Created newsletter post (ID: ' . $newsletter_id . ')' );

          // Copy all metadata
          $metadata = get_post_meta( $child_page->ID );
          foreach ( $metadata as $meta_key => $meta_values ) {
            // Skip WordPress internal meta that shouldn't be copied
            if ( in_array( $meta_key, array( '_wp_page_template', '_edit_lock', '_edit_last' ), true ) ) {
              continue;
            }

            foreach ( $meta_values as $meta_value ) {
              add_post_meta( $newsletter_id, $meta_key, maybe_unserialize( $meta_value ) );
            }
          }

          // Copy featured image
          $thumbnail_id = get_post_thumbnail_id( $child_page->ID );
          if ( $thumbnail_id ) {
            set_post_thumbnail( $newsletter_id, $thumbnail_id );
            $log( 'Copied featured image' );
          }

          // Copy terms (tags, categories, etc.)
          $taxonomies = get_object_taxonomies( 'page' );
          foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $child_page->ID, $taxonomy );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
              $term_ids = wp_list_pluck( $terms, 'term_id' );
              wp_set_object_terms( $newsletter_id, $term_ids, $taxonomy );
              $log( 'Copied ' . count( $term_ids ) . ' terms from taxonomy: ' . $taxonomy );
            }
          }

          // Add migration tracking meta
          add_post_meta( $newsletter_id, '_migrated_from_page_id', $child_page->ID );
          add_post_meta( $newsletter_id, '_migration_date', current_time( 'mysql' ) );

          $results['migrated']++;
          $log( 'Successfully migrated: "' . $child_page->post_title . '"' );

        } else {
          $log( 'DRY RUN: Would migrate "' . $child_page->post_title . '"' );
          $results['migrated']++;
        }

      } catch ( Exception $e ) {
        $error = 'Exception processing page "' . $child_page->post_title . '": ' . $e->getMessage();
        $results['errors'][] = $error;
        $log( 'ERROR: ' . $error );
      }
    }
  }

  $log( 'Migration complete. Found: ' . $results['found_pages'] . ', Migrated: ' . $results['migrated'] . ', Errors: ' . count( $results['errors'] ) );

  return $results;
}
