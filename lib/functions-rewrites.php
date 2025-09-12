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
                'asksophie' => 'https://docs.google.com/forms/d/17qKQIMyYNdYEq0Uh4wcRSEhV6EGgamBaoFt_vfMlVd0/viewform',
                'shop'      => 'https://shop.novaramedia.com',
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
function handle_external_redirects( $redirects ) {
  if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
      return;
  }

  $request_uri = trim( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/' );

  if ( isset( $redirects[ $request_uri ] ) ) {
      wp_redirect( esc_url_raw( $redirects[ $request_uri ] ), 301 );
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
    // Red flags - uses category path lookup
    array(
      'pattern'     => '^red-flags/?$',
      'category'    => 'red-flags',
      'lookup_type' => 'slug',
    ),
    // Committed - uses category slug lookup
    array(
      'pattern'     => '^committed/?$',
      'category'    => 'committed',
      'lookup_type' => 'slug',
    ),
    // Novara Live - multiple patterns for the same category
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
