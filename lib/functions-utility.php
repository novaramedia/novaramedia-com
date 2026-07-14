<?php
/**
 * Get file contents
 *
 * If found, get file contents directly with file_get_contents. Otherwise get file via old url_get_contents fallback curl function.
 *
 * @param string $path Path to file relative to theme root.
 *
 * @return string $file File contents as a string
 */
function nm_get_file( $path ) {
  if ( function_exists( 'file_get_contents' ) ) {
    $file = file_get_contents( __DIR__ . '/..' . $path );
  } else {
    $file = url_get_contents( get_bloginfo( 'stylesheet_directory' ) . $path );
  }

  return $file;
}

/** To replace file_get_contents */
function url_get_contents( $url ) {
  if ( ! function_exists( 'curl_init' ) ) {
    return;
  }
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  $output = curl_exec( $ch );
  curl_close( $ch );
  return $output;
}

/**
 * Whether the current site is the production environment.
 *
 * Wraps WordPress's native wp_get_environment_type(), which respects the
 * WP_ENVIRONMENT_TYPE constant (set automatically by Kinsta for production and
 * staging). Use `! nm_is_production()` to gate not-yet-ready features so they
 * stay available on local/development/staging but never reach production.
 *
 * @return bool True on production, false on local/development/staging.
 */
function nm_is_production() {
  return wp_get_environment_type() === 'production';
}

/**  A is_single for custom post type */
function is_single_type( $type, $post ) {
  if ( get_post_type( $post->ID ) === $type ) {
    return true;
  } else {
    return false;
  }
}

/**
 * Cleanly print out a variable, with optional title
 *
 * @param mixed $var Any PHP variable.
 * @param string $title Optional title for variable.
 *
 * @return void
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
  function nm_pr( $var, $title = null ) {
    if ( $title ) {
      echo '<strong>' . esc_html( $title ) . '</strong><br>';
    }
    echo '<pre>';
    print_r( $var ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
    echo '</pre>';
  }

  /** Debug page and template request */
  function debug_page_request() {
    global $wp, $template;
    define( 'D4P_EOL', "\r\n" );
    echo '<!-- Request: ';
    echo empty( $wp->request ) ? 'None' : esc_html( $wp->request );
    echo ' -->' . D4P_EOL;
    echo '<!-- Matched Rewrite Rule: ';
    echo empty( $wp->matched_rule ) ? 'None' : esc_html( $wp->matched_rule );
    echo ' -->' . D4P_EOL;
    echo '<!-- Matched Rewrite Query: ';
    echo empty( $wp->matched_query ) ? 'None' : esc_html( $wp->matched_query );
    echo ' -->' . D4P_EOL;
    echo '<!-- Loaded Template: ';
    echo basename( $template );
    echo ' -->' . D4P_EOL;
  }
}
