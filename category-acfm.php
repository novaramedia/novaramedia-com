<?php
get_header();

$category = get_category(get_query_var('cat'));

$podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : false;
$podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);

$podcast_copy = !empty($podcast_copy_override) ? $podcast_copy_override : 'Subscribe to the podcast';
?>
<main id="main-content" class="category-archive category-archive__acfm">
  <style type="text/css">
    .category-archive__acfm__logo svg {
      width: auto;
      max-height: 200px;
    }

    @media screen and (max-width: 1104px) {
      .category-archive__acfm__logo svg {
        max-height: 180px;
      }
    }

    @media screen and (max-width: 910px) {
      .category-archive__acfm__logo svg {
        max-height: 120px;
      }
    }

    @media screen and (max-width: 759px) {
      .category-archive__acfm__logo svg {
        max-height: 120px;
      }
    }
  </style>
  <div style="background-color: #FC16CB">
    <section class="container pt-5 pb-5 mb-4 font-color-white">
      <div class="grid-row">
        <div class="grid-item is-s-16 is-xxl-8 mt-4 fs-6 text-paragraph-breaks">
          <?php echo category_description(); ?>
        </div>
        <div class="category-archive__acfm__logo grid-item is-s-8 is-xxl-8 text-align-center">
          <?php echo nm_get_file('/dist/img/products/acfm/acfm-logo.svg'); ?>
        </div>
        <div class="grid-item is-s-24 is-xxl-8 mt-4">
          <?php
            if (get_term_meta($category->term_id, '_nm_podcast_url', true)) {
              $podcast_url = get_term_meta($category->term_id, '_nm_podcast_url', true);
          ?>
          <a class="nm-button nm-button--white mb-3" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
          <?php
            }
          ?>
          <a class="nm-button nm-button--white" href="https://novara.media/ACFMnewsletter" target="_blank" rel="nofollow">Sign up to the mailing list</a>
        </div>
      </div>
    </section>
  </div>

  <div class="container">
    <div class="grid-row mb-4">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="grid-item is-s-24"><?php _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
} ?>
    </div>
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </div>
</main>
<?php
get_footer();
?>
