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

    get_template_part('partials/front-page/above-the-fold');

    render_front_page_banner($banners[0]);

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
