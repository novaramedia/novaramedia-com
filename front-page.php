<?php
get_header();
?>

<!-- main content -->
<main id="main-content">

  <section id="home-wire-posts" class="container margin-bottom-large">
    <?php
      $category_id = get_cat_ID('Wire');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Wire</a></h4>
      </div>
    </div>

    <div class="row margin-bottom-small">
      <?php
        $latest_wire = new WP_Query(array(
          'posts_per_page' => 7,
          'category_name' => 'Wire'
        ));

        if ($latest_wire->have_posts()) {
          $i = 0;
          while ($latest_wire->have_posts()) {
            $latest_wire->the_post();

            if ($i === 0) {
              get_template_part('partials/post-layouts/post-col12');
            } else {
              get_template_part('partials/post-layouts/home-wire-post-col6');
            }

            if ($i === 2) {
              echo '</div><div class="row margin-bottom-small">';
            }

            $i++;
          }
        }
      ?>
    </div>
  </section>

  <section id="home-tv-posts" class="container margin-bottom-large">
    <?php
      $category_id = get_cat_ID('TV');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">TV</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_tv = new WP_Query(array(
          'posts_per_page' => 4,
          'category_name' => 'TV'
        ));

        if ($latest_tv->have_posts()) {
          pr($latest_tv->posts[0]);
        }
      ?>
    </div>
  </section>

  <section id="home-fm-posts" class="container margin-bottom-large">
    <?php
      $category_id = get_cat_ID('FM');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">FM</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_fm = new WP_Query(array(
          'posts_per_page' => 4,
          'category_name' => 'FM'
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
    $focus = IGV_get_option('_igv_home_focus');

    if ($focus) {

      $focus_object = get_term($focus);
      $focus_link = get_term_link($focus_object);
?>
  <section id="home-focus-posts" class="container margin-bottom-large">
    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $focus_link; ?>">Focus on <?php echo $focus_object->name; ?></a></h4>
      </div>
    </div>

    <div class="row margin-bottom-small">
<?php
      $focusPosts = new WP_Query(array(
        'posts_per_page' => -1,
        'tax_query' => array(
          array(
            'taxonomy' => 'focus',
            'field' => 'term_id',
            'terms' => $focus
          ),
        ),
      ));
      if ($focusPosts->have_posts()) {
        $i = 0;
        while ($focusPosts->have_posts()) {
          $focusPosts->the_post();

          if ($i % 3 === 0 && $i !== 0) {
            echo "</div>\n<div class=\"row margin-bottom-small\">";
          }

          get_template_part('partials/post-layouts/home-focus-post-col8');

          $i++;
        }
      }
?>
    </div>
  </section>
 <?php
    }
  ?>

  <section id="home-long-read-posts" class="container margin-bottom-large">
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

  <section id="home-imo-posts" class="container margin-bottom-large">
    <div class="row">
      #imo?!
    </div>
  </section>

<!-- end main-content -->
</main>

<?php
get_footer();
?>