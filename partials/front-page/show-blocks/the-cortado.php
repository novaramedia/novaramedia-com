<?php
/**
 * Front page product block: The Cortado.
 *
 * Three stacked sections — signup banner, latest issue, recent issues — on the page
 * background rather than in a coloured box, matching the design. Bails out entirely if
 * the category or its posts are missing, the same way the other show blocks do.
 */

$cortado_category = get_term_by( 'slug', 'the-cortado', 'category' );

if ( ! $cortado_category ) {
  return;
}

$cortado_posts = get_posts(
  array(
    'posts_per_page' => 1,
    'category'       => $cortado_category->term_id,
  )
);

if ( empty( $cortado_posts ) ) {
  return;
}

$category_link   = get_category_link( $cortado_category->term_id );
$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/the-cortado/';

$featured = $cortado_posts[0];

// Own query so the shared post-layout partial runs inside a real loop.
$recent = new WP_Query(
  array(
    'posts_per_page' => 3,
    'cat'            => $cortado_category->term_id,
    'post__not_in'   => array( $featured->ID ),
  )
);

// The newsletter record supplies the Mailchimp key, as on the category archive.
$newsletter         = get_posts(
  array(
    'post_type'      => 'newsletter',
    'name'           => 'the-cortado',
    'posts_per_page' => 1,
  )
);
$newsletter_post_id = ! empty( $newsletter ) ? $newsletter[0]->ID : false;
$mailchimp_key      = $newsletter_post_id ? get_post_meta( $newsletter_post_id, '_nm_mailchimp_key', true ) : false;
?>
<section class="container front-page-cortado mt-5 mb-5" data-testid="front-page-cortado">
  <style type="text/css">
    /* Wordmark is inlined so its currentColor fill can be set here. Sized by height so
       it tracks the type scale rather than an arbitrary width; the viewBox gives the width. */
    .front-page-cortado__wordmark {
      color: var(--color-ochre);
      display: block;
    }

    .front-page-cortado__wordmark svg {
      display: block;
      height: 2.75rem;
      width: auto;
      max-width: 100%;
    }

    /* Everything in the banner hangs from the top; only the presenters sit on the
       bottom edge, as drawn. No utility exists for per-item flex-end. */
    .front-page-cortado__presenters-col {
      align-self: flex-end;
    }

    .front-page-cortado__presenters {
      display: block;
      width: 100%;
      height: auto;
    }

    /* Eyebrows and the archive link carry the brand colour. */
    .front-page-cortado__label {
      color: var(--color-ochre);
    }

    /* Card rules and avatar rings are black here, unlike the ochre on the archive. */
    .front-page-cortado .ui-border-top,
    .front-page-cortado .ui-circle-image {
      --ui-border-color: var(--color-black-soft);
    }
  </style>

  <?php // ── Sign-up banner ── ?>
  <div class="grid-row">
    <div class="grid-item is-s-24 is-l-12 is-xxl-8 mb-s-4 mb-l-4">
      <a href="<?php echo esc_url( $category_link ); ?>" class="front-page-cortado__wordmark ui-hover" aria-label="The Cortado">
        <?php echo nm_get_file( '/dist/img/products/the-cortado/the-cortado-wordmark.svg' ); ?>
      </a>
      <p class="font-size-11 mt-3 text-wrap-pretty">
        Your shot of political analysis from <strong>Ash Sarkar</strong> and <strong>Steven Methven</strong>. Brewed on Monday and Friday mornings.
      </p>
    </div>

    <div class="grid-item is-s-24 is-l-12 is-xxl-8 mb-s-4 mb-l-4 front-page-cortado__presenters-col">
      <picture>
        <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.avif' ); ?>" type="image/avif">
        <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.webp' ); ?>" type="image/webp">
        <img class="front-page-cortado__presenters" src="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.png' ); ?>" alt="" width="1195" height="762" loading="lazy" />
      </picture>
    </div>

    <?php if ( $mailchimp_key ) { ?>
    <div class="grid-item is-s-24 is-l-24 is-xxl-8">
      <?php render_mailchimp_signup_form( $mailchimp_key, 'white', 'black', 'Get The Cortado' ); ?>
    </div>
    <?php } ?>

    <?php // No top margin: the rule sits directly under the presenters, which are flush to it. ?>
    <div class="grid-item is-xxl-24">
      <div class="ui-border-bottom ui-border--gray-mid"></div>
    </div>
  </div>

  <?php // ── Latest issue ── ?>
  <div class="grid-row mt-5">
    <div class="grid-item is-s-24 is-xxl-12 mb-s-4">
      <a href="<?php echo esc_url( get_the_permalink( $featured->ID ) ); ?>" class="ui-hover u-display-block">
        <?php render_thumbnail( $featured->ID, 'col12-16to9', array( 'class' => 'ui-rounded-box u-display-block' ) ); ?>
      </a>
    </div>
    <div class="grid-item is-s-24 is-xxl-12">
      <p class="front-page-cortado__label font-size-8 font-weight-bold text-uppercase">Latest Issue</p>
      <a href="<?php echo esc_url( get_the_permalink( $featured->ID ) ); ?>" class="ui-hover u-display-block">
        <h3 class="font-size-15 font-weight-bold text-wrap-pretty mt-2"><?php echo esc_html( get_the_title( $featured->ID ) ); ?></h3>
        <p class="font-size-10 font-weight-bold text-uppercase mt-2"><?php render_bylines( $featured->ID ); ?></p>
        <div class="font-size-10 mt-2"><?php render_standfirst( $featured->ID ); ?></div>
      </a>
    </div>
  </div>

  <?php // ── Recent issues ── ?>
  <?php if ( $recent->have_posts() ) { ?>
  <div class="grid-row mt-5">
    <div class="grid-item is-xxl-24 mb-4">
      <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover u-display-block">
        <div class="front-page-cortado__label layout-split-level font-size-9 font-weight-bold">
          <h3 class="font-weight-bold text-uppercase">Recent Issues</h3>
          <span>See all</span>
        </div>
      </a>
    </div>
    <?php
    while ( $recent->have_posts() ) {
      $recent->the_post();

      get_template_part(
        'partials/post-layouts/archive-post-no-thumbnail',
        null,
        array(
          'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
          'hide-excerpt'      => true,
        )
      );
    }

    wp_reset_postdata();
    ?>
  </div>
  <?php } ?>
</section>
