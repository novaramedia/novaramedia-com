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
 * Track the post being published so we can purge its contributor pages via the Kinsta cache filter.
 * Must run at priority 9 — before Kinsta's transition_post_status callback at priority 10.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function nm_track_post_for_contributor_purge( $new_status, $old_status, $post ) {
  if ( 'publish' !== $new_status ) {
    return;
  }
  if ( wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
    return;
  }
  nm_contributor_purge_post_id( $post->ID );
}
add_action( 'transition_post_status', 'nm_track_post_for_contributor_purge', 9, 3 );

/**
 * Static store for the post ID currently being cache-purged.
 *
 * @param int|null $set Post ID to store, or null to retrieve.
 * @return int|null
 */
function nm_contributor_purge_post_id( $set = null ) {
  static $post_id = null;
  if ( null !== $set ) {
    $post_id = $set;
  }
  return $post_id;
}

/**
 * Inject contributor page URLs into Kinsta's immediate cache purge list.
 *
 * Kinsta only purges the post itself, home, and taxonomy/date archives. It has no
 * awareness of the _cmb_contributors post meta relation, so contributor pages go stale.
 * This filter adds each linked contributor's permalink to the immediate purge batch.
 *
 * @param array $purge_request Kinsta immediate purge request (keys: 'group|*' / 'single|*', values: protocol-stripped URLs).
 * @return array
 */
function nm_purge_contributor_pages_on_post_publish( $purge_request ) {
  $post_id = nm_contributor_purge_post_id();
  if ( ! $post_id ) {
    return $purge_request;
  }

  $contributors_meta = get_post_meta( $post_id, '_cmb_contributors', true );
  if ( empty( $contributors_meta ) ) {
    return $purge_request;
  }

  foreach ( explode( ',', $contributors_meta ) as $contributor_id ) {
    $contributor_id = (int) trim( $contributor_id );
    if ( ! $contributor_id ) {
      continue;
    }
    $url = get_permalink( $contributor_id );
    if ( ! $url ) {
      continue;
    }
    // group| purges the contributor URL and all sub-paths beneath it.
    $purge_request[ 'group|contributor_' . $contributor_id ] = str_replace( array( 'http://', 'https://' ), '', $url );
    // single| purges the query string variant not covered by prefix purge.
    $purge_request[ 'single|contributor_' . $contributor_id . '_full_archive' ] = str_replace( array( 'http://', 'https://' ), '', add_query_arg( 'is_full_archive', 'true', $url ) );
  }

  return $purge_request;
}
add_filter( 'KinstaCache/purgeImmediate', 'nm_purge_contributor_pages_on_post_publish' );

/**
 * Add contributor page URLs to Cloudflare's per-post cache purge list.
 *
 * The Cloudflare plugin purges taxonomies, WP author, and the post itself but has no
 * awareness of _cmb_contributors. This filter injects the linked contributor permalinks
 * so Cloudflare serves fresh pages after a post is published or updated.
 *
 * @param array $urls   URLs already queued for Cloudflare purge.
 * @param int   $post_id Post ID triggering the purge.
 * @return array
 */
function nm_purge_contributor_pages_cloudflare( $urls, $post_id ) {
  $contributors_meta = get_post_meta( $post_id, '_cmb_contributors', true );
  if ( empty( $contributors_meta ) ) {
    return $urls;
  }

  foreach ( explode( ',', $contributors_meta ) as $contributor_id ) {
    $contributor_id = (int) trim( $contributor_id );
    if ( ! $contributor_id ) {
      continue;
    }
    $url = get_permalink( $contributor_id );
    if ( $url ) {
      $urls[] = $url;
      $urls[] = add_query_arg( 'is_full_archive', 'true', $url );
    }
  }

  return $urls;
}
add_filter( 'cloudflare_purge_by_url', 'nm_purge_contributor_pages_cloudflare', 10, 2 );

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
