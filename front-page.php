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

    // Editable layout: banners + product blocks, ordered in Front Page > Layout.
    // Renders no sections when no layout has been saved. The shared
    // context is passed to every product block; only those that need it use it
    // (e.g. the highlight section dedupes against the above-the-fold posts).
    // Normalise to arrays: get_above_the_fold_featured_post_ids() returns false
    // when empty (low-content / staging), which would make array_merge throw.
    $block_context = array(
      'excluded_posts_ids' => array_merge(
        is_array($featured_posts_ids) ? $featured_posts_ids : array(),
        is_array($latest_news_posts_ids) ? $latest_news_posts_ids : array()
      ),
    );

    // Sections carry no outer vertical margin of their own. The sequence buffers
    // each one, skips any that render nothing, and emits the join between each
    // rendered pair from the two frames (see lib/front-page-sequence.php).
    // Above the Fold and the Mega Block are the permanent open bookends.
    $sequence = new NM_Front_Page_Sequence();

    $sequence->render('open', function () use ($featured_posts_ids, $latest_news_posts_ids) {
      get_template_part('partials/front-page/above-the-fold', null, array(
        'featured_posts_ids' => $featured_posts_ids,
        'latest_news_posts_ids' => $latest_news_posts_ids,
      ));
    });

    foreach (nm_get_front_page_layout() as $block_slug) {
      $sequence->render(nm_get_front_page_block_frame($block_slug), function () use ($block_slug, $block_context) {
        nm_render_front_page_block($block_slug, $block_context);
      });
    }

    $sequence->render('open', function () {
      get_template_part('partials/front-page/mega-block');
    });
  ?>

<!-- end main-content -->
</main>

<?php
get_footer();
?>
