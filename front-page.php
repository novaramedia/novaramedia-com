<?php
get_header();
?>

<!-- main content -->
<main id="main-content" data-testid="main-content">
  <?php
    get_template_part('partials/front-page/submenu');

    get_template_part('partials/front-page/products-bar');

    // **************
    // ABOVE THE FOLD
    // **************

    $featured_posts_ids = get_above_the_fold_featured_post_ids();
    $latest_news_posts_ids = get_latest_news_ids($featured_posts_ids);

    get_template_part('partials/front-page/above-the-fold', null, array(
      'featured_posts_ids' => $featured_posts_ids,
      'latest_news_posts_ids' => $latest_news_posts_ids,
    ));

    // Highlight block stays pinned directly below the fold. It is excluded from
    // the Layout editor because it takes args and dedupes against the
    // above-the-fold posts. Disabled by default via its own settings page.
    get_template_part('partials/front-page/highlight-block', null, array(
      'excluded_posts_ids' => array_merge($featured_posts_ids, $latest_news_posts_ids),
    ));

    // Editable layout: banners + product blocks, ordered in Front Page > Layout.
    // Falls back to the historic order when no layout has been saved.
    foreach (nm_get_front_page_layout() as $block_slug) {
      nm_render_front_page_block($block_slug);
    }

    get_template_part('partials/front-page/mega-block');
  ?>

<!-- end main-content -->
</main>

<?php
get_footer();
?>
