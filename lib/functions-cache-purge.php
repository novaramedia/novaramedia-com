<?php
/**
 * Full page-cache purges for changes WordPress core does not tie to a post.
 *
 * Logged-out readers are served from three page caches: Kinsta's full-page
 * cache, Kinsta's edge, and Cloudflare APO (via the Cloudflare plugin). Both
 * plugins purge on post publish/update/trash; neither knows about the theme's
 * CMB2 options pages (front page, fundraising, products, IGV), whose values
 * render on the homepage or on every page. wp_cache_flush() alone only clears
 * the object cache, so options saves used to leave every page cache stale.
 *
 * Purges here are full, not per-URL: neither plugin exposes a public API for
 * purging an arbitrary URL. Each purge is recorded in the nm_cache_purge_log
 * option so its frequency can be checked against cache hit-ratio analytics.
 */

/**
 * Option holding the rolling log of full purges.
 */
const NM_CACHE_PURGE_LOG_OPTION = 'nm_cache_purge_log';

/**
 * Number of purges kept in the log; older entries are dropped.
 */
const NM_CACHE_PURGE_LOG_LIMIT = 200;

/**
 * Return the Kinsta mu-plugin's cache purge object, or null when unavailable.
 *
 * Kinsta auto-updates its mu-plugin; callers bail rather than fatal if this API
 * ever moves. Null locally and anywhere outside Kinsta.
 *
 * @return object|null
 */
function nm_get_kinsta_cache_purge() {
  global $kinsta_muplugin;

  if ( empty( $kinsta_muplugin->kinsta_cache_purge ) || ! is_callable( array( $kinsta_muplugin->kinsta_cache_purge, 'purge_complete_caches' ) ) ) {
    return null;
  }

  return $kinsta_muplugin->kinsta_cache_purge;
}

/**
 * Return the Cloudflare plugin's Hooks instance, or null when unavailable.
 *
 * The plugin keeps no global reference to the instance it configured, so it is
 * taken from the plugin's own deleted_post registration. Null if the plugin is
 * inactive or changes its hooks.
 *
 * @return \Cloudflare\APO\WordPress\Hooks|null
 */
function nm_get_cloudflare_hooks() {
  global $wp_filter;

  if ( empty( $wp_filter['deleted_post'] ) ) {
    return null;
  }

  foreach ( $wp_filter['deleted_post']->callbacks as $callbacks ) {
    foreach ( $callbacks as $callback ) {
      $function = $callback['function'];

      if ( is_array( $function ) && $function[0] instanceof \Cloudflare\APO\WordPress\Hooks && $function[1] === 'purgeCacheByRelevantURLs' ) {
        return $function[0];
      }
    }
  }

  return null;
}

/**
 * Purge the Cloudflare zone cache (APO included), once per request.
 *
 * @param string $trigger What caused the purge, recorded in the purge log.
 * @return bool True if a purge was sent this call.
 */
function nm_purge_cloudflare_cache( $trigger ) {
  static $purged = false;

  if ( $purged ) {
    return false;
  }

  $cloudflare = nm_get_cloudflare_hooks();
  if ( ! $cloudflare || ! is_callable( array( $cloudflare, 'purgeCacheEverything' ) ) ) {
    return false;
  }

  $cloudflare->purgeCacheEverything();
  $purged = true;
  nm_log_cache_purge( 'cloudflare', $trigger );

  return true;
}

/**
 * Purge every page cache layer: Kinsta (page, edge and object cache) and Cloudflare.
 *
 * Falls back to an object cache flush where Kinsta is unavailable (local).
 * Runs at most once per request.
 *
 * @param string $trigger What caused the purge, recorded in the purge log.
 * @return bool True if a purge ran this call.
 */
function nm_purge_all_page_caches( $trigger ) {
  static $purged = false;

  if ( $purged ) {
    return false;
  }
  $purged = true;

  $kinsta = nm_get_kinsta_cache_purge();
  if ( $kinsta ) {
    $kinsta->purge_complete_caches();
    nm_log_cache_purge( 'kinsta', $trigger );
  } else {
    wp_cache_flush();
  }

  nm_purge_cloudflare_cache( $trigger );

  return true;
}

/**
 * Append a purge to the rolling log, newest last.
 *
 * Read it with: wp option get nm_cache_purge_log --format=json
 *
 * @param string $layer   Cache layer purged: 'kinsta' or 'cloudflare'.
 * @param string $trigger What caused the purge.
 */
function nm_log_cache_purge( $layer, $trigger ) {
  $log = get_option( NM_CACHE_PURGE_LOG_OPTION, array() );
  if ( ! is_array( $log ) ) {
    $log = array();
  }

  $log[] = array(
    'time'    => time(),
    'layer'   => $layer,
    'trigger' => $trigger,
    'user'    => get_current_user_id(),
  );

  if ( count( $log ) > NM_CACHE_PURGE_LOG_LIMIT ) {
    $log = array_slice( $log, -NM_CACHE_PURGE_LOG_LIMIT );
  }

  // Not autoloaded: only read when checking purge frequency.
  update_option( NM_CACHE_PURGE_LOG_OPTION, $log, false );
}

/**
 * Hook cmb2_save_options-page_fields to purge all page caches when any CMB2
 * options page is saved.
 *
 * Every options page on the site sets values that render on cached pages —
 * front page sections on the homepage, fundraising copy and IGV settings
 * (cookie notice, SEO tags) on every page — so all are purged rather than
 * keeping an allowlist that drifts as pages are added. Runs on every save,
 * changed or not, so saving a page also works as a manual refresh.
 *
 * @param string $object_id Option key of the saved options page.
 */
function nm_purge_caches_on_options_save( $object_id ) {
  nm_purge_all_page_caches( 'options:' . $object_id );
}
add_action( 'cmb2_save_options-page_fields', 'nm_purge_caches_on_options_save', 10, 1 );

/**
 * Purge Cloudflare when a menu or term changes.
 *
 * Kinsta's mu-plugin already runs a full purge on these hooks; the Cloudflare
 * plugin does not, so APO kept serving old menus and term archives.
 *
 * @param int $id Menu or term ID.
 */
function nm_purge_cloudflare_on_menu_or_term_change( $id ) {
  nm_purge_cloudflare_cache( current_action() . ':' . $id );
}
add_action( 'wp_update_nav_menu', 'nm_purge_cloudflare_on_menu_or_term_change', 10, 1 );
add_action( 'edited_term', 'nm_purge_cloudflare_on_menu_or_term_change', 10, 1 );
add_action( 'delete_term', 'nm_purge_cloudflare_on_menu_or_term_change', 10, 1 );

/**
 * Hook post_updated to purge all page caches when a published contributor's
 * name or slug changes.
 *
 * The name and permalink are printed in bylines on every post by that
 * contributor, which no per-post purge reaches. Other contributor edits (bio,
 * image) only change their own page, which the plugins already purge.
 *
 * @param int     $post_id     Post ID.
 * @param WP_Post $post_after  Post object after the update.
 * @param WP_Post $post_before Post object before the update.
 */
function nm_purge_caches_on_contributor_rename( $post_id, $post_after, $post_before ) {
  if ( $post_after->post_type !== 'contributor' || $post_after->post_status !== 'publish' ) {
    return;
  }

  if ( $post_after->post_title === $post_before->post_title && $post_after->post_name === $post_before->post_name ) {
    return;
  }

  nm_purge_all_page_caches( 'contributor_rename:' . $post_id );
}
add_action( 'post_updated', 'nm_purge_caches_on_contributor_rename', 10, 3 );

/**
 * Hook post meta writes to purge all page caches when a Mailchimp key changes.
 *
 * The key is printed into signup forms on newsletter pages, category archives,
 * the front page and newsletter signup blocks inside posts, so a stale copy
 * keeps sending signups to the old list. WordPress fires these hooks only when
 * the stored value actually changes, so saving a post with the same key does
 * not purge.
 *
 * @param int|int[] $meta_ids  Meta ID(s).
 * @param int       $object_id Post ID.
 * @param string    $meta_key  Meta key.
 */
function nm_purge_caches_on_mailchimp_key_change( $meta_ids, $object_id, $meta_key ) {
  if ( $meta_key !== '_nm_mailchimp_key' ) {
    return;
  }

  nm_purge_all_page_caches( 'mailchimp_key:' . $object_id );
}
add_action( 'added_post_meta', 'nm_purge_caches_on_mailchimp_key_change', 10, 3 );
add_action( 'updated_post_meta', 'nm_purge_caches_on_mailchimp_key_change', 10, 3 );
add_action( 'deleted_post_meta', 'nm_purge_caches_on_mailchimp_key_change', 10, 3 );
