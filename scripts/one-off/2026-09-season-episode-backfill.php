<?php
/**
 * One-off: backfill season/episode meta on existing episodic posts.
 *
 * Capsule podcasts (Committed, Foreign Agent, Death in Westminster) put their
 * episode label in the standfirst. "Episode N" becomes _nm_episode = N; any
 * other standfirst ("Bonus 1", "Bonus 2") becomes _nm_episode_label. Every
 * post gets _nm_season = 1. Standfirsts are NOT modified — editorial rewrites
 * them after deploy (docs/post-deploy-checklist.md).
 *
 * Do Your Own Research season 1: episode numbers follow publish order.
 *
 * Death in Westminster's dates were set in reverse so the default newest-
 * first archive showed Episode 1 at the top. It now lists oldest first like
 * the other serial podcasts, so the "redate" mode reassigns its existing
 * publish dates in episode order (Episode 1 gets the earliest). Run it right
 * after the deploy; until then the archive shows 6 → 1.
 *
 * Usage (dry run, prints the plan):  wp eval-file scripts/one-off/2026-09-season-episode-backfill.php
 * Apply:                            wp eval-file scripts/one-off/2026-09-season-episode-backfill.php apply
 * Redate dry run / apply:           wp eval-file scripts/one-off/2026-09-season-episode-backfill.php redate [apply]
 *
 * Safe to re-run: meta mode overwrites the same keys with the same values;
 * redate mode sorts the same set of dates the same way.
 */

$nm_redate = isset( $args[0] ) && 'redate' === $args[0];
$nm_apply  = in_array( 'apply', $args, true );

if ( $nm_redate ) {
  $nm_posts = get_posts( array(
    'category_name'  => 'death-in-westminster',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
    'meta_key'       => '_nm_episode', // Only numbered episodes; run the meta backfill first.
  ) );

  $nm_dates = wp_list_pluck( $nm_posts, 'post_date' ); // Already ascending.

  usort( $nm_posts, function ( $a, $b ) {
    return (int) get_post_meta( $a->ID, '_nm_episode', true ) <=> (int) get_post_meta( $b->ID, '_nm_episode', true );
  } );

  foreach ( $nm_posts as $index => $post ) {
    WP_CLI::log( sprintf( '  #%d  episode %d  %s -> %s', $post->ID, (int) get_post_meta( $post->ID, '_nm_episode', true ), $post->post_date, $nm_dates[ $index ] ) );

    if ( $nm_apply && $post->post_date !== $nm_dates[ $index ] ) {
      wp_update_post( array(
        'ID'            => $post->ID,
        'post_date'     => $nm_dates[ $index ],
        'post_date_gmt' => get_gmt_from_date( $nm_dates[ $index ] ),
        'edit_date'     => true,
      ) );
    }
  }

  WP_CLI::success( $nm_apply ? 'Redated.' : 'Redate dry run only. Re-run with "redate apply" to write.' );

  return;
}

$nm_capsule_slugs = array( 'committed', 'foreign-agent', 'death-in-westminster' );
$nm_dyor_slug     = 'do-your-own-research';

$nm_get_posts_in_date_order = function ( $slug ) {
  return get_posts( array(
    'category_name'  => $slug,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
  ) );
};

$nm_write = function ( $post_id, $season, $episode, $label ) use ( $nm_apply ) {
  WP_CLI::log( sprintf( '  #%d  season=%d  episode=%s  label=%s', $post_id, $season, $episode ? $episode : '-', '' !== $label ? $label : '-' ) );

  if ( ! $nm_apply ) {
    return;
  }

  update_post_meta( $post_id, '_nm_season', (string) $season );

  if ( $episode ) {
    update_post_meta( $post_id, '_nm_episode', (string) $episode );
    delete_post_meta( $post_id, '_nm_episode_label' );
  } else {
    delete_post_meta( $post_id, '_nm_episode' );
    update_post_meta( $post_id, '_nm_episode_label', $label );
  }
};

foreach ( $nm_capsule_slugs as $slug ) {
  WP_CLI::log( $slug );

  foreach ( $nm_get_posts_in_date_order( $slug ) as $post ) {
    $standfirst = trim( wp_strip_all_tags( (string) get_post_meta( $post->ID, '_cmb_standfirst', true ) ) );

    if ( preg_match( '/^episode\s+(\d+)$/i', $standfirst, $matches ) ) {
      $nm_write( $post->ID, 1, (int) $matches[1], '' );
    } elseif ( '' !== $standfirst ) {
      $nm_write( $post->ID, 1, 0, $standfirst );
    } else {
      WP_CLI::warning( sprintf( '#%d %s has no standfirst — skipped, set by hand', $post->ID, $post->post_name ) );
    }
  }
}

WP_CLI::log( $nm_dyor_slug . ' (season 1, publish order — check for non-episode posts)' );

foreach ( $nm_get_posts_in_date_order( $nm_dyor_slug ) as $index => $post ) {
  $nm_write( $post->ID, 1, $index + 1, '' );
  WP_CLI::log( '      ' . $post->post_title );
}

WP_CLI::success( $nm_apply ? 'Applied.' : 'Dry run only. Re-run with "apply" to write.' );
