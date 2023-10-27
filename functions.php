<?php
// Enqueues the compiled main.js file and site.css. main.js is registered with a global WP object parsing some Wordpress variables
function scripts_and_styles_method() {
  $site_js = get_template_directory_uri() . '/dist/main.js';

  $current_theme = wp_get_theme();
  $theme_version = $current_theme->get('Version');

  wp_register_script( 'site-js', $site_js, array(), $theme_version );

  $global_javascript_variables = array(
  	'siteUrl' => home_url(),
  	'themeUrl' => get_template_directory_uri(),
  	'isAdmin' => current_user_can('administrator') ? 1 : 0,
  	'supportSectionAutovalues' => nm_get_support_autovalues()
  );

  wp_localize_script( 'site-js', 'WP', $global_javascript_variables );

  wp_enqueue_script( 'site-js', $site_js, array(), $theme_version, true );

  wp_enqueue_style( 'site', get_stylesheet_directory_uri() . '/dist/main.css', null, $theme_version );

  if (is_admin()) {
    wp_enqueue_style( 'dashicons' );
  }
}
add_action('wp_enqueue_scripts', 'scripts_and_styles_method');

// Theme supports

if( function_exists( 'add_theme_support' ) ) {
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
}

// Set max width of main content area for oembed etc

if ( !isset( $content_width ) ) {
	$content_width = 644;
}

// Declare thumbnail sizes

get_template_part( 'lib/thumbnail-sizes' );

// Register Nav Menus
function nm_register_menus() {
  register_nav_menus(
    array(
      'footer-podcasts' => __( 'Footer: Podcasts' ),
      'footer-focuses' => __( 'Footer: Focuses' ),
      'footer-articles' => __( 'Footer: Articles' ),
      'footer-shows' => __( 'Footer: Shows' ),
      'footer-social-media' => __( 'Footer: Social Media' ),
      'articles-archive-menu' => __( 'Articles archive' ),
      'audio-archive-menu' => __( 'Audio archive' ),
      'video-archive-menu' => __( 'Video archive' ),
     )
   );
 }
add_action( 'init', 'nm_register_menus' );

get_template_part( 'lib/custom-gallery' );

get_template_part( 'lib/post-types' );
get_template_part( 'lib/taxonomies' );

get_template_part( 'lib/meta/meta-boxes-instructions' );
get_template_part( 'lib/meta/meta-boxes' );
get_template_part( 'lib/meta/cmb2-validation' );
get_template_part( 'lib/meta/meta-boxes-post' );
get_template_part( 'lib/meta/meta-boxes-page' );
get_template_part( 'lib/meta/meta-boxes-page-about' );
get_template_part( 'lib/meta/meta-boxes-page-support' );
get_template_part( 'lib/meta/meta-boxes-page-newsletters' );
get_template_part( 'lib/meta/meta-boxes-text-copy-page-template' );
get_template_part( 'lib/meta/meta-boxes-taxonomy' );
get_template_part( 'lib/meta/meta-boxes-category-novara-live' );
get_template_part( 'lib/meta/meta-boxes-category-tyskysour' );
get_template_part( 'lib/meta/meta-boxes-posttype-job' );
get_template_part( 'lib/meta/meta-boxes-posttype-contributor' );

get_template_part( 'lib/theme-options/theme-options' );
get_template_part( 'lib/theme-options/options-front-page' );
get_template_part( 'lib/theme-options/options-fundraising' );

function cmb_initialize_cmb_meta_boxes() {
  if (!class_exists( 'cmb2_bootstrap_202' ) ) {
    require_once 'vendor/cmb2/cmb2/init.php';
    require_once 'vendor/webdevstudios/cmb2-post-search-field/lib/init.php';
  }
}
add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 11 );

function composer_autoload() {
  require_once( 'vendor/autoload.php' );
}
add_action( 'init', 'composer_autoload', 10 );

// Register shortcodes

function nm_register_shortcodes() {
  function nm_caption_shortcode( $atts, $content = null) {
    return '<div class="video-caption wp-caption">' . $content . '</div>';
  }
  add_shortcode( 'video-caption', 'nm_caption_shortcode' );
}
add_action( 'init', 'nm_register_shortcodes' );

// Add custom functions

get_template_part( 'lib/renderers' );
get_template_part( 'lib/functions-rewrites' );
get_template_part( 'lib/functions-misc' );
get_template_part( 'lib/functions-custom' );
get_template_part( 'lib/functions-filters' );
get_template_part( 'lib/functions-hooks' );
get_template_part( 'lib/functions-utility' );
?>
