<?php
/** TODO: Refactor this to use an array rather than repeating code.
 */

// for redirects that send users to an external URL.
// Add more path => URL pairs to the array as needed.
// Format: 'path' => 'https://example.com/redirect-url'
add_action(
    'template_redirect',
    function () {
        handle_simple_redirects(
            array(
                'asksophie' => 'https://docs.google.com/forms/d/17qKQIMyYNdYEq0Uh4wcRSEhV6EGgamBaoFt_vfMlVd0/viewform',
            )
        );
    }
);
/**
 * Handles simple path-based redirects.
 * For redirects that send users to an external URL.
 *
 * You can add more redirects in the array above without duplicating logic.
 *
 * @param array $redirects Associative array of path => destination URL.
 */
function handle_simple_redirects( $redirects ) {
  if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
      return;
  }

  $request_uri = trim( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/' );

  if ( isset( $redirects[ $request_uri ] ) ) {
      wp_redirect( esc_url_raw( $redirects[ $request_uri ] ), 301 );
      exit;
  }
}

add_action( 'init', 'red_flag_rewrites' );
/**
 * Redirects /red-flags to /category/articles/red-flags/
 * Internal rewrite rule.
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
