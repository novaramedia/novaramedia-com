<?php
/** EXTERNAL REDIRECTS
 * -------------------------------------------------------------
 */

// for redirects that send users to an external URL.
// Add more path => URL pairs to the array as needed.
// Format: 'path' => 'https://example.com/redirect-url'
$nm_external_redirects = array(
  'shop' => 'https://shop.novaramedia.com',
);

add_action(
  'template_redirect',
  function () use ( $nm_external_redirects ) {
    handle_external_redirects( $nm_external_redirects );
  },
  1
);

/**
 * Allow external redirect hosts for wp_safe_redirect().
 * Derived automatically from the redirects array above.
 */
add_filter(
  'allowed_redirect_hosts',
  function ( $hosts ) use ( $nm_external_redirects ) {
    $external_hosts = array_unique(
      array_map(
        function ( $url ) {
          return wp_parse_url( $url, PHP_URL_HOST );
        },
        array_values( $nm_external_redirects )
      )
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
      'pattern'  => '^dyor/?$',
      'category' => 'do-your-own-research',
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
    array(
      'pattern'  => '^death-in-westminster/?$',
      'category' => 'death-in-westminster',
    ),
    array(
      'pattern'  => '^the-cortado/?$',
      'category' => 'the-cortado',
    ),
  );

  foreach ( $internal_rewrites as $rewrite ) {
    $cat = get_category_by_slug( $rewrite['category'] );

    // Add rewrite rule if category exists
    if ( $cat ) {
      add_rewrite_rule(
        $rewrite['pattern'],
        'index.php?cat=' . $cat->term_id,
        'top'
      );
    }
  }
}

/** NEWSLETTER → CATEGORY REDIRECTS
 * -------------------------------------------------------------
 */

// Newsletter CPT permalinks that should 301 to a category archive.
// The newsletter record stays as the source of signup metadata; the
// category archive is the canonical destination for readers.
// Format: 'newsletter-slug' => 'category-slug'
$nm_newsletter_category_redirects = array(
  'the-cortado' => 'the-cortado',
);

add_action(
  'template_redirect',
  function () use ( $nm_newsletter_category_redirects ) {
    handle_newsletter_category_redirects( $nm_newsletter_category_redirects );
  }
);

/**
 * Redirects newsletter CPT singles to their category archive.
 *
 * @param array $redirects Associative array of newsletter slug => category slug.
 * @return void Exits script execution after issuing a redirect.
 */
function handle_newsletter_category_redirects( $redirects ) {
  if ( ! is_singular( 'newsletter' ) ) {
    return;
  }

  $newsletter = get_queried_object();

  if ( ! $newsletter || empty( $newsletter->post_name ) ) {
    return;
  }

  if ( ! isset( $redirects[ $newsletter->post_name ] ) ) {
    return;
  }

  $category = get_category_by_slug( $redirects[ $newsletter->post_name ] );

  if ( ! $category ) {
    return; // Category not created yet — leave the newsletter page reachable.
  }

  $link = get_term_link( $category );

  if ( is_wp_error( $link ) ) {
    return;
  }

  wp_safe_redirect( $link, 301 );
  exit;
}
