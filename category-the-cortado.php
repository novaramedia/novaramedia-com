<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/the-cortado/';

// The newsletter record supplies the Mailchimp key and signup copy (Downstream pattern).
$newsletter = get_posts(
  array(
    'post_type'      => 'newsletter',
    'name'           => 'the-cortado',
    'posts_per_page' => 1,
  )
);
$newsletter_post_id = ! empty( $newsletter ) ? $newsletter[0]->ID : false;

// Page 1 pulls the first post out as the featured "Latest" block; the grid that
// follows continues the same loop, so pagination counts stay honest (Downstream pattern).
$query_var_paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$is_first_page   = ( $query_var_paged === 1 );

get_header();
?>

<main id="main-content" class="category-archive category-archive__the-cortado" data-testid="main-content">
  <style type="text/css">
    /* Wordmark is inlined (nm_get_file) so its currentColor fill is CSS-driven. */
    .category-archive__the-cortado__wordmark {
      color: var(--color-gray-base);
    }

    .category-archive__the-cortado__wordmark svg {
      display: block;
      width: 100%;
      height: auto;
    }

    .category-archive__the-cortado__presenters {
      width: 100%;
      max-width: 678px;
      margin: 0 auto;
    }

    /* The shared card partial supplies the rules; the brand picks their colour. */
    .category-archive__the-cortado__past-issues .ui-border-top,
    .category-archive__the-cortado__past-issues .ui-border {
      --ui-border-color: var(--color-ochre);
    }
  </style>

  <?php // ── Section 1: Hero ── ?>
  <section class="container mt-4 mb-4" data-testid="cortado-hero">
    <div class="grid-item is-xxl-24">
      <div class="grid-row background-ochre ui-rounded-box ui-backgrounded-box-padding ui-backgrounded-box-padding--flush-bottom">

        <div class="grid-item is-xxl-24">
          <p class="font-size-10 font-weight-bold text-uppercase mb-1">Newsletter</p>
          <h1 class="category-archive__the-cortado__wordmark m-0" aria-label="The Cortado">
            <?php echo nm_get_file( '/dist/img/products/the-cortado/the-cortado-wordmark.svg' ); ?>
          </h1>
          <picture>
            <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.webp' ); ?>" type="image/webp">
            <img class="category-archive__the-cortado__presenters u-display-block" src="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.png' ); ?>" alt="Ash Sarkar and Steven Methven" width="1195" height="762" loading="eager" fetchpriority="high" />
          </picture>
        </div>

      </div>
    </div>
  </section>

  <?php // ── Section 2: Signup ── ?>
  <?php
  if ( $newsletter_post_id ) {
    get_template_part(
      'partials/email-signup',
      null,
      array(
        'newsletter_post_id' => $newsletter_post_id,
        'background-color'   => 'white',
        'button-color'       => 'black',
        'button-label'       => 'Get The Cortado',
        'hide-discover'      => true,
        'hide-headline'      => true,
        'hide-image'         => true,
      )
    );
    ?>
  <div class="container">
    <div class="grid-row">
      <?php // Border sits on an inner element: .grid-item carries half-gutter padding, so a border on it would overhang the content columns by 8px each side. ?>
      <div class="grid-item is-xxl-24">
        <div class="ui-border-bottom ui-border--gray-mid"></div>
      </div>
    </div>
  </div>
    <?php
  }
  ?>

  <?php // ── Section 3: Latest ── ?>
  <?php
  if ( $is_first_page && have_posts() ) {
    the_post();
    $featured_post_id = get_the_ID();
    ?>
  <section class="container mt-5 mb-5" data-testid="cortado-latest">
    <div class="grid-row">
      <div class="grid-item is-s-24 is-xxl-12 mb-s-4">
        <a href="<?php the_permalink(); ?>" class="ui-hover u-display-block">
          <?php render_thumbnail( $featured_post_id, 'col12-16to9', array( 'class' => 'ui-rounded-box u-display-block' ) ); ?>
        </a>
      </div>
      <div class="grid-item is-s-24 is-xxl-12">
        <p class="font-size-8 font-weight-bold text-uppercase">Latest Cortado</p>
        <a href="<?php the_permalink(); ?>" class="ui-hover u-display-block">
          <h2 class="font-size-15 font-weight-bold text-wrap-pretty mt-2"><?php the_title(); ?></h2>
          <p class="font-size-10 font-weight-bold text-uppercase mt-2"><?php render_bylines( $featured_post_id ); ?></p>
          <div class="font-size-10 mt-2"><?php render_standfirst( $featured_post_id ); ?></div>
        </a>
      </div>
    </div>
  </section>
    <?php
  }
  ?>

  <?php // ── Section 4: Past issues ── ?>
  <?php if ( have_posts() ) { ?>
  <section class="container category-archive__the-cortado__past-issues mt-5 mb-5" data-testid="cortado-past-issues">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-4">
        <h2 class="font-size-8 font-weight-bold text-uppercase">Past Issues</h2>
      </div>
      <?php
      while ( have_posts() ) {
        the_post();

        get_template_part(
          'partials/post-layouts/archive-post-no-thumbnail',
          null,
          array(
            'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-5',
          )
        );
      }
      ?>
    </div>
  </section>
  <?php } ?>

  <?php // ── Section 5: Footer row ── ?>
  <section class="container mb-5" data-testid="cortado-footer-row">
    <div class="grid-row">
      <div class="grid-item is-s-24 is-xxl-12 font-size-10 font-weight-bold mb-s-3">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
      <div class="grid-item is-s-24 is-xxl-12 text-align-right">
        <a href="<?php echo esc_url( site_url( 'newsletters/' ) ); ?>" class="ui-action-link">Discover all our newsletters</a>
      </div>
    </div>
  </section>

</main>
<?php
get_footer();
