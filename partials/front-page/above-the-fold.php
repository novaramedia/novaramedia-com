<?php
  // LOGIC/FUNCTION
  // TODO: Logic for recent articles bar images or not | custom cmb2 field option 1-5 radio buttons row or ?
  // TODO: Update wordpress declared image sizes

  if ($args['featured_posts_ids']) {
    $featured_posts_ids = $args['featured_posts_ids'];
  } else {
    return;
  }

  if ($args['latest_articles_posts_ids']) {
    $latest_articles_posts_ids = $args['latest_articles_posts_ids'];
  } else {
    return;
  }
?>
<section class="front-page__above-the-fold container container--padded mt-2 mb-6 mb-s-5">
  <div class="above-the-fold layout-grid">
    <div class="above-the-fold__featured-1 ui-border-bottom pb-4 mb-4">
      <?php
        get_template_part('partials/front-page/above-the-fold/featured-posts-block', null, array(
          'block_number' => 1,
          'featured_posts_ids' => $featured_posts_ids,
        ));
      ?>
    </div>
    <div class="above-the-fold__latest-articles">
      <?php get_template_part('partials/front-page/above-the-fold/latest-articles', null, array(
        'latest_articles_posts_ids' => $latest_articles_posts_ids,

      )); ?>
    </div>
    <div class="above-the-fold__featured-2">
      <?php
        get_template_part('partials/front-page/above-the-fold/featured-posts-block', null, array(
          'block_number' => 2,
          'featured_posts_ids' => $featured_posts_ids,
        ));
      ?>
    </div>
  </div>
</section>
