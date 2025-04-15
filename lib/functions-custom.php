<?php
/**
 * Redirects /committed to /category/committed for SEO purposes.
 *
 * @return void
 */
function redirect_committed_custom_url() {
  if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        $request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
    if ( trim( $request_uri, '/' ) === 'committed' ) {
            wp_redirect( home_url( 'category/audio/committed/' ), 301 );
            exit;
    }
  }
}
add_action( 'template_redirect', 'redirect_committed_custom_url' );
/**
 * Redirects single posts in a serial podcast category to the category archive with an anchor.
 *
 * @return void Exits script execution after issuing a redirect.
 */
function nm_serial_podcast_redirect() {
  if ( ! is_singular( 'post' ) ) {
    return;
  }
  global $post;
  // Slugs of serial podcasts you want this redirect behavior for:
  $serial_slugs = array( 'foreign-agent', 'committed' );
  $categories = get_the_category( $post->ID );
    $match = array_filter(
        $categories,
        function ( $cat ) use ( $serial_slugs ) {
          return in_array( $cat->slug, $serial_slugs );
        }
    );
  if ( ! empty( $match ) ) {
    $matched_category = array_values( $match )[0];
    $link = get_term_link( $matched_category );
    if ( $link && isset( $post->post_name ) ) {
      wp_redirect( $link . '#' . $post->post_name, 301 );
      exit;
    }
  }
}
add_action( 'template_redirect', 'nm_serial_podcast_redirect' );

/**
 * Check for apology notice and display it if it is in the future
 * This function will check if the apology notice is in the future and display it if it is
 *
 * @return array/Boolean Array of apology post or false if no apology post
 * @since 4.1.1
 */
function check_for_apology_notice() {
  $m = new \Moment\Moment( 1727035200 ); // Sun, 22 Sept 20:00:00 GMT
  $m->addWeeks( 8 ); // Time for notice to show
  $momentFromVo = $m->fromNow();

  if ( $momentFromVo->getDirection() === 'future' ) {
    $apology_post = get_posts(
        array(
            'name'      => 'gary-and-jack-lubner-apology',
            'post_type' => 'notice',
        )
    );
    if ( $apology_post ) {
      return $apology_post;
    } else {
      return false;
    }
  } else {
    return false;
  }
}
/**
 * Get the latest articles ids
 * This function will get the latest articles ids, excluding the featured posts if set
 * If the featured posts are not set, it will return the latest articles ids
 * If the latest articles are already in the featured posts, it will skip them
 * If there are no latest articles, it will return false
 *
 * @param array $featured_posts_ids Array of featured post ids
 *
 * @return array/Boolean Array of latest articles ids or false if no latest articles
 */
function get_latest_articles_ids( $featured_posts_ids = false ) {
  $query_args = array(
      'category_name'  => 'articles',
      'posts_per_page' => 7,
      'fields'         => 'ids',
  );

  if ( is_array( $featured_posts_ids ) && count( $featured_posts_ids ) > 0 ) {
    $query_args = array_merge( $query_args, array( 'post__not_in' => $featured_posts_ids ) );
  }

  $recent_articles = new WP_Query( $query_args );

  if ( ! $recent_articles->have_posts() ) {
    return false;
  }

  $latest_articles_ids = $recent_articles->posts;

  return $latest_articles_ids;
}
/**
 * Get the featured post ids for the above the fold section
 * This function will get the featured post ids from the theme options if set and in position
 * If the featured post id is not set in the theme options, it will use the latest featured post
 * If the latest featured post is already in the theme options featured posts, it will skip it
 *
 * @return array Array of featured post ids
 */
function get_above_the_fold_featured_post_ids() {
  $latest_args = array(
      'posts_per_page' => 16,
      'fields'         => 'ids',
      'category_name'  => 'articles,video,audio',
      'meta_key'       => '_cmb_featurable',
      'meta_value'     => 'on',
  );

  $latest_featured_posts = new WP_Query( $latest_args );
  $latest_featured_posts_ids = $latest_featured_posts->posts;

  $featured_posts_ids = array();

  for ( $i = 1; $i <= 8; $i++ ) { // get the featured posts ids from the theme options if set and in position
    $featured_posts_ids[ $i - 1 ] = NM_get_option( 'nm_above_the_fold_featured_' . $i, 'nm_front_page_above_the_fold_featured_options' );
  }

  for ( $i = 0; $i < 8; $i++ ) {
    if ( ! is_numeric( $featured_posts_ids[ $i ] ) ) { // if the featured post id is not set in the theme options, use the latest featured post
      if ( ! empty( $latest_featured_posts_ids ) ) {
        while ( in_array( $latest_featured_posts_ids[0], $featured_posts_ids ) ) { // ensure fallback latest is not already in the theme options featured posts
          array_shift( $latest_featured_posts_ids );
        }

        $featured_posts_ids[ $i ] = array_shift( $latest_featured_posts_ids );
      }
    }
  }

  if ( empty( $featured_posts_ids ) ) {
    return false;
  }

  return $featured_posts_ids;
}
/**
 * Gets data for livechecker javascript module
 *
 * @return array Array of autovalues for support form
 */
function nm_get_livechecker_data() {
  $overrides = NM_get_option( 'nm_front_page_settings_live_schedule_overrides', 'nm_front_page_options' );
  $messages = NM_get_option( 'nm_front_page_settings_live_schedule_offline_messages', 'nm_front_page_options' );

  $return = array();

  if ( ! empty( $overrides ) ) {
    $return['overrides'] = $overrides;
  }

  if ( ! empty( $messages ) ) {
    $return['messages'] = $messages;
  }

  return $return;
}

/**
 * Takes content filterd by the_content and removes shortcodes and html in order to be useable for meta etc
 * This can be from both post content but also WYSIWYG meta fields.
 *
 * @param string
 *
 * @return string
 */
function nm_clean_content_to_plaintext( $content ) {
  // strip shortcodes from content
  $content = strip_shortcodes( $content );
  // strip html tags
  $cleaned_content = strip_tags( html_entity_decode( $content ) );

  return $cleaned_content;
}
/**
 * Checks a string to see if it is set and also is a number
 *
 * @param string String to check
 *
 * @return boolean
 */
function nm_isset_and_numeric( $string ) {
  if ( isset( $string ) && is_numeric( $string ) ) {
    return true;
  }

  return false;
}
/**
 * Gets metadata for support form autovalues, validates and returns correctly structured array
 *
 * @return array Array of autovalues for support form
 */
function nm_get_support_autovalues() {
  $meta = NM_get_option( 'nm_fundraising_settings_support_section_autovalues', 'nm_fundraising_options' );

  $return = array();

  if ( ! empty( $meta ) ) {
    foreach ( $meta as $index => $autovalues_set ) {
      if ( $index === 0 ) {
        $key = 'default';
      } elseif ( isset( $autovalues_set['url_code'] ) ) {
        $key = $autovalues_set['url_code'];
      } else {
        $key = false;
      }

      $data = new \stdClass();

      $data->show_first = isset( $autovalues_set['show_first'] ) ? $autovalues_set['show_first'] : null;

      $data->regular_low = nm_isset_and_numeric( $autovalues_set['regular_low'] ) ? $autovalues_set['regular_low'] : null;
      $data->regular_medium = nm_isset_and_numeric( $autovalues_set['regular_medium'] ) ? $autovalues_set['regular_medium'] : null;
      $data->regular_high = nm_isset_and_numeric( $autovalues_set['regular_high'] ) ? $autovalues_set['regular_high'] : null;
      $data->oneoff_low = nm_isset_and_numeric( $autovalues_set['oneoff_low'] ) ? $autovalues_set['oneoff_low'] : null;
      $data->oneoff_medium = nm_isset_and_numeric( $autovalues_set['oneoff_medium'] ) ? $autovalues_set['oneoff_medium'] : null;
      $data->oneoff_high = nm_isset_and_numeric( $autovalues_set['oneoff_high'] ) ? $autovalues_set['oneoff_high'] : null;

      if ( $key ) {
        $return[ $key ] = $data;
      }
    }
  }

  if ( count( $return ) === 0 ) {
    $data = new \stdClass();
    $data->show_first = 'regular';
    $data->regular_low = 3;
    $data->regular_medium = 5;
    $data->regular_high = 8;
    $data->oneoff_low = 5;
    $data->oneoff_medium = 10;
    $data->oneoff_high = 20;

    $return['default'] = $data;
  }

  return $return;
}

/**
 * Gets contributors on a post and returns an array of post objects, or false if nothing set
 *
 * @param integer $post_id Post ID to check for contributors
 *
 * @return array/Boolean Array of contributor post objects
 */
function get_contributors_array( $post_id ) {
  $contributors = get_post_meta( $post_id, '_cmb_contributors', true );

  if ( empty( $contributors ) ) {
    return false;
  }

  $contributors_posts_array = array();

  foreach ( explode( ',', $contributors ) as $contributor_id ) {
    $post = get_post( $contributor_id );

    if ( $post ) {
      array_push( $contributors_posts_array, $post );
    }
  }

  if ( count( $contributors_posts_array ) === 0 ) {
    return false;
  }

  return $contributors_posts_array;
}

/**
 * Get the category at the show/brand AKA child level. Meaning get the first child of the top level category.
 *
 * @param integer $post_id Post ID
 *
 * @return Object/Boolean WP Term object or false if doesn't exist
 */
function get_child_level_child_category( $post_id ) {
  $categories = get_the_category( $post_id );
  $child_categories = array_filter( $categories, 'only_child_category_filter' );
  $child_categories = array_values( $child_categories );

  if ( isset( $child_categories[0] ) ) {
    return $child_categories[0];
  } else {
    return false;
  }
}

/**
 * Get the category at the top level. Should be either Articles, Audio or Video
 *
 * @param integer $post_id Post ID
 *
 * @return Object/Boolean WP Term object or false if isnt set
 */
function get_the_top_level_category( $post_id = null ) {
  if ( is_null( $post_id ) ) {
    return false;
  }

  $categories = get_the_category( $post_id );
  $top_level_category = array_filter( $categories, 'only_top_level_category_filter' );

  if ( ! $top_level_category ) { // if there is no top level category set to post
    if ( ! empty( $categories ) && $categories[0]->parent ) { // then check the first category set
      $top_level_category = get_category( $categories[0]->parent ); // and if there is a parent
    }
  } else {
    $top_level_category = array_values( $top_level_category ); // if there is a top level category
    if ( ! empty( $top_level_category ) ) {
      $top_level_category = $top_level_category[0]; // get the first one (because there should only be one)
    }
  }

  if ( ! empty( $top_level_category ) ) {
    return $top_level_category;
  } else {
    return false;
  }
}

/**
 * Does the post have set the Articles category? or is it a child of the Articles category?
 * Defaults to current $post context
 *
 * @param integer $post_id Post ID
 *
 * @return Boolean
 */
function nm_is_article( $post_id = null ) {
  global $post;

  if ( $post_id === null ) {
    $post_id = $post->ID;
  }

  $categories = get_the_terms( $post_id, 'category' ); // get the categories for the post

  if ( ! $categories ) {
    return false;
  }

  // check to see if any of the categories returned match the articles slug or have a parent with the articles id
$found_in_categories = array_filter(
    $categories,
    function ( $category ) {
      if ( $category->slug === 'articles' || $category->parent === get_term_by( 'slug', 'articles', 'category' )->term_id ) {
        return true;
      }

      return false;
    }
); // check to see if any of the categories returned match the articles slug

  if ( count( $found_in_categories ) > 0 ) {
    return true; // if articles slug was found return true
  }

  return false;
}

/**
 * @deprecated 4.0.0 Replaced by nm_is_article()
 * @see nm_is_article()
 *
 * Answer the question is this a single post in the articles category?
 *
 * @return Boolean
 */
function nm_is_single_article() {
  if ( ! is_single() ) { // if not single return straight away
    return false;
  }

  global $post;

  $categories = get_the_terms( $post->ID, 'category' ); // get the categories for the post

  if ( ! $categories ) {
    return false;
  }

$found_in_categories = array_filter(
    $categories,
    function ( $category ) {
      return $category->slug === 'articles';
    }
); // check to see if any of the categories returned match the articles slug

  if ( count( $found_in_categories ) > 0 ) {
    return true; // if articles slug was found return true
  }

  return false;
}

/**
 * Get the first sub category assigned to the post
 *
 * @param integer $postId Post ID
 * @param boolean $object Return WP Term object or just the name
 */
function get_the_sub_category( $postId, $object = false ) {
  $categories = get_the_category( $postId );

  $child_categories = array_filter( $categories, 'only_child_category_filter' );
  $child_categories = array_values( $child_categories );

  if ( isset( $child_categories[0] ) ) {
    if ( $object ) {
      return $child_categories[0];
    } else {
      return $child_categories[0]->name;
    }
  } else {
    return false;
  }
}

// for array_filters

// returns the id from a post object from a WP query
function nm_filter_query_ids( $post ) {
  return $post->ID;
}

// filters an array of post categories for just top level categories
function only_top_level_category_filter( $var ) {
  if ( $var->category_parent == 0 ) {
    return true;
  }
}

// filters an array of post categories for just child categories
function only_child_category_filter( $var ) {
  if ( $var->category_parent !== 0 ) {
    return true;
  }
}

/**
 * Create youtube embed url with consistent parameters
 *
 * Note the option to use the lazysizes for lazyloading the iframe https://github.com/aFarkas/lazysizes
 *
 * @param string $id Youtube video ID
 * @param boolean $autoplay Set true if the video autoplay function is required (only posible on internal website linking due to browser policy)
 *
 * @return string Valid youtube iframe src url
 */
function generate_youtube_embed_url( $id, $autoplay = false ) {
  $url = 'https://www.youtube-nocookie.com/embed/' . $id . '?modestbranding=1&rel=0';

  if ( $autoplay ) {
    $url .= '&autoplay=1';
  }

  return $url;
}

// obviously it gets related posts and returns a WP Query
function get_related_posts( $excluded_ids_array = null, $category_name = null, $number_of_posts = 4 ) {

  // Check if we are on a single page, if not, return false
  if ( ! is_single() ) {
    return false;
  }

  // Get the current post id
  $post_id = get_queried_object_id();

  // Get all the tags of the current post
  $tags = wp_get_post_tags( $post_id );

  // Exlude this post and possibly others if parsed as variable
  $posts_not_in[] = $post_id;

  if ( $excluded_ids_array ) {
    $posts_not_in = array_merge( $posts_not_in, $excluded_ids_array );
  }

  $args = array(
      'post__not_in'   => $posts_not_in,
      'posts_per_page' => $number_of_posts,
  );

  if ( $category_name ) {
    $args['category_name'] = $category_name;
  }

  if ( $tags ) {

    $tag_ids = array();

    // Get the id of every tag and add it to the array
    foreach ( $tags as $individual_tag ) {
      $tag_ids[] = $individual_tag->term_id;
    }

    // args for the query which will get the related posts
    $args = array_merge(
        $args,
        array(
            'tag__in' => $tag_ids,
            'order'   => 'DESC',
            'orderby' => 'post_date',
        )
    );

  }

  return new WP_Query( $args );
}

// for site options. Returns array for select
function home_focus_list() {

  $focuses = array(
      null => 'No Focus Shown',
  );

$focus_terms = get_terms(
    array(
        'taxonomy' => 'focus',
    )
);

  if ( $focus_terms ) {
    foreach ( $focus_terms as $term ) {
      $focuses[ $term->term_id ] = $term->name;
    }
  }

  return $focuses;
}

function menu_tags_list() {

  $tags = array();

$tags_all = get_terms(
    array(
        'taxonomy' => 'post_tag',
    )
);

  if ( $tags_all ) {
    foreach ( $tags_all as $tag ) {
      $tags[ $tag->term_id ] = $tag->name;
    }
  }

  return $tags;
}
