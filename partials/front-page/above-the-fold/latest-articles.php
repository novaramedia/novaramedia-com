<?php
  if ( empty( $args['latest_news_posts_ids'] ) ) {
    return;
  }
  $latest_news_posts_ids = $args['latest_news_posts_ids'];

  $recent_articles = new WP_Query(
    array(
      'post__in' => $latest_news_posts_ids,
      'orderby' => 'post__in',
    )
  );

  $number_of_articles = $recent_articles->post_count;

  // Derive the ID list from the query results so indices align with the
  // render loop. This avoids index drift if WP_Query filters out any
  // deleted or unpublished posts from the input IDs.
  $rendered_post_ids = wp_list_pluck( $recent_articles->posts, 'ID' );

  // Image slot assignment
  // ----------------------
  // The latest articles list shows 3 thumbnail images spread across the posts.
  // News posts typically lack strong imagery, so we shift image slots away from
  // them onto non-news (feature/opinion/analysis) posts that have better images.
  //
  // The algorithm:
  // 1. Collect the indices of all non-news posts in display order.
  // 2. If there are 3+ non-news posts, the first 3 get the image slots.
  //    This effectively "shifts" images away from wherever news posts sit.
  //    Sizes follow the normal pattern: small — LARGE — small.
  // 3. If fewer than 3 non-news posts exist (fallback path), we start with
  //    those and fill remaining slots from hardcoded fallback positions
  //    (2nd, 4th, 7th post — indices 1, 3, 6), enforcing a minimum gap of
  //    1 post between image slots. News posts never get the large size;
  //    instead the first non-news post gets 'large' and everything else
  //    gets 'small'.
  // 4. The chosen indices are sorted so they appear in reading order before
  //    sizes are assigned.
  $non_news_indices = array();
  $default_image_positions = array( 1, 3, 6 );

  foreach ( $rendered_post_ids as $idx => $pid ) {
    if ( ! has_category( 'news', $pid ) ) {
      $non_news_indices[] = $idx;
    }
  }

  $used_fallback = false;

  if ( count( $non_news_indices ) >= 3 ) {
    // Enough non-news posts — use the first 3 as image slots
    $image_indices = array_slice( $non_news_indices, 0, 3 );
  } else {
    // Not enough non-news posts — use what we have, then fill from defaults.
    // Fallback slots (which may land on news posts) are constrained:
    //  - Must be at least 2 positions from any existing image slot (gap of 1 post)
    //  - News posts filled by fallback never receive the large image size
    $used_fallback = true;
    $image_indices = $non_news_indices;
    $remaining = 3 - count( $image_indices );
    foreach ( $default_image_positions as $pos ) {
      if ( $remaining <= 0 ) {
        break;
      }
      if ( $pos >= $number_of_articles || in_array( $pos, $image_indices, true ) ) {
        continue;
      }
      // Enforce minimum gap of 1 post between any two image slots
      $too_close = false;
      foreach ( $image_indices as $existing ) {
        if ( abs( $pos - $existing ) < 2 ) {
          $too_close = true;
          break;
        }
      }
      if ( ! $too_close ) {
        $image_indices[] = $pos;
        $remaining--;
      }
    }
  }

  // Sort so positions are in display order before assigning sizes
  sort( $image_indices );

  // Assign image sizes.
  // Normal path: middle slot (array index 1) gets 'large', others 'small'.
  //   With 1 slot the only image is 'large'; with 2+ the second is 'large'.
  // Fallback path: first non-news post gets 'large', everything else 'small'.
  $image_map = array();
  $large_assigned = false;
  foreach ( $image_indices as $j => $idx ) {
    if ( $used_fallback ) {
      $give_large = ! $large_assigned && in_array( $idx, $non_news_indices, true );
    } else {
      $give_large = ( count( $image_indices ) === 1 ) ? $j === 0 : $j === 1;
    }
    $image_map[ $idx ] = $give_large ? 'large' : 'small';
    if ( $give_large ) {
      $large_assigned = true;
    }
  }

  $i = 0;

  // TODO: Deprecate and remove apology_notice functionality
  // This is a hardcoded temporary solution for a specific incident from Sept 2024.
  // The apology_notice injection causes index drift in the image_map, making image
  // slots shift onto wrong posts. Consider removing this entirely or refactoring
  // image_map to use post IDs as keys instead of numeric positions.
  // Related: check_for_apology_notice() in lib/functions-custom.php
  // Also used in: partials/front-page/products-bar.php
  if ($apology_post = check_for_apology_notice()) { // Temporary fix for the apology notice
    $post_id = $apology_post[0]->ID;
  ?>
  <div class="mb-4 pb-4 ui-border-bottom">
    <div class="layout-split-level font-size-8 font-weight-bold mb-1">
      <span class="ui-tag">Correction</span>
    </div>
    <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
      <h4 class="post__title font-size-11 font-size-s-12 font-condensed"><?php echo get_the_title($post_id); ?></h4>
    </a>
  </div>
  <?php
  }

  if ($recent_articles->have_posts()) {
    while ($recent_articles->have_posts()) {
      $recent_articles->the_post();
      $has_border = ($i !== ($number_of_articles - 1)) ? true : false;
      $show_image = isset( $image_map[ $i ] ) ? $image_map[ $i ] : false;

      get_template_part('partials/front-page/above-the-fold/latest-article', null, array(
        'post_id' => $post->ID,
        'has_bottom_border' => $has_border,
        'show_image' => $show_image,
      ));

      $i++;
    }
    wp_reset_postdata();
  }
?>
