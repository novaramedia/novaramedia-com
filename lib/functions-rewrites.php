<?php
/** EXTERNAL REDIRECTS
 * -------------------------------------------------------------
 */

// for redirects that send users to an external URL.
// Add more path => URL pairs to the array as needed.
// Format: 'path' => 'https://example.com/redirect-url'
add_action(
  'template_redirect',
  function () {
    handle_external_redirects(
      array(
        'shop'      => 'https://shop.novaramedia.com',
        'asksophie' => 'https://docs.google.com/forms/d/17qKQIMyYNdYEq0Uh4wcRSEhV6EGgamBaoFt_vfMlVd0/viewform',
      )
    );
  }
);

/**
 * Allow external redirect hosts for wp_safe_redirect().
 */
add_filter(
  'allowed_redirect_hosts',
  function ( $hosts ) {
    $external_hosts = array(
      'docs.google.com',
      'shop.novaramedia.com',
    );
    return array_merge( $hosts, $external_hosts );
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
function handle_external_redirects( $redirects ) {
  if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
    return;
  }

  $request_uri = trim( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/' );

  if ( isset( $redirects[ $request_uri ] ) ) {
    wp_safe_redirect( esc_url_raw( $redirects[ $request_uri ] ), 301 );
    exit;
  }
}

/** INTERNAL REDIRECTS
 * -------------------------------------------------------------
 */

add_action( 'init', 'handle_internal_rewrites' );
/**
 * Handles internal rewrite rules for category redirects.
 * Add more redirects to the array as needed.
 */
function handle_internal_rewrites() {
  $internal_rewrites = array(
    array(
      'pattern'  => '^red-flags/?$',
      'category' => 'red-flags',
    ),
    array(
      'pattern'  => '^committed/?$',
      'category' => 'committed',
    ),
    array(
      'pattern'  => '^tyskysour/?$',
      'category' => 'novara-live',
    ),
    array(
      'pattern'  => '^novara-live/?$',
      'category' => 'novara-live',
    ),
    array(
      'pattern'  => '^downstream/?$',
      'category' => 'downstream',
    ),
    array(
      'pattern'  => '^if-i-speak/?$',
      'category' => 'if-i-speak',
    ),
    array(
      'pattern'  => '^acfm/?$',
      'category' => 'acfm',
    ),
  );

  foreach ( $internal_rewrites as $rewrite ) {
    $cat = get_category_by_slug( $rewrite['category'] );

    // Add rewrite rule if category exists
    if ( $cat ) {
      add_rewrite_rule(
        $rewrite['pattern'],
        'index.php?category_name=' . $cat->slug,
        'top'
      );
    }
  }
}
