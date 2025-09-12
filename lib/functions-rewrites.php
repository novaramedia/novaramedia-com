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
      'pattern'     => '^red-flags/?$',
      'category'    => 'red-flags',
      'lookup_type' => 'slug',
    ),
    array(
      'pattern'     => '^committed/?$',
      'category'    => 'committed',
      'lookup_type' => 'slug',
    ),
    array(
      'pattern'     => '^tyskysour/?$',
      'category'    => 'novara-live',
      'lookup_type' => 'slug',
    ),
    array(
      'pattern'     => '^novara-live/?$',
      'category'    => 'novara-live',
      'lookup_type' => 'slug',
    ),
  );

  foreach ( $internal_rewrites as $rewrite ) {
    $cat = null;

    // Get category based on lookup type
    if ( $rewrite['lookup_type'] === 'path' ) {
      $cat = get_category_by_path( $rewrite['category'] );
    } elseif ( $rewrite['lookup_type'] === 'slug' ) {
      $cat = get_category_by_slug( $rewrite['category'] );
    }

    // Add rewrite rule if category exists
    if ( $cat ) {
      $category_name = ( $rewrite['lookup_type'] === 'path' )
        ? 'articles/' . $cat->slug
        : $cat->slug;

      add_rewrite_rule(
        $rewrite['pattern'],
        'index.php?category_name=' . $category_name,
        'top'
      );
    }
  }
}
