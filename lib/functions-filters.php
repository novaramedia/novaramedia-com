<?php
/**
* Look for custom template in /specials folder when loading Focus archive template
*
*/
function tax_focus_specials_template_path($template) {
  if (is_tax('focus')) {
    $term = get_query_var('term');
    $new_template = locate_template(array('specials/taxonomy-focus-' . $term . '.php'));

    if ('' != $new_template) {
      return $new_template;
    }
  }

  return $template;
}
add_filter( 'template_include', 'tax_focus_specials_template_path', 99 );

// add category nicenames in body class
function nm_category_id_class($classes) {
  if (is_single()) {
    global $post;
    foreach((get_the_category($post->ID)) as $category)
      $classes[] = 'category-' . $category->category_nicename;
  }

  return $classes;
}

add_filter('body_class', 'nm_category_id_class');

// Add classes to oembed elements
function my_embed_oembed_html($html, $url, $attr, $post_id) {
  return '<div class="oembed-element"><div class="u-video-embed-container">' . $html . '</div></div>';
}
add_filter('embed_oembed_html', 'my_embed_oembed_html', 99, 4);

// Custom img attributes to be compatible with lazysize
function add_lazysize_on_srcset($attr) {

  if (!is_admin()) {

    // if image has data-no-lazysizes attribute dont add lazysizes classes
    if (isset($attr['data-no-lazysizes'])) {
      unset($attr['data-no-lazysizes']);
      return $attr;
    }

    // Add lazysize class
    $attr['class'] .= ' lazyload';

    if (isset($attr['srcset'])) {
      // Add lazysize data-srcset
      $attr['data-srcset'] = $attr['srcset'];
      // Remove default srcset
      unset($attr['srcset']);
    } else {
      // Add lazysize data-src
      $attr['data-src'] = $attr['src'];
    }

    // Set default to white blank
    $attr['src'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAABCAQAAABTNcdGAAAAC0lEQVR42mNkgAIAABIAAmXG3J8AAAAASUVORK5CYII=';

  }

  return $attr;

}
add_filter('wp_get_attachment_image_attributes', 'add_lazysize_on_srcset');

// add image to the RSS feeds
// https://wordpress.org/plugins/add-featured-image-to-rss-feed/#developers
function add_featured_image_to_feed( $content ) {
  global $post;

  if ( isset( $post->ID ) && has_post_thumbnail( $post->ID ) ){
    return get_the_post_thumbnail( $post->ID, apply_filters( 'rss_featured_image_thumbnail_size', 'large' ), 'data-no-lazysizes' ) . $content;
  }
  return $content;
}

add_filter( 'the_excerpt_rss', 'add_featured_image_to_feed', 1000, 1 );
add_filter( 'the_content_feed', 'add_featured_image_to_feed', 1000, 1 );

function feed_author($name) { // return the value of the author meta field (this is just for feed readers as author tag is not used in the theme)
  if (is_feed()) {
    global $post;
    $meta = get_post_meta($post->ID);

    if (!empty($meta['_cmb_author'])) {
      return $meta['_cmb_author'][0];
    } else {
      return 'Novara Media';
    }
  }
}
add_filter( 'the_author', 'feed_author' );
