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
  ?>

  <!-- Novara Live video block -->
  <section id="front-page-novara-live-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $video_category_slug = 'novara-live';

      render_front_page_video_block($video_category_slug);
    ?>
  </section>

  <?php
    render_front_page_banner($banners[1]);
  ?>

  <!-- Below the fold articles block -->
  <section id="front-page-articles-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      // *** EXCLUDE ALREADY SHOWN ABOVE

      $category_id = get_cat_ID('Articles');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Articles</a></h4>
      </div>
    </div>

    <div class="row margin-bottom-small mobile-margin-bottom-none">
      <?php
        // we will need to parse already shown IDs to exclude them from the query

        // $latest_articles = new WP_Query(array(
        //   'posts_per_page' => 8,
        //   'category_name' => 'Articles',
        //   'post__not_in' => array_merge($featured_posts_ids, $recent_articles_ids)
        // ));

        // if ($latest_articles->have_posts()) {
        //   $i = 0;
        //   while ($latest_articles->have_posts()) {
        //     $latest_articles->the_post();

        //     get_template_part('partials/front-page/front-page-article-default');

        //     if ($i === 3) {
        //       echo '</div><div class="row margin-bottom-small mobile-margin-bottom-none">';
        //     }

        //     $i++;
        //   }
        // }
      ?>
    </div>
  </section>

  <?php
    render_front_page_banner($banners[2]);
  ?>

  <!-- Audio block -->
  <section id="front-page-audio-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $audio_category = get_term_by('slug', 'audio', 'category');
      $burner_category = get_term_by('slug', 'the-burner', 'category');
      $burner_category_id = !empty($burner_category) ? array($burner_category->term_id) : array();

      $category_link = get_category_link( $audio_category->term_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Audio</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_audio = new WP_Query(array(
          'posts_per_page' => 8,
          'cat' => $audio_category->term_id,
          'category__not_in' => $burner_category_id
        ));

        if ($latest_audio->have_posts()) {
          $i = 0;
          while ($latest_audio->have_posts()) {
            $latest_audio->the_post();
            get_template_part('partials/front-page/front-page-audio-default');

            if ($i === 3) {
              echo '</div><div class="row margin-bottom-small mobile-margin-bottom-none">';
            }

            $i++;
          }
        }
      ?>
    </div>
  </section>

  <!-- non-Novara Live video block -->
  <section id="front-page-video-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $video_category_slug = 'video';
      $excluded_category_slug = 'novara-live';

      render_front_page_video_block($video_category_slug, $excluded_category_slug);
    ?>
  </section>

  <?php
    render_front_page_banner($banners[3]);
  ?>

<!-- end main-content -->
</main>

<?php
get_footer();
?>
