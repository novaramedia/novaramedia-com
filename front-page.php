<?php
get_header();

$banners = array(
  NM_get_option('nm_front_page_banner_option_1'),
  NM_get_option('nm_front_page_banner_option_2'),
  NM_get_option('nm_front_page_banner_option_3'),
  NM_get_option('nm_front_page_banner_option_4')
);
?>

<!-- main content -->
<main id="main-content">
  <?php
    get_template_part('partials/front-page/submenu');

    get_template_part('partials/front-page/products-bar');

    // **************
    // ABOVE THE FOLD
    // **************

    $featured_posts_ids = get_above_the_fold_featured_post_ids();
    $latest_articles_posts_ids = get_latest_articles_ids($featured_posts_ids);

    get_template_part('partials/front-page/above-the-fold', null, array(
      'featured_posts_ids' => $featured_posts_ids,
      'latest_articles_posts_ids' => $latest_articles_posts_ids,
    ));

    render_front_page_banner($banners[0]);

    get_template_part('partials/front-page/highlight-block', null, array(
      'excluded_posts_ids' => array_merge($featured_posts_ids, $latest_articles_posts_ids),
    ));

    get_template_part('partials/front-page/show-blocks/novara-live');

    render_front_page_banner($banners[1]);

    get_template_part('partials/front-page/show-blocks/audio');

    render_front_page_banner($banners[2]);

    get_template_part('partials/front-page/show-blocks/downstream');

    render_front_page_banner($banners[3]);

    get_template_part('partials/front-page/mega-block');
  ?>

<!-- end main-content -->
</main>

<?php
get_footer();
?>
