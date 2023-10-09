<?php

?>
<section class="front-page__above-the-fold container margin-bottom-mid mobile-margin-top-small mobile-margin-bottom-basic">
  <div class="above-the-fold__featured-posts above-the-fold__featured-posts--primary grid-row">
    <div class="grid-item is-xxl-18 margin-bottom-small padding-bottom-small ui-border-bottom">
      <?php
        $latest_args = array(
          'posts_per_page' => 8,
          'fields' => 'ids',
        );

        $latest_fallback = new WP_Query(array_merge($latest_args, array('category_name' => 'articles,video,audio')));
        $latest_posts_ids = $latest_fallback->posts;

        get_template_part('partials/front-page/above-the-fold/featured-posts-block', null, array(
          'post_1' => $latest_posts_ids[0],
          'post_2' => $latest_posts_ids[1],
          'post_3' => $latest_posts_ids[2],
          'post_4' => $latest_posts_ids[3],
          'layout_direction' => 'ltr'
        ));
      ?>
      <div class="grid-row">
        <div class="grid-item is-xxl-24 mt-4 mb-5 ui-border-bottom">
        </div>
      </div>
      <?php
        get_template_part('partials/front-page/above-the-fold/featured-posts-block', null, array(
          'post_1' => $latest_posts_ids[4],
          'post_2' => $latest_posts_ids[5],
          'post_3' => $latest_posts_ids[6],
          'post_4' => $latest_posts_ids[7],
          'layout_direction' => 'rtl'
        ));
      ?>
    </div>
    <div class="grid-item is-xxl-6">
      <?php get_template_part('partials/front-page/above-the-fold/latest-articles'); ?>
    </div>
  </div>
</section>
