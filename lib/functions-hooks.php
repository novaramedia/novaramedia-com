<?php
/**
 * Hook cmb2_save_options-page_fields to flush the cache when theme options are saved
 * This is necessary because the front page is cached and the cache needs to be flushed when the front page options are saved
 *
 * @param int $object_id
 * @param array $updated
 * @param CMB2 $cmb
 * @param string $object_type
 */
function nm_flush_cache_on_theme_options_save( $object_id, $updated, $cmb, $object_type ) {
  $matches = array(
      'nm_front_page_options',
      'nm_front_page_above_the_fold_featured_options',
      'nm_front_page_links_bar_options',
      'nm_front_page_highlight_section_options',
  );

  if ( in_array( $object_id, $matches ) ) {
    // flushes the whole cache. would be better to flush only the cache for the front page but that functionality doest not seem to be available
    wp_cache_flush();
  }
}
add_action( 'cmb2_save_options-page_fields', 'nm_flush_cache_on_theme_options_save', 10, 4 );
/**
 * Hook template_redirect to 301 redirect author pages to the homepage
 * Author pages are those created for WP users and thus do not relate to any real content
 */
function nm_disable_author_page() {
  global $wp_query;

  if ( is_author() ) {
      // Redirect to homepage, set status to 301 permenant redirect.
      // Function defaults to 302 temporary redirect.
      wp_redirect( get_option( 'home' ), 301 );
      exit;
  }
}
add_action( 'template_redirect', 'nm_disable_author_page' );

/**
 * Hook pre_get_posts on category archives that match via slug.
 * Changes the main query to display reverse chronological and all posts for serial podcasts
 */
function podcast_series_pre_get_posts( $query ) {
  if ( is_admin() ) {
    return;
  }

  $serial_categories = array( 'foreign-agent', 'committed' ); // Add more slugs as needed

  if ( $query->is_archive() && $query->is_category( $serial_categories ) ) {
    if ( isset( $query->query_vars['posts_per_page'] ) && $query->query_vars['posts_per_page'] === 1 ) {
      return; // Skip modification if a specific post count is requested
    }

    $query->set( 'posts_per_page', -1 ); // Show all posts
    $query->set( 'order', 'ASC' ); // Oldest first
  }
}
add_action( 'pre_get_posts', 'podcast_series_pre_get_posts' );

/**
 * Hook pre_get_posts to show all posts on Focus archive pages
 */
function focus_pre_get_posts( $query ) {
  if ( $query->is_admin() ) {
    return;
  }

  if ( $query->is_archive() && $query->is_tax( 'focus' ) ) {
    $query->set( 'posts_per_page', -1 );
  }
}
add_action( 'pre_get_posts', 'focus_pre_get_posts' );

/**
 * Save estimated reading time in minutes as meta on post.
 */
function save_reading_time_meta( $post_id, $post, $update ) {
  // Thanks to https://wordpress.org/plugins/estimated-post-reading-time/

  $words_per_minute = 265;
  $content = strip_tags( $post->post_content );
  $content_words = str_word_count( $content );
  $estimated_minutes = floor( $content_words / $words_per_minute );

  update_post_meta( $post_id, '_igv_reading_time', $estimated_minutes );
}
add_action( 'save_post', 'save_reading_time_meta', 10, 3 );

// Custom editor button to find replace all links in the_content and make target _blank

if ( is_admin() ) {
  add_action( 'init', 'extlink_setup_tinymce_plugin' );
}

/**
 * Setup TinyMCE plugin for external links.
 */
function extlink_setup_tinymce_plugin() {
  // Check if the logged in WordPress User can edit Posts or Pages
  // If not, don't register our TinyMCE plugin
  if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
    return;
  }

  // Check if the logged in WordPress User has the Visual Editor enabled
  // If not, don't register our TinyMCE plugin
  if ( get_user_option( 'rich_editing' ) !== 'true' ) {
    return;
  }

  // Setup some filters
  add_filter( 'mce_external_plugins', 'extlink_add_tinymce_plugin' );
  add_filter( 'mce_buttons', 'extlink_add_tinymce_toolbar_button' );
}

/**
 * Adds external TinyMCE plugins.
 *
 * @param array $plugin_array Array of TinyMCE plugins.
 * @return array Modified array of TinyMCE plugins.
 */
function extlink_add_tinymce_plugin( $plugin_array ) {
  $plugin_array['extlinks'] = get_template_directory_uri() . '/lib/tinyMCE/extlink-tinymce.js';
  $plugin_array['videocaptionshortcode'] = get_template_directory_uri() . '/lib/tinyMCE/videocaptionshortcode-tinymce.js';
  return $plugin_array;
}

/**
 * Adds buttons to the TinyMCE toolbar.
 *
 * @param array $buttons Array of TinyMCE toolbar buttons.
 * @return array Modified array of TinyMCE toolbar buttons.
 */
function extlink_add_tinymce_toolbar_button( $buttons ) {
  array_push( $buttons, 'extlinks' );
  array_push( $buttons, 'videocaptionshortcode' );
  return $buttons;
}

/**
 * Modify the main query to order events by meta value.
 *
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */
function order_events_by_meta( $query ) {

  if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive() ) {
    if ( $query->query['post_type'] === 'event' ) {
      $query->set( 'orderby', 'meta_value' );
      $query->set( 'meta_key', '_cmb_time' );
    }
  }
}

add_action( 'pre_get_posts', 'order_events_by_meta' );

/**
 * Get custom metadata for GTM dataLayer.
 * Returns only custom metadata that GTM4WP plugin doesn't provide:
 * - Authors array from contributor post type relation
 * - Standfirst
 * - Reading age (nm_readability_age meta value)
 *
 * GTM4WP plugin already handles: postId, postTitle, postDate, categories, tags
 *
 * @return array Array of custom metadata for dataLayer.
 */
function nm_get_custom_metadata_for_datalayer() {
  $data = array();

  // Only add custom metadata on single posts
  if ( ! is_singular( 'post' ) ) {
    return $data;
  }

  $post_id = get_queried_object_id();

  if ( ! $post_id ) {
    return $data;
  }

  // Get authors from contributors (custom post type relation)
  $authors = array();
  if ( function_exists( 'get_contributors_array' ) ) {
    $contributors = get_contributors_array( $post_id );

    if ( $contributors && is_array( $contributors ) ) {
      foreach ( $contributors as $contributor ) {
        if ( isset( $contributor->post_title ) ) {
          $authors[] = sanitize_text_field( $contributor->post_title );
        }
      }
    }
  }

  // Fallback to legacy author meta field if no contributors
  if ( empty( $authors ) ) {
    $legacy_author = get_post_meta( $post_id, '_cmb_author', true );
    if ( ! empty( $legacy_author ) ) {
      $authors[] = sanitize_text_field( $legacy_author );
    }
  }

  if ( ! empty( $authors ) ) {
    $data['authors'] = $authors;
  }

  // Get standfirst (strip all HTML for security)
  $standfirst = get_post_meta( $post_id, '_cmb_standfirst', true );
  if ( ! empty( $standfirst ) ) {
    $data['standfirst'] = sanitize_text_field( wp_strip_all_tags( $standfirst ) );
  }

  // Get reading age if set
  $reading_age = get_post_meta( $post_id, 'nm_readability_age', true );
  if ( ! empty( $reading_age ) ) {
    $data['readingAge'] = sanitize_text_field( $reading_age );
  }

  return $data;
}
