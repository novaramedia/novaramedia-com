<?php
get_header();

$category = get_category( get_query_var( 'cat' ) );

$podcast_url = ! empty( get_term_meta( $category->term_id, '_nm_podcast_url', true ) ) ? get_term_meta( $category->term_id, '_nm_podcast_url', true ) : false;
$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Subscribe to the podcast';
?>
<main id="main-content" class="category-archive category-archive__if-i-speak">
  <style type="text/css">
    .category-archive__if-i-speak__header {
      color: var(--color-red);
    }

    .category-archive__if-i-speak__title {
      font-size: 18.5rem;
      line-height: .9;
      letter-spacing: -0.03em;
      font-weight: 700;
    }

    .category-archive__if-i-speak__image {
      background-size: cover;
      background-position: top;
      height: 210px;
      width: 360px;
      margin: 0 auto;
      margin-top: 0;
    }

    .category-archive__if-i-speak__border {
      background-color: var(--color-red);
      margin-top: 0;
    }

    .avif .category-archive__if-i-speak__image, .webp .category-archive__if-i-speak__image {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/banners/if-i-speak.webp'; ?>);
    }

    .fallback .category-archive__if-i-speak__image {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/banners/if-i-speak.png'; ?>);
    }

    @media screen and (max-width: 1408px) {
      .category-archive__if-i-speak__title {
        font-size: 14.5rem;
      }

      .category-archive__if-i-speak__image {
        height: 220px;
        width: 320px;
      }
    }

    @media screen and (max-width: 1104px) {
      .category-archive__if-i-speak__title {
        font-size: 12.5rem;
      }

      .category-archive__if-i-speak__image {
        width: 280px;
      }
    }

    @media screen and (max-width: 910px) {
      .category-archive__if-i-speak__title {
        font-size: 11.4rem;
      }

      .category-archive__if-i-speak__image {
        height: 190px;
        width: 290px;
      }

      .category-archive__if-i-speak__on-smaller {
        display: block;
      }

      .category-archive__if-i-speak__off-smaller {
        display: none;
      }

      .category-archive__if-i-speak__cta-container {
        text-align: left;
      }
    }

    @media screen and (max-width: 759px) {
      .category-archive__if-i-speak__title {
        font-size: 7rem;
      }

      .category-archive__if-i-speak__image {
        height: 160px;
        width: 100%;
      }
    }
  </style>
  <div>
    <section class="category-archive__if-i-speak__header container pt-4 pb-4 mb-4">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <h1 class="category-archive__if-i-speak__title mb-4 mb-s-0">If I Speak...</h1>
        </div>
        <div class="grid-item is-s-24 is-xxl-10 mt-4 font-size-12 font-size-s-11 font-weight-bold">
          <?php echo category_description(); ?>
        </div>
        <div class="grid-item is-s-12 is-xxl-10">
          <div class="category-archive__if-i-speak__image"></div>
        </div>
        <div class="grid-item is-s-12 is-xxl-4 mt-4 text-align-right">
          <?php
          if ( get_term_meta( $category->term_id, '_nm_podcast_url', true ) ) {
              $podcast_url = get_term_meta( $category->term_id, '_nm_podcast_url', true );
            ?>
          <a class="ui-button ui-button--red ui-button--auto-height mb-2" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
            <?php
          }
          ?>
        </div>
        <div class="grid-item is-xxl-24">
          <hr class="category-archive__if-i-speak__border">
        </div>
      </div>
    </section>
  </div>

  <div class="container">
    <div class="grid-row mb-4">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();

    get_template_part(
        'partials/post-layouts/flex-post',
        null,
        array(
            'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
            'image-size'        => 'col12-16to9',
        )
    );
  }
} else {
  ?>
    <article class="grid-item is-s-24"><?php esc_html_e( 'Sorry, nothing matched your criteria :/' ); ?></article>
  <?php
}
?>
    </div>
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
    </div>
  </div>
</main>
<?php
get_footer();
?>
