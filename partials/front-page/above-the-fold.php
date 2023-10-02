<?php

?>
<section class="front-page__above-the-fold container margin-bottom-mid mobile-margin-top-small mobile-margin-bottom-basic">
  <div class="above-the-fold__featured-posts above-the-fold__featured-posts--primary grid-row">
    <div class="grid-item is-xxl-18 margin-bottom-small padding-bottom-small" style="border-bottom: 1px solid #BAB8B8">
      <?php
        $recent_featured = new WP_Query(array(
          'category_name' => 'video,audio,articles',
          'posts_per_page' => 8,
        ));
      ?>
      <div class="grid-row grid--nested">
        <div class="grid-item is-xxl-16">
          <?php
          if ($recent_featured->have_posts()) {
            $recent_featured->the_post();
            get_template_part('partials/front-page/above-the-fold/featured-post-primary');
          }
          ?>
        </div>
        <div class="grid-item is-xxl-8">
        <?php
          if ($recent_featured->have_posts()) {
            $recent_featured->the_post();
            get_template_part('partials/front-page/above-the-fold/featured-post-secondary');
          }

          if ($recent_featured->have_posts()) {
            $recent_featured->the_post();
            get_template_part('partials/front-page/above-the-fold/featured-post-tertiary');
          }

          if ($recent_featured->have_posts()) {
            $recent_featured->the_post();
            get_template_part('partials/front-page/above-the-fold/featured-post-tertiary');
          }
        ?>
        </div>
      </div>
    </div>
    <div class="grid-item is-xxl-6">
      <?php get_template_part('partials/front-page/above-the-fold/latest-articles'); ?>
    </div>
  </div>
</section>
