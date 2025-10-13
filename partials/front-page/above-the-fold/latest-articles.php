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

  $number_of_articles = $recent_articles->post_count;
  $i = 0;

  if ($apology_post = check_for_apology_notice()) { // Temporary fix for the apology notice
    $post_id = $apology_post[0]->ID;
  ?>
  <div class="margin-bottom-small padding-bottom-small ui-border-bottom">
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

      if ($i === 1 || $i === 6) {
        get_template_part('partials/front-page/above-the-fold/latest-article--thumb-small', null, array(
          'post_id' => $post->ID,
          'has_bottom_border' => $has_border,
        ));
      } else if ($i === 3) {
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
