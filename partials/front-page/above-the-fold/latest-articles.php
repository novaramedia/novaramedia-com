<?php
  if ($args['latest_articles_posts_ids']) {
    $latest_articles_posts_ids = $args['latest_articles_posts_ids'];
  } else {
    return;
  }

  $recent_articles = new WP_Query(
    array(
      'post__in' => $latest_articles_posts_ids,
      'orderby' => 'post__in',
    )
  );

  // count article returned here
  $number_of_articles = $recent_articles->post_count;

  $i = 0;

  if ($recent_articles->have_posts()) {
    while ($recent_articles->have_posts()) {
      $recent_articles->the_post();
      $has_border = ($i !== ($number_of_articles - 1)) ? true : false;

      // [temp logic]. to be driven by meta logics based on position and quality of image assets

      if ($i === 1 || $i === 6) { // render small thumb layout
        get_template_part('partials/front-page/above-the-fold/latest-article--thumb-small', null, array(
          'post_id' => $post->ID,
          'has_bottom_border' => $has_border,
        ));
      } else if ($i === 3) { // render full image layout
        get_template_part('partials/front-page/above-the-fold/latest-article--thumb-large', null, array(
          'post_id' => $post->ID,
          'has_bottom_border' => $has_border,
        ));
      } else {
        get_template_part('partials/front-page/above-the-fold/latest-article--default', null, array(
          'post_id' => $post->ID,
          'has_bottom_border' => $has_border,
        ));
      }

      $i++;
    }
  }
?>
