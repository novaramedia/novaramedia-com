<?php
/**
 * Include custom post types in front-end search results.
 *
 * WP main query defaults to post_type='post' for search even when CPTs are
 * registered with exclude_from_search=false. This hook makes the intent explicit.
 * notice is excluded (registered with exclude_from_search=true; no standalone value).
 *
 * @param WP_Query $query The current query.
 */
function nm_search_include_cpts( WP_Query $query ) {
  if ( $query->is_search() && $query->is_main_query() && ! is_admin() ) {
    $existing = $query->get( 'post_type' );
    if ( empty( $existing ) ) {
      $query->set( 'post_type', array( 'post', 'contributor', 'event', 'job' ) );
    }
  }
}
add_action( 'pre_get_posts', 'nm_search_include_cpts' );

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
add_filter( 'manage_posts_columns', 'nm_filter_admin_columns' );
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
 * Wraps embed HTML in a consent gate placeholder.
 * Returns raw HTML in RSS feeds so feed readers get working embeds.
 *
 * @param string $html     The embed HTML to gate.
 * @param string $platform Human-readable platform name shown in the placeholder.
 * @return string Consent gate wrapper HTML, or raw $html in feeds.
 */
function nm_consent_gate_wrap( $html, $platform ) {
  if ( is_feed() ) {
    return $html;
  }

  $platform_esc = esc_html( $platform );
  $privacy_url  = esc_url( get_privacy_policy_url() );
  $privacy_link = $privacy_url
    ? sprintf( '<a href="%s" class="embed-consent-gate__privacy-link font-size-8">Privacy policy</a>', $privacy_url )
    : '';

  // Embed HTML is placed inside <template> — an inert element whose content is
  // browser-parsed but never rendered or executed until explicitly cloned by JS.
  // Neutralise any </template sequences so adversarial oEmbed output cannot
  // prematurely close the wrapper and execute without consent.
  $html_safe = preg_replace( '/<\/\s*template\b/i', '&lt;/template', $html );

  return sprintf(
    '<div class="embed-consent-gate">
      <template class="embed-consent-gate__template">%s</template>
      <div class="embed-consent-gate__placeholder">
        <div class="embed-consent-gate__content">
          <p class="embed-consent-gate__message font-size-9">%s content is blocked because you have not accepted cookies.</p>
          <button type="button" class="embed-consent-gate__accept ui-button ui-button--small ui-button--white">Accept cookies &amp; load %s</button>
          %s
        </div>
      </div>
    </div>',
    $html_safe,
    $platform_esc,
    $platform_esc,
    $privacy_link
  );
}

/**
 * Returns true for YouTube embeds (youtube.com/embed or youtube-nocookie.com).
 * These are exempt from the consent gate because callers swap them to the
 * privacy-enhanced youtube-nocookie.com URL before displaying.
 *
 * @param string $html Embed HTML.
 * @return bool
 */
function nm_is_embed_exempt( $html ) {
  return str_contains( $html, 'youtube-nocookie.com' ) || str_contains( $html, 'youtube.com/embed' );
}

/**
 * Maps embed HTML to a human-readable platform name.
 *
 * @param string $html Embed HTML.
 * @return string Platform name, or 'Third-party' if unrecognised.
 */
function nm_detect_embed_platform( $html ) {
  // x.com needs a domain-boundary check: str_contains('x.com') also matches box.com.
  // Match ://x.com (bare domain) and .x.com (any subdomain like www/platform).
  if ( preg_match( '/[.\\/]x\.com/', $html ) ) {
    return 'Twitter/X';
  }

  $platforms = array(
    'soundcloud.com' => 'SoundCloud',
    'twitter.com'    => 'Twitter/X',
    'vimeo.com'      => 'Vimeo',
    'spotify.com'    => 'Spotify',
    'instagram.com'  => 'Instagram',
    'facebook.com'   => 'Facebook',
    'tiktok.com'     => 'TikTok',
  );
  foreach ( $platforms as $domain => $name ) {
    if ( str_contains( $html, $domain ) ) {
      return $name;
    }
  }
  return 'Third-party';
}

/**
 * Returns true if HTML contains an iframe or script tag.
 * Used to avoid gating plain-text oEmbed responses (e.g. link cards).
 *
 * @param string $html Embed HTML.
 * @return bool
 */
function nm_html_has_iframe_or_script( $html ) {
  return str_contains( $html, '<iframe' ) || str_contains( $html, '<script' );
}

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
  if ( is_admin() ) {
    return $html;
  }

  if ( str_contains( $url, 'youtube.com/' ) || str_contains( $url, 'youtu.be/' ) ) {
    // Replace youtube.com with youtube-nocookie.com in iframe src for reduced tracking
    $html = str_replace( 'youtube.com/embed', 'youtube-nocookie.com/embed', $html );
    return '<div class="oembed-element"><div class="u-video-embed-container">' . $html . '</div></div>';
  }

  if ( str_contains( $url, 'vimeo.com/' ) ) {
    $wrapped = '<div class="oembed-element"><div class="u-video-embed-container">' . $html . '</div></div>';
    return nm_consent_gate_wrap( $wrapped, 'Vimeo' );
  }

  if ( nm_html_has_iframe_or_script( $html ) ) {
    return nm_consent_gate_wrap( $html, nm_detect_embed_platform( $html ) );
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

/**
 * Gate block editor embeds (core/embed blocks) behind the consent placeholder.
 * Runs at priority 11, after nm_add_caption_class at priority 10, so captions
 * inside embed blocks are styled before being wrapped in the consent gate template.
 * YouTube blocks get the nocookie switch applied but are not gated.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data including name and attributes.
 * @return string Modified block HTML.
 */
function nm_consent_gate_block_embeds( $block_content, $block ) {
  if ( $block['blockName'] !== 'core/embed' ) {
    return $block_content;
  }
  if ( is_admin() || empty( $block_content ) ) {
    return $block_content;
  }
  if ( nm_is_embed_exempt( $block_content ) ) {
    return str_replace( 'youtube.com/embed', 'youtube-nocookie.com/embed', $block_content );
  }

  $provider_slug = $block['attrs']['providerNameSlug'] ?? '';
  $platform_map  = array(
    'soundcloud' => 'SoundCloud',
    'twitter'    => 'Twitter/X',
    'vimeo'      => 'Vimeo',
    'spotify'    => 'Spotify',
    'instagram'  => 'Instagram',
    'facebook'   => 'Facebook',
    'tiktok'     => 'TikTok',
  );
  $platform = $platform_map[ $provider_slug ] ?? nm_detect_embed_platform( $block_content );

  return nm_consent_gate_wrap( $block_content, $platform );
}
add_filter( 'render_block', 'nm_consent_gate_block_embeds', 11, 2 );
