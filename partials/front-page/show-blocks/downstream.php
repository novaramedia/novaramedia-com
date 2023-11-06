<section class="container mt-6 mb-6">
  <?php
    $downstream_category = get_term_by('slug', 'downstream', 'category');

  if ($downstream_category) {
    $category_link = get_category_link($downstream_category->term_id);
?>
  <div class="grid-row">
    <div class="grid-item is-s-24 mb-3">
      <h4 class="fs-3-sans font-uppercase font-weight-bold"><a href="<?php echo $category_link; ?>">Downstream</a></h4>
    </div>
  </div>

  <div class="grid-row">
    <?php
      $args = array(
        'posts_per_page' => 5,
        'cat' => $downstream_category->term_id,
      );

      $latest_video = new WP_Query($args);

      if ($latest_video->have_posts()) {
      $latest_video->the_post();
    ?>
    <div class="grid-item is-s-24 is-xxl-14 mb-s-5">
      <a href="<?php the_permalink(); ?>">
        <?php render_thumbnail($post->ID, 'col24-16to9', array(
          'class' => 'ui-rounded-image',
        )); ?>
      </a>

      <a href="<?php the_permalink(); ?>">
        <h6 class="js-fix-widows fs-7 mt-2"><?php the_title(); ?></h6>
        <h5 class="fs-5-sans mt-2">
          <?php render_standfirst($post->ID); ?>
        </h5>
        <p class="fs-3-sans mt-2 mb-0">
          <?php render_short_description($post->ID); ?>
        </p>
      </a>
    </div>
    <div class="grid-item is-s-24 is-xxl-10">
      <div class="grid-row grid--nested">
      <?php

      if ($latest_video->have_posts()) {
        while($latest_video->have_posts()) {
          $latest_video->the_post();
          $meta = get_post_meta($post->ID);
      ?>
        <div class="grid-item is-xxl-12 mb-4">
          <a href="<?php the_permalink(); ?>">
            <?php render_thumbnail($post->ID, 'col24-16to9', array(
              'class' => 'ui-rounded-image',
            )); ?>
            <h6 class="js-fix-widows fs-4-sans mt-1"><?php the_title(); ?></h6>
            <h5 class="js-fix-widows fs-2 font-uppercase mt-1"><?php render_standfirst($post->ID); ?></h5>
          </a>
        </div>
      <?php
        }
      }
      wp_reset_postdata();
      ?>
      </div>
    </div>
  <?php
    }

    ?>
  </div>
<?php
  }
  ?>
</section>
