<?php
/** TODO: Refactor this to use an array rather than repeating code.
 */

add_action( 'template_redirect', 'ask_sophie_rewrites' );
/**
 * Redirects novaramedia.com/asksophie to the Google Form.
 */
function ask_sophie_rewrites() {
  if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
      return;
  }
  $request_uri = trim( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/' );
  $redirects = array(
      'asksophie' => 'https://docs.google.com/forms/d/17qKQIMyYNdYEq0Uh4wcRSEhV6EGgamBaoFt_vfMlVd0/viewform',
  );
  if ( isset( $redirects[ $request_uri ] ) ) {
      wp_redirect( esc_url_raw( $redirects[ $request_uri ] ), 301 );
      exit;
  }
}

add_action( 'init', 'red_flag_rewrites' );
/**
 * Redirects /red-flags to /category/articles/red-flags/
 */
function red_flag_rewrites() {
  $cat = get_category_by_path( 'articles/red-flags' );
  if ( $cat ) {
    add_rewrite_rule(
        '^red-flags/?$',
        'index.php?category_name=articles/' . $cat->slug,
        'top'
    );
  }
}
add_action( 'init', 'committed_rewrites' );
/**
 * Redirects /committed to /category/audio/committed.
 */
function committed_rewrites() {
  $cat = get_category_by_slug( 'committed' );
  if ( $cat ) {
    add_rewrite_rule(
        '^committed/?$',
        'index.php?category_name=' . $cat->slug,
        'top'
    );
  }
}

add_action( 'init', 'novara_live_rewrites' );
/**
 * Adds rewrite rules for Novara Live category.
 */
function novara_live_rewrites() {
  $novara_live_cat = get_category_by_slug( 'novara-live' );

  if ( $novara_live_cat ) {
    add_rewrite_rule(
        'tyskysour',
        'index.php?category_name=' . $novara_live_cat->slug,
        'top'
    );

    add_rewrite_rule(
        'novara-live',
        'index.php?category_name=' . $novara_live_cat->slug,
        'top'
    );
  }
}
