<?php
/**
 * Page-cache purging the Kinsta and Cloudflare plugins do not do themselves.
 *
 * Two kinds, in this order below:
 *  - full purges for changes WordPress core does not tie to a post (options
 *    pages, menus, terms, contributor renames, Mailchimp keys)
 *  - per-post additions: contributor pages of a changed post, and permanent
 *    deletes
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
 * @param string $method Method the caller is about to call on the object.
 * @return object|null
 */
function nm_get_kinsta_cache_purge( $method = 'purge_complete_caches' ) {
  global $kinsta_muplugin;

  if ( empty( $kinsta_muplugin->kinsta_cache_purge ) || ! is_callable( array( $kinsta_muplugin->kinsta_cache_purge, $method ) ) ) {
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
 * Runs on publish (tracked above) and permanent delete (tracked below).
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
 * so Cloudflare serves fresh pages after a post is published, updated, or deleted.
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
 * Purge Kinsta page cache when a post is permanently deleted.
 *
 * Kinsta's mu-plugin purges on publish, update, and trash, but registers no hook
 * for permanent deletion, so a force-deleted post stays cached until its TTL
 * expires. before_delete_post fires while the post row still exists, so the
 * permalink can still be resolved. Posts deleted from the trash carry the
 * __trashed permalink suffix — their live URL was already purged at trash time.
 *
 * Also stores the post ID for the KinstaCache/purgeImmediate filter above, so
 * linked contributor pages are purged on delete as they are on publish.
 *
 * @param int     $post_id Post ID being deleted.
 * @param WP_Post $post    Post object.
 */
function nm_purge_kinsta_cache_on_post_delete( $post_id, $post ) {
  if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
    return;
  }

  $kinsta = nm_get_kinsta_cache_purge( 'initiate_purge' );
  if ( ! $kinsta ) {
    return;
  }

  nm_contributor_purge_post_id( $post_id );
  $kinsta->initiate_purge( $post_id );
}
add_action( 'before_delete_post', 'nm_purge_kinsta_cache_on_post_delete', 10, 2 );

/**
 * Run the Cloudflare plugin's per-post URL purge while the post row still exists.
 *
 * The plugin hooks deleted_post, which fires after WP core removes the row; its
 * handler bails when get_post() returns null, so permanent deletes never reach
 * Cloudflare (trashing is unaffected — that purge fires while the row exists).
 * Registering the same handler on before_delete_post lets it resolve the post and
 * its related URLs. The handler itself skips revisions, autosaves, and non-viewable
 * post types; its later deleted_post run still bails harmlessly.
 *
 * The plugin's own cloudflare_purge_url_actions filter cannot be used for this: the
 * plugin applies it while plugins load, before the theme's functions.php runs, so a
 * theme filter is never seen. Instead the handler instance is taken from the plugin's
 * existing deleted_post registration (nm_get_cloudflare_hooks()) — the instance it
 * configured itself — and added to before_delete_post. Does nothing if the plugin is
 * inactive or changes its hooks.
 */
function nm_cloudflare_purge_before_delete() {
  $cloudflare = nm_get_cloudflare_hooks();

  if ( $cloudflare ) {
    add_action( 'before_delete_post', array( $cloudflare, 'purgeCacheByRelevantURLs' ), PHP_INT_MAX );
  }
}
add_action( 'init', 'nm_cloudflare_purge_before_delete' );
