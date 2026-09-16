<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$category = get_category( get_query_var( 'cat' ) );

$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/the-cortado/';

get_header();
?>

<main id="main-content" class="category-archive category-archive__the-cortado" data-testid="main-content">
  <style type="text/css">
    .category-archive__the-cortado__wordmark img {
      width: 100%;
    }

    .category-archive__the-cortado__presenters {
      width: 100%;
      max-width: 678px;
      margin: 0 auto;
    }
  </style>

  <?php // ── Section 1: Hero ── ?>
  <section class="container mt-4 mb-4" data-testid="cortado-hero">
    <div class="grid-item is-xxl-24">
      <div class="grid-row background-ochre ui-rounded-box ui-backgrounded-box-padding ui-backgrounded-box-padding--flush-bottom">

        <div class="grid-item is-xxl-24">
          <p class="font-size-10 font-weight-bold text-uppercase mb-1">Newsletter</p>
          <h1 class="category-archive__the-cortado__wordmark m-0">
            <img class="u-display-block" src="<?php echo esc_url( $base_image_path . 'the-cortado-wordmark.svg' ); ?>" alt="The Cortado" width="1384" height="166" />
          </h1>
          <picture>
            <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.webp' ); ?>" type="image/webp">
            <img class="category-archive__the-cortado__presenters u-display-block" src="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.png' ); ?>" alt="Ash Sarkar and Steven Methven" width="1357" height="720" loading="eager" fetchpriority="high" />
          </picture>
        </div>

      </div>
    </div>
  </section>

</main>
<?php
get_footer();
