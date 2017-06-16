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
  if ($home_radio) {
    get_template_part('partials/radio-player');
  }

  if ($show_special) {

    get_template_part('partials/home-special');

  } else if ($fundraiser_expiration > time()) {

    get_template_part('partials/home-fundraiser');

  } else if (!empty($home_featured)) {
    $home_featured_ids = explode(', ', $home_featured);
    $post_id = $home_featured_ids[0];

    // Check for alt_thumb first
    $thumb_id = get_post_meta($post_id, '_cmb_alt_thumb_id', true);

    // If alt_thumb is empty, fallback to default thumb
    if (empty($thumb_id)) {
      $thumb_id = get_post_thumbnail_id($post_id);
    }
?>
  <section id="home-featured" class="container margin-bottom-large mobile-margin-bottom-basic">
    <div class="row">
       <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo get_permalink($post_id); ?>">Featured</a></h4>
      </div>
    </div>
    <div class="row">
      <a href="<?php echo get_permalink($post_id); ?>">
        <article id="featured-post" class="col col24">
          <?php
            echo wp_get_attachment_image($thumb_id, 'col24-featured-crop', null, array('class' => 'featured-post-thumbnail only-desktop'));
            echo wp_get_attachment_image($thumb_id, 'col24-mobile-featured-crop', null, array('class' => 'featured-post-thumbnail only-mobile'));
          ?>
          <h1 id="featured-post-title" class="text-align-center font-color-white text-shadow-light-gray u-flex-center js-fix-widows"><?php echo get_the_title($post_id); ?></h1>
        </article>
      </a>
    </div>
  </section>
<?php
  }

  if ($focus && $focus_at_top) {
    render_home_focus($focus);
  }
  ?>

  <section id="home-articles-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
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
          'posts_per_page' => 7,
          'category_name' => 'Articles'
        ));

        if ($latest_articles->have_posts()) {
          $i = 0;
          while ($latest_articles->have_posts()) {
            $latest_articles->the_post();

            if ($i === 0) {
              get_template_part('partials/post-layouts/post-col12');
            } else {
              get_template_part('partials/post-layouts/home-articles-post-col6');
            }

            if ($i === 2) {
              echo '</div><div class="row margin-bottom-small mobile-margin-bottom-none">';
            }

            $i++;
          }
        }
      ?>
    </div>
  </section>

  <section id="home-video-posts" class="container margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $category_id = get_cat_ID('Video');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Video</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_video = new WP_Query(array(
          'posts_per_page' => 4,
          'category_name' => 'Video'
        ));

        render_video_query($latest_video);
      ?>
    </div>
  </section>

  <section id="home-fm-posts" class="container margin-bottom-large mobile-margin-bottom-basic">
    <?php
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
          'posts_per_page' => 4,
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

  <?php get_template_part('partials/support-section'); ?>

  <?php
    if ($focus && !$focus_at_top) {
      render_home_focus($focus);
    }
  ?>

  <section id="home-long-read-posts" class="container margin-top-large margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $category_id = get_cat_ID('Long Read');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Long Reads</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_long_reads = new WP_Query(array(
          'posts_per_page' => 3,
          'category_name' => 'Long Read'
        ));

        if ($latest_long_reads->have_posts()) {
          while ($latest_long_reads->have_posts()) {
            $latest_long_reads->the_post();
            get_template_part('partials/post-layouts/post-col8');
          }
        }
      ?>
    </div>
  </section>

<?php
  if ($show_imo) {
?>
  <section id="home-imo-posts" class="container margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $category_id = get_cat_ID('imobastani');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">#IMOBastani</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_imo = new WP_Query(array(
          'posts_per_page' => 4,
          'category_name' => 'imobastani'
        ));

        render_video_query($latest_imo);
      ?>
    </div>
  </section>
<?php
  }
?>


<!-- end main-content -->
</main>

<?php
get_footer();
?>