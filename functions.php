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
/*
register_nav_menus( array(
	'menu_location' => 'Location Name',
) );
*/

get_template_part( 'lib/custom-gallery' );

get_template_part( 'lib/post-types' );
get_template_part( 'lib/taxonomies' );

get_template_part( 'lib/meta-boxes' );
get_template_part( 'lib/meta/meta-boxes-page-newsletters' );
get_template_part( 'lib/meta/meta-boxes-taxonomy' );
get_template_part( 'lib/meta/meta-boxes-category-tyskysour' );
get_template_part( 'lib/meta/meta-boxes-posttype-job' );

get_template_part( 'lib/theme-options' );
get_template_part( 'lib/options-front-page' );


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

// Add custom functions

get_template_part( 'lib/renderers' );
get_template_part( 'lib/functions-misc' );
get_template_part( 'lib/functions-custom' );
get_template_part( 'lib/functions-filters' );
get_template_part( 'lib/functions-hooks' );
get_template_part( 'lib/functions-utility' );
?>
