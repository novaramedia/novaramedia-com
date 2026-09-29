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
 * Usage (dry run, prints the plan):  wp eval-file scripts/one-off/2026-09-season-episode-backfill.php
 * Apply:                            wp eval-file scripts/one-off/2026-09-season-episode-backfill.php apply
 *
 * Safe to re-run: it overwrites the same keys with the same values.
 */

$nm_apply = isset( $args[0] ) && 'apply' === $args[0];

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
