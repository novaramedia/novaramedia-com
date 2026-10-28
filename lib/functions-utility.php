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

/**
 * Cache-busting version string for a theme asset.
 *
 * On production the theme version is used, so a release busts the cache once and the
 * asset stays cached between releases. Everywhere else — local, dev and staging — the
 * file's modification time is used instead, so a rebuild is picked up immediately
 * rather than being masked by a stale stylesheet until the next version bump.
 *
 * @param string $path          Asset path relative to the template directory, e.g. '/dist/main.css'.
 * @param string $theme_version Fallback version, used on production or if the file is missing.
 *
 * @return string Version string for wp_enqueue_*.
 */
function nm_asset_version_for_environment( $path, $theme_version ) {
  if ( nm_is_production() ) {
    return $theme_version;
  }

  // Must match the directory the assets are enqueued from in functions.php
  // (get_template_directory_uri()), or a child theme would version the parent's file
  // by the child's mtime.
  $file = get_template_directory() . $path;

  if ( ! file_exists( $file ) ) {
    return $theme_version;
  }

  return (string) filemtime( $file );
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

/**
 * Strip empty paragraphs and blank lines that editors leave by pressing Enter.
 *
 * Blank means nothing but whitespace, &nbsp; (and its numeric forms or the
 * raw U+00A0 character) and <br>. Spacing between content is the stylesheet's
 * job, so blank lines are removed anywhere, not just at the end.
 *
 * Classic content (TinyMCE saves paragraphs as plain lines, wpautop adds the
 * <p> tags on render):
 * - removes lines and <p> elements that are blank
 * - removes <br> at the end of a paragraph or of the value
 * - collapses the gaps left behind to one paragraph break
 *
 * Kept: <br> inside a paragraph with text (soft line breaks), &nbsp; within a
 * line of text, and markup such as <td>&nbsp;</td> that shares its line with
 * other tags.
 *
 * Block content: only whole empty paragraph blocks are removed. Nothing
 * inside any other block is touched, as changing a block's markup makes the
 * block editor report it as invalid.
 *
 * Safe on slashed input (wp_insert_post_data) and stable on its own output.
 *
 * @param string $value Field or post content as stored.
 *
 * @return string The value without empty paragraphs. Unchanged if it is not valid UTF-8.
 */
function nm_strip_empty_paragraphs( $value ) {
  $space = '(?:&nbsp;|&#160;|&#xA0;|\x{00A0}|<br\s*\/?>)';
  $blank = '(?:\s|' . $space . ')';

  if ( false !== strpos( $value, '<!-- wp:' ) ) {
    $stripped = preg_replace( '/<!-- wp:paragraph(?: [^>]*?)? -->\s*<p(?:\s[^>]*)?>' . $blank . '*<\/p>\s*<!-- \/wp:paragraph -->\s*/iu', '', $value );

    return null === $stripped ? $value : trim( $stripped );
  }

  $empty_p  = '<p(?:\s[^>]*)?>' . $blank . '*<\/p>';
  $patterns = array(
    // Whole lines holding nothing but blank tokens or empty paragraphs.
    '/^[ \t]*(?:(?:' . $space . '|' . $empty_p . ')[ \t]*)+(?:\r?\n|\z)/imu' => '',
    // Empty paragraphs sharing a line with other markup.
    '/' . $empty_p . '/iu'                                                   => '',
    // <br> closing a paragraph, followed by a blank line, or ending the value.
    '/(?:\s*<br\s*\/?>)+(?=\s*(?:<\/p>|\r?\n[ \t]*\r?\n|\z))/iu'             => '',
    // Gaps left behind collapse to one paragraph break, in the value's own line endings.
    '/(\r?\n)(?:[ \t]*\r?\n){2,}/u'                                          => '$1$1',
    // Blank content at either end.
    '/\A' . $blank . '+|' . $blank . '+\z/iu'                                => '',
  );

  $stripped = preg_replace( array_keys( $patterns ), array_values( $patterns ), $value );

  return null === $stripped ? $value : $stripped;
}
