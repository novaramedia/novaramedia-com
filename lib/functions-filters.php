<?php

// Custom filters (like pre_get_posts etc)


// Add classes to oembed elements
function my_embed_oembed_html($html, $url, $attr, $post_id) {
  return '<div class="margin-top-basic margin-bottom-basic"><div class="u-video-embed-container">' . $html . '</div></div>';
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
function salzano_add_featured_image_to_feed( $content ) {
  global $post;

  if ( isset( $post->ID ) && has_post_thumbnail( $post->ID ) ){
    return get_the_post_thumbnail( $post->ID, apply_filters( 'rss_featured_image_thumbnail_size', 'large' ), 'data-no-lazysizes' ) . $content;
  }
  return $content;
}

add_filter( 'the_excerpt_rss', 'salzano_add_featured_image_to_feed', 1000, 1 );
add_filter( 'the_content_feed', 'salzano_add_featured_image_to_feed', 1000, 1 );