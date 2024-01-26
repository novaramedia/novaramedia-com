<?php
get_header();

$category = get_category(get_query_var('cat'));

$podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : false;
$podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);

$podcast_copy = !empty($podcast_copy_override) ? $podcast_copy_override : 'Subscribe to the podcast';
?>
<main id="main-content" class="category-archive category-archive__if-i-speak">
  <style type="text/css">
    .category-archive__if-i-speak__header {
      color: rgb(220, 0, 5);
    }

    .category-archive__if-i-speak__header .nm-button {
      border-color: rgb(220, 0, 5);
      color: rgb(220, 0, 5);
    }

    .category-archive__if-i-speak__header .nm-button:hover {
      background-color: rgb(220, 0, 5);
      color: rgb(255, 255, 255);
    }

    .category-archive__if-i-speak__title {
      font-size: 18.5rem;
      line-height: .9;
      letter-spacing: -0.03em;
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
      border: 1px solid rgb(220, 0, 5);
      margin-top: 0;
    }

    .avif .category-archive__if-i-speak__image, .webp .category-archive__if-i-speak__image {
      background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/banners/if-i-speak.webp'; ?>);
    }

    .fallback .category-archive__if-i-speak__image {
      background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/banners/if-i-speak.png'; ?>);
    }

    @media screen and (max-width: 1336px) {
      .category-archive__if-i-speak__title {
        font-size: 14.5rem;
      }

      .category-archive__if-i-speak__image {
        height: 200px;
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
        height: 180px;
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
    <section class="category-archive__if-i-speak__header container padding-top-small padding-bottom-small margin-bottom-small">
      <div class="flex-grid-row">
        <div class="flex-grid-item flex-item-s-12">
          <h1 class="category-archive__if-i-speak__title">If I Speak...</h1>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-xxl-5 margin-top-basic font-weight-bold font-size-2 font-size-l-1">
          <?php echo category_description(); ?>
        </div>
        <div class="flex-grid-item flex-item-s-6 flex-item-xxl-5">
          <div class="category-archive__if-i-speak__image"></div>
        </div>
        <div class="flex-grid-item flex-item-s-6 flex-item-xxl-2 margin-top-small text-align-right">
          <?php
            if (get_term_meta($category->term_id, '_nm_podcast_url', true)) {
              $podcast_url = get_term_meta($category->term_id, '_nm_podcast_url', true);
          ?>
          <a class="nm-button nm-button--white nm-button--inline margin-bottom-tiny" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
          <?php
            }
          ?>
        </div>
        <div class="flex-grid-item flex-item-xxl-12">
          <hr class="category-archive__if-i-speak__border">
        </div>
      </div>
    </section>
  </div>

  <div class="container">
    <div class="flex-grid-row margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4 margin-bottom-basic',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="flex-grid-item flex-item-s-12"><?php _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </div>
</main>
<?php
get_footer();
?>
