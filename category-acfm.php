<?php
get_header();

$category = get_category(get_query_var('cat'));

$podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : false;
$podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);

$podcast_copy = !empty($podcast_copy_override) ? $podcast_copy_override : 'Subscribe to the podcast';
?>
<main id="main-content" class="category-archive category-archive__acfm">
  <style type="text/css">
    .category-archive__acfm__logo {
      width: auto;
      max-height: 200px;
    }

    @media screen and (max-width: 1104px) {
      .category-archive__acfm__logo {
        max-height: 180px;
      }
    }

    @media screen and (max-width: 910px) {
      .category-archive__acfm__logo {
        max-height: 120px;
      }
    }

    @media screen and (max-width: 759px) {
      .category-archive__acfm__logo {
        max-height: 120px;
      }
    }
  </style>
  <div style="background-color: #FC16CB">
    <section class="container padding-top-small padding-bottom-small margin-bottom-small font-color-white">
      <div class="flex-grid-row">
        <div class="flex-grid-item flex-item-s-8 flex-item-xxl-4 margin-top-small font-weight-bold font-size-2 font-size-m-1">
          <?php echo category_description(); ?>
        </div>
        <div class="flex-grid-item flex-item-s-4 flex-item-xxl-4 text-align-center">
          <svg class="category-archive__acfm__logo" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 273 276"><path fill="#0FB8FA" d="M272.3 268.8c-.3-22.1.2-44.2-.6-66.3-.5-13.7-.3-27.3-.6-41-.9-47.2 1.4-94.5-1.4-141.7-.3-5.5.3-11.1-.6-16.6-.4-2.3-.9-2.6-2.8-1.9-4.9 1.8-9.5 4.3-14.3 6.2-8 3.3-16.1 6.4-24 9.8-7.2 3.2-14.5 6.2-21.8 9.2-7 3-14.1 5.9-21.1 8.9-3 1.3-5.9 2.9-9 3.9-5.6 1.6-10.6 4.5-15.9 6.6-5.5 2.1-10.7 4.8-16.2 6.8-3.3 1.2-6 3.7-9.9 4.1-3.9.4-7.4 2.7-11 4.1-8.6 3.6-17.4 6.8-25.9 10.6-8.7 3.9-17.5 7.8-26.3 11.5-9.4 4-18.9 7.7-28.3 11.8-6.1 2.6-31.5 13.6-37.5 16.6-2.7 1.4-3.8 1.1-4.8 5.8-.7 3.4.1 48.5.1 48.5 0 4.7 5.2 7.8 9.9 9.3 4.5 1.8 9.3 3.1 13.7 5.1 4.3 2 9 2.9 13.2 5.3 4 2.2 8.6 3.2 12.9 5 5.8 2.4 11.6 4.5 17.4 6.8 9.2 3.6 18.5 6.8 27.7 10.4 1.9.7 4 1.2 5.7 2.1 1.2.6 1.8-1.4 2.4-.3.8 1.4 2 2 3.4 2.5 3.9 1.4 7.9 2.6 11.6 4.4 4.8 2.3 9.8 4.2 14.7 5.9 6.3 2.2 12.5 4.6 18.7 7.1 4.2 1.7 8.5 2.7 12.6 4.7 6.1 2.9 12.7 4.9 19 7.5 3.6 1.5 7.4 2.6 11 4.3 3.1 1.4 6.5 2.5 9.8 3.8 3.6 1.4 7.3 2.5 10.8 4.2 3.1 1.5 6.8 1.6 9.8 3.5 1.5.9 3.4 1.3 4.9 2.1 3.8 1.9 8.1 1.9 11.7 4.5 3.2 2.4 7.5 2.7 11.2 4.4 4.2 1.8 8.6 3.2 12.8 5.2 6.7 2.8 7.1 2.5 7-4.7Z"/><path fill="#FBFD15" d="M184.2 224.5c-4.8-2.7-9.8-3.6-14.4-5.5-1-.4-1.8-.5-1.7-2.2.7-21 1.3-42 1.9-63 .7-25 1.6-50.1 2-75.1.1-5.6-.1-11.1-.1-16.7 0-4 1.3-7 5.4-8.3 2.9-.9 5.3-2.9 8.6-2.7 1.5.1 2.1.6 2.3 2.1 1.4 10.2 4.2 20 6.2 30.1 1.1 5.4 2.2 10.9 3.7 16.2 3.7 13 5.8 26.3 9.1 39.4.1.4.2.8.4 1.5 3.3-4.5 4.3-9.6 5.5-14.4 2.4-9.3 5.2-18.5 7.1-27.8 2.3-11.8 5.3-23.5 8.2-35.2 2.3-9.4 4.4-18.8 6.4-28.2.6-2.7 3-3.8 4.7-4.4 3.3-1.2 6.4-3.1 10-3.7 1.7-.3 2.5-.2 2.5 1.7v.7c0 13.9.6 27.7.9 41.6.2 9.5-.8 19.2.8 28.4 1.3 7.1-.3 13.8.2 20.7 2.4 34.8 1.8 69.6 3 104.5.3 8.9 0 17.9 0 26.3-7.8-1.6-14.6-6.7-22.6-8.9-1.2-.3-1.6-1.7-1.4-2.9 1-5.2.8-10.3 0-15.5-.2-1.2 0-2.4 0-3.6V190c0-12.8-.6-25.6-1.1-38.5-.1-3 1.3-6 .8-9.2-2 1.5-2.7 3.6-3.3 6-2.3 9.2-6.5 17.8-9.2 26.9-1.8 5.9-4.6 11.2-7.2 16.6-1.4 3-3.1 5.8-5.6 8-1 .9-1.6 1.3-2.3-.3-3.7-7.7-6.7-15.6-8.3-24-1.1-5.7-2.9-11.2-4.7-16.7-1.1-3.5-2.1-7.1-2.4-10.8-.1-1-.2-1.6-1.4-1.7-1.5-.1-1.3.8-1.2 1.7.4 6.1-.7 12.1-.7 18.2-.2 14.9-1.3 29.8-1.4 44.7-.2 4.2-.4 8.7-.7 13.6ZM11.8 161.2c-.1-5.5 1.5-10.5 2.8-15.4 1.8-6.6 2.1-13.6 4.6-20.1.1-.3.2-.6.2-1 1.5-6.7 5.7-10.8 12.1-12.9 3.9-1.3 7.7-3 11.8-3.9 4.8-1 7.8.9 8.7 5.7 1 5.7 1.8 11.4 2.7 17.1 2.4 15.1 4.8 30.2 7.1 45.3.2 1.1.9 2.4-.4 3.3-1.1.8-2.3.2-3.4-.2l-12.9-4.8c-1.7-.6-2.5-1.7-2.3-3.4.1-.9.1-1.8 0-2.6-.6-7.1-.6-7.1-7.8-8.6-5.7-1.2-5.7-1.2-6.3 4.3-.6 5.7-.6 5.7-6.1 3.8-2.6-.9-5.1-2-7.8-2.5-2.6-.4-3.5-1.8-3-4.1Zm18.7-19.8c-.1 3.1-.1 3.2 3.1 3.8 2 .4 4.1.6 6.1.9 1.3.2 2-.2 1.9-1.7-.5-5.8-.8-11.5-1.3-17.3-.1-1.1-.5-2.6-1.7-3-2.1-.7-6.1 2.3-6.6 4.7-.8 4.2-1.3 8.4-1.5 12.6Zm109.9 67.7c-4.6-2.1-8.7-3.5-12.9-4.8-3.3-1-4.1-3.2-4-6.9.2-5.7-.2-11.6-.3-17.3-.3-24.9.1-49.9-.3-74.8-.1-8.8-.4-17.6-.5-26.4 0-2.7.2-4.2 3.7-5.3 8.6-2.9 16.9-6.8 25.2-10.5 2.1-.9 4.4-.8 6.3-2.7 1.4-1.4 4.4.6 4.5 2.8.2 5.1.3 10.3.4 15.4.1 2.3-1.2 4.1-3.2 4.4-4.7.7-8.7 3.5-13.3 4.4-2.2.4-4.5 1.9-6.1 3.6-.8.9-.3 3.1-.3 4.8 0 8.5.1 17.1 0 25.6 0 2.4.4 3.1 2.9 2.1 3.1-1.2 6.4-1.4 9.8-.9 1.7.3 1.9.9 1.9 2.2.1 5 .2 10.1.4 15.1.1 1.7-.5 2.3-2.2 2.4-3.6.2-7.2.5-10.7.1-1.3-.1-2.2 0-2.2 1.8.1 8.8-.5 17.6.2 26.3 1 12.6 0 25.3.7 38.6Zm-71.5-69.6c0-10.6.3-21.2-.1-31.8-.3-7.1 3.1-11.3 9.1-13.8 6.9-2.8 13.7-5.9 20.7-8.1 6.5-2.1 10.1-.1 10.8 6.6.6 6.8.3 13.6.5 20.3.1 2.9-.7 5.4-4.5 5.3-1.9 0-3.9.3-5.8.9-2.7.8-3.3-.5-3.5-2.7-.1-2.2 0-4.4-.4-6.5-.3-1.2-.8-2-2.1-2.1-2.2-.3-6.7 3-6.9 5.3-.2 2.3-.2 4.6-.2 6.9 0 16 0 31.9-.1 47.9 0 4 5.2 7.2 8.6 5.3 1.1-.6 1.4-1.7 1.4-2.8.1-1.6 0-3.3 0-4.9 0-2 .6-3.2 3.1-3.2 3-.1 6.1-.7 9.1-1.1 1.9-.3 3 0 2.9 2.5-.2 8.5-.2 17.1-.4 25.6-.1 3.3-2.7 5.6-6 5.4-4.9-.3-9.3-2.5-13.9-3.7-5.4-1.5-10.7-3-15.6-5.6-4.4-2.3-6.7-6.3-6.7-11.2-.1-11.7 0-23.1 0-34.5Z"/></svg>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-xxl-4 margin-top-small">
          <?php
            if (get_term_meta($category->term_id, '_nm_podcast_url', true)) {
              $podcast_url = get_term_meta($category->term_id, '_nm_podcast_url', true);
          ?>
          <a class="nm-button nm-button--white margin-bottom-tiny" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
          <?php
            }
          ?>
          <a class="nm-button nm-button--white" href="https://novara.media/ACFMnewsletter" target="_blank" rel="nofollow">Sign up to the mailing list</a>
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
