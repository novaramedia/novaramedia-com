<div class="background-red font-color-white">
  <section class="container pt-6 pb-6">
    <?php
      $downstream_category = get_term_by('slug', 'downstream', 'category');

    if ($downstream_category) {
      $category_link = get_category_link($downstream_category->term_id);
  ?>
    <div class="grid-row">
      <div class="grid-item is-s-24 is-l-24 is-xxl-18 mb-5">
        <h4 class="fs-6 font-weight-regular"><a href="<?php echo $category_link; ?>"><strong>Downstream</strong> is an in depth interview show featuring conversations with activists, authors, economists, politicians, scientists, philosophers and thinkers of all stripes. Produced by leftists—but made for everyone.</a></h4>
      </div>
    </div>

    <div class="grid-row">
      <?php
        $args = array(
          'posts_per_page' => 7,
          'cat' => $downstream_category->term_id,
        );

        $latest_video = new WP_Query($args);

        if ($latest_video->have_posts()) {
        $latest_video->the_post();
      ?>
      <div class="grid-item is-s-24 is-l-14 is-xxl-16 mb-s-5">
        <a href="<?php the_permalink(); ?>">
          <div class="layout-thumbnail-frame">
            <div class="layout-thumbnail-frame__inner mt-1 ml-1">
              <?php render_post_ui_tags($post->ID, true, true, true); ?>
            </div>
            <?php render_thumbnail($post->ID, 'col24-16to9', array(
              'class' => 'ui-rounded-image'
            )); ?>
          </div>
        </a>

        <a href="<?php the_permalink(); ?>">
          <h6 class="js-fix-widows fs-8 mt-4"><?php the_title(); ?></h6>
          <h5 class="fs-6 mt-3">
            <?php render_standfirst($post->ID); ?>
          </h5>
        </a>
      </div>
      <div class="grid-item is-s-24 is-l-10 is-xxl-8">
        <div class="grid-row grid--nested">
        <?php

        if ($latest_video->have_posts()) {
          while($latest_video->have_posts()) {
            $latest_video->the_post();
            $meta = get_post_meta($post->ID);
        ?>
          <div class="grid-item is-xxl-12 mb-5">
            <a href="<?php the_permalink(); ?>">
              <div class="layout-thumbnail-frame">
                <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                  <?php render_post_ui_tags($post->ID, false, true, true); ?>
                </div>
                <?php render_thumbnail($post->ID, 'col24-16to9', array(
                  'class' => 'ui-rounded-image'
                )); ?>
              </div>
              <h6 class="js-fix-widows fs-3-sans font-bold mt-1"><?php the_title(); ?></h6>
              <h5 class="js-fix-widows fs-2 mt-1"><?php render_standfirst($post->ID); ?></h5>
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
</div>
