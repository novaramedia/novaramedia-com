<?php
  $number_of_articles = 7;
  $query_args = array(
    'category_name' => 'articles',
    'posts_per_page' => $number_of_articles,
  );

  if (is_array($args) && count($args) > 0) {
    $query_args = array_merge($query_args, array('post__not_in' => $args));
  }

  $recent_articles = new WP_Query($query_args);
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
