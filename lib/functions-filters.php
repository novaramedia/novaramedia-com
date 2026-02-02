<?php
/**
 * Add classes to pagination links
 *
 * @param string $attributes Existing link attributes.
 * @return string The modified string of attributes.
 */
function pagination_posts_link_attributes( $attributes ) {
  $attributes .= ' class="ui-action-link"';

  return $attributes;
}
add_filter( 'previous_posts_link_attributes', 'pagination_posts_link_attributes' );
add_filter( 'next_posts_link_attributes', 'pagination_posts_link_attributes' );

/**
 * Filters the admin columns.
 *
 * This function is responsible for modifying the columns displayed in the admin area. It removes the author and comments columns.
 *
 * @param array $columns An array of column names.
 * @return array The modified array of column names.
 */
function nm_filter_admin_columns( $columns ) {
  unset( $columns['author'] );
  unset( $columns['comments'] );

  return $columns;
}
add_filter( 'manage_pages_columns', 'nm_filter_admin_columns' );

/**
 * Change the return values when oembedding one of our articles.
 * Main reason here is to edit the author value to not be the WP editor
 */
function nm_edit_oembed_response_data( $data ) {
    /**
    * This could get the true author/contrib and link correctly.
    * But this is an edge case only seen on Discord, so for now this fixes the error.
    */
    unset( $data['author_url'] );
    $data['author_name'] = 'Novara Media';

    return $data;
}
add_filter( 'oembed_response_data', 'nm_edit_oembed_response_data' );

/**
 * Register custom query variables for custom PHP logic
 */
function nm_query_vars( $vars ) {
  $vars[] = 'is_full_archive';
  return $vars;
}
add_filter( 'query_vars', 'nm_query_vars' );

/**
 * Look for custom template in /specials folder when loading Focus archive template
 */
function tax_focus_specials_template_path( $template ) {
  if ( is_tax( 'focus' ) ) {
    $term = get_query_var( 'term' );
    $new_template = locate_template( array( 'specials/taxonomy-focus-' . $term . '.php' ) );

    if ( $new_template !== '' ) {
      return $new_template;
    }
  }

  return $template;
}
add_filter( 'template_include', 'tax_focus_specials_template_path', 99 );

/**
 * Add category nicenames to body classes for single posts.
 *
 * Loops through all categories assigned to the current post and adds
 * a 'category-{nicename}' class for each to the body_class array.
 *
 * @param array $classes Array of existing body classes.
 * @return array Modified array of body classes with category nicenames added.
 */
function nm_category_id_class( $classes ) {
  if ( is_single() ) {
    global $post;
    foreach ( ( get_the_category( $post->ID ) ) as $category ) {
      $classes[] = 'category-' . $category->category_nicename;
    }
  }

  return $classes;
}

add_filter( 'body_class', 'nm_category_id_class' );

/**
 * Add wrapper classes to oEmbed elements and use privacy-enhanced YouTube embeds.
 *
 * YouTube oEmbed returns iframes with youtube.com/embed URLs regardless of whether
 * the original URL was youtube.com or youtu.be. The str_replace works for both
 * because it operates on the returned iframe HTML, not the original URL.
 *
 * @param string $html    The oEmbed HTML.
 * @param string $url     The original URL that was embedded.
 * @param array  $attr    Embed attributes.
 * @param int    $post_id The post ID.
 * @return string Modified HTML with wrapper classes and privacy-enhanced URLs.
 */
function nm_embed_oembed_html( $html, $url, $attr, $post_id ) {
  if ( str_contains( $url, 'youtube.com/' ) || str_contains( $url, 'youtu.be/' ) ) {
    // Replace youtube.com with youtube-nocookie.com in iframe src for reduced tracking
    $html = str_replace( 'youtube.com/embed', 'youtube-nocookie.com/embed', $html );
    return '<div class="oembed-element"><div class="u-video-embed-container">' . $html . '</div></div>';
  }

  if ( str_contains( $url, 'vimeo.com/' ) ) {
    return '<div class="oembed-element"><div class="u-video-embed-container">' . $html . '</div></div>';
  }

  return $html;
}
add_filter( 'embed_oembed_html', 'nm_embed_oembed_html', 99, 4 );

/**
 * Modify image attributes to enable lazy loading via lazysizes library.
 *
 * Converts standard src/srcset attributes to data-src/data-srcset for lazy loading,
 * adds the 'lazyload' class, and sets a placeholder image. Images with the
 * data-no-lazysizes attribute are excluded from this transformation.
 *
 * @param array $attr Array of image attributes.
 * @return array Modified array of image attributes with lazysizes data attributes.
 */
function add_lazysize_on_srcset( $attr ) {

  if ( ! is_admin() ) {

    // if image has data-no-lazysizes attribute dont add lazysizes classes
    if ( isset( $attr['data-no-lazysizes'] ) ) {
      unset( $attr['data-no-lazysizes'] );
      return $attr;
    }

    // Add lazysize class
    $attr['class'] .= ' lazyload';

    if ( isset( $attr['srcset'] ) ) {
      // Add lazysize data-srcset
      $attr['data-srcset'] = $attr['srcset'];
      // Remove default srcset
      unset( $attr['srcset'] );
    } else {
      // Add lazysize data-src
      $attr['data-src'] = $attr['src'];
    }

    // Set default to white blank
    $attr['src'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAABCAQAAABTNcdGAAAAC0lEQVR42mNkgAIAABIAAmXG3J8AAAAASUVORK5CYII=';

  }

  return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'add_lazysize_on_srcset' );

/**
 * Add featured image to RSS feed content.
 *
 * Prepends the post's featured image (if it exists) to the RSS feed content.
 * The image uses the 'large' thumbnail size and includes the data-no-lazysizes
 * attribute to prevent lazy loading in feed readers.
 *
 * @see https://wordpress.org/plugins/add-featured-image-to-rss-feed/#developers
 *
 * @param string $content The RSS feed content.
 * @return string Content with featured image prepended if available.
 */
function add_featured_image_to_feed( $content ) {
  global $post;

  if ( isset( $post->ID ) && has_post_thumbnail( $post->ID ) ) {
    return get_the_post_thumbnail( $post->ID, apply_filters( 'rss_featured_image_thumbnail_size', 'large' ), 'data-no-lazysizes' ) . $content;
  }
  return $content;
}

add_filter( 'the_excerpt_rss', 'add_featured_image_to_feed', 1000, 1 );
add_filter( 'the_content_feed', 'add_featured_image_to_feed', 1000, 1 );

/**
 * Override author name in RSS feeds with contributor data
 *
 * Uses nm_get_post_authors() to fetch author names from contributor posts
 * or legacy _cmb_author meta field, with fallback to 'Novara Media'.
 *
 * @since 4.3.0
 *
 * @param string $name Default author name.
 * @return string Author name for feed.
 */
function feed_author( $name ) {
  if ( is_feed() ) {
    $author = nm_get_post_authors( get_the_ID(), 'text' );
    return $author !== false ? $author : 'Novara Media';
  }
  return $name;
}
add_filter( 'the_author', 'feed_author' );

/**
 * Add font-size-8 class to all WordPress caption elements.
 * Ensures consistent styling for captions across all media blocks.
 *
 * @param string $block_content The block content.
 * @param array  $block         The full block, including name and attributes.
 * @return string Modified block content with font-size-8 class added to captions.
 */
function nm_add_caption_class( $block_content, $block ) {
  // Only process if block content is not empty
  if ( empty( $block_content ) ) {
    return $block_content;
  }

  // Add font-size-8 to wp-element-caption (modern WordPress captions)
  // Check font-size-8 doesn't already exist anywhere in content to avoid duplicates
  if ( strpos( $block_content, 'wp-element-caption' ) !== false
       && strpos( $block_content, 'font-size-8' ) === false ) {
    // Handle both single and double quotes, preserve quote style with backreference
    $block_content = preg_replace(
      '/class=(["\'])([^"\']*?)wp-element-caption([^"\']*?)\1/i',
      'class=$1$2wp-element-caption font-size-8$3$1',
      $block_content
    );
  }

  // Also handle legacy wp-caption-text if present
  if ( strpos( $block_content, 'wp-caption-text' ) !== false
       && strpos( $block_content, 'font-size-8' ) === false ) {
    $block_content = preg_replace(
      '/class=(["\'])([^"\']*?)wp-caption-text([^"\']*?)\1/i',
      'class=$1$2wp-caption-text font-size-8$3$1',
      $block_content
    );
  }

  return $block_content;
}
add_filter( 'render_block', 'nm_add_caption_class', 10, 2 );
