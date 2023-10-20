<?php
  // TODO: Logic and UI for curation [x]
  // TODO: Filtering to avoid double display [x]
  // TODO: Layout options for recent articles bar [x]
  // TODO: Fix stick bottom on primary featured [x]
  // TODO: Round soften all images [x]
  // TODO: UI element for AV tag [x]
  // LOGIC/FUNCTION
  // TODO: Curation select for featureable - meta checkbox?
  // TODO: Logic for recent articles bar images or not
  // TODO: Update wordpress declared image sizes
  // STYLING
  // TODO: Responsive breakpoint details
  // TODO: Vertical borders?
  // TODO: Hover states?

$latest_args = array(
  'posts_per_page' => 16,
  'fields' => 'ids',
); // this will also filter by some meta value as well

$latest_featured_posts = new WP_Query(array_merge($latest_args, array('category_name' => 'articles,video,audio'))); // get the latest featured posts
$latest_featured_posts_ids = $latest_featured_posts->posts;

$featured_posts_ids = [];

for ($i = 1; $i <= 8; $i++) { // get the featured posts ids from the theme options if set and in position
  $featured_posts_ids[$i - 1] = NM_get_option('nm_above_the_fold_featured_' . $i, 'nm_front_page_above_the_fold_featured_options');
}

for ($i = 0; $i < 8; $i++) {
  if (!is_numeric($featured_posts_ids[$i])) { // if the featured post id is not set in the theme options, use the latest featured post
    while (in_array($latest_featured_posts_ids[0], $featured_posts_ids)) { // ensure fallback latest is not already in the theme options featured posts
      array_shift($latest_featured_posts_ids);
    }
    $featured_posts_ids[$i] = array_shift($latest_featured_posts_ids);
  }
}
?>
<section class="front-page__above-the-fold container mt-2 mb-6">
  <div class="above-the-fold layout-grid">
    <div class="above-the-fold__featured-1">
      <?php
        get_template_part('partials/front-page/above-the-fold/featured-posts-block', null, array(
          'block_number' => 1,
          'featured_posts_ids' => $featured_posts_ids,
        ));
      ?>
    </div>
    <div class="above-the-fold__latest-articles">
      <?php get_template_part('partials/front-page/above-the-fold/latest-articles', null, $featured_posts_ids); ?>
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
