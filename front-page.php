<?php
get_header();

$show_special = IGV_get_option('_igv_show_special');

$home_radio = IGV_get_option('_igv_home_radio');

$fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');

$home_featured = IGV_get_option('_igv_front_feature');

$focus = IGV_get_option('_igv_home_focus');
$focus_at_top = IGV_get_option('_igv_home_focus_at_top');

$show_imo = IGV_get_option('_igv_show_imo');

?>

<!-- main content -->
<main id="main-content">
  <?php
    if ($home_radio) { // refactor/check this
      get_template_part('partials/radio-player');
    }

    get_template_part('partials/front-page/front-page-signups');
  ?>

  <?php
    /*
      getting the data here
      - 2 selected featured articles. fallback to most recent if not selected
      - 5 most recent articles excluding the 2 featured
      - most recent burner
      - most recent novarafm
      - selected sub featured article. if none most recent analysis article excluding existing 7 requested articles
      - most recent tysky
    */
  ?>

  <?php
    get_template_part('partials/support-section');
  ?>

  <section id="front-page-audio-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      // *** EXCLUDE THE BURNER

      $category_id = get_cat_ID('Audio');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Audio</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_fm = new WP_Query(array(
          'posts_per_page' => 8,
          'category_name' => 'Audio'
        ));

        if ($latest_fm->have_posts()) {
          while ($latest_fm->have_posts()) {
            $latest_fm->the_post();
            get_template_part('partials/post-layouts/post-col6');
          }
        }
      ?>
    </div>
  </section>

<!-- Tyksy Sour video block -->

  <section id="front-page-tysky-sour-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $video_category_slug = 'tyskysour-video';

      render_front_page_video_block($video_category_slug);
    ?>
  </section>

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
        $latest_articles = new WP_Query(array(
          'posts_per_page' => 8,
          'category_name' => 'Articles'
        ));

        if ($latest_articles->have_posts()) {
          $i = 0;
          while ($latest_articles->have_posts()) {
            $latest_articles->the_post();

            get_template_part('partials/post-layouts/home-articles-post-col6');

            if ($i === 3) {
              echo '</div><div class="row margin-bottom-small mobile-margin-bottom-none">';
            }

            $i++;
          }
        }
      ?>
    </div>
  </section>

  <section id="front-page-video-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $video_category_slug = 'video';
      $excluded_category_slug = 'tyskysour-video';

      render_front_page_video_block($video_category_slug, $excluded_category_slug);
    ?>
  </section>

  <?php
    get_template_part('partials/support-section');
  ?>

<!-- end main-content -->
</main>

<?php
get_footer();
?>