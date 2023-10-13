<?php
  // TODO: Logic and UI for curation [~]
  // TODO: Filtering to avoid double display [x]
  // TODO: Responsive breakpoint details
  // TODO: Related post/see more logic
  // TODO: Vertical borders?
  // TODO: Round soften all images
  // TODO: Hover states?
  // TODO: Layout options for recent articles bar [x]
  // TODO: UI element for AV tag
?>
<section class="front-page__above-the-fold container margin-bottom-mid mobile-margin-top-small mobile-margin-bottom-basic">
  <div class="above-the-fold__featured-posts above-the-fold__featured-posts--primary grid-row">
    <div class="grid-item is-xxl-18 margin-bottom-small padding-bottom-small ui-border-bottom">
      <?php
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

        get_template_part('partials/front-page/above-the-fold/featured-posts-block', null, array(
          'block_number' => 1,
          'featured_posts_ids' => $featured_posts_ids,
        ));
      ?>
      <div class="grid-row">
        <div class="grid-item is-xxl-24 mt-4 mb-5 ui-border-bottom">
        </div>
      </div>
      <?php
        get_template_part('partials/front-page/above-the-fold/featured-posts-block', null, array(
          'block_number' => 2,
          'featured_posts_ids' => $featured_posts_ids,
        ));
      ?>
    </div>
    <div class="grid-item is-xxl-6">
      <?php get_template_part('partials/front-page/above-the-fold/latest-articles', null, $featured_posts_ids); ?>
    </div>
  </div>
</section>
