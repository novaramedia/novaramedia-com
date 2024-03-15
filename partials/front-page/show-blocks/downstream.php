<div class="background-red font-color-white">
  <section class="container pt-6 pb-6">
    <?php
      $downstream_category = get_term_by('slug', 'downstream', 'category');

    if ($downstream_category) {
      $category_link = get_category_link($downstream_category->term_id);
  ?>
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-5">
        <h4 class="fs-7 font-weight-regular"><a href="<?php echo $category_link; ?>"><strong>Downstream</strong> is an in depth interview show featuring conversations with activists, authors, economists, politicians, scientists, philosophers and thinkers of all stripes. Produced by leftists—but made for everyone.</a></h4>
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
              <?php render_post_ui_tags($post->ID, true, true, 'no-border'); ?>
            </div>
            <?php render_thumbnail($post->ID, 'col24-16to9', array(
              'class' => 'ui-rounded-image'
            )); ?>
          </div>
        </a>

        <div class="grid-row grid--nested mt-4">
          <?php
            $meta = get_post_meta($post->ID);

            $has_related = false;

            if (!empty($meta['_cmb_related_posts'])) {
              $related_args = array(
                'posts_per_page' => 2,
                'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
              );

              $related_posts = new WP_Query($related_args);
              $has_related = $related_posts->have_posts();
            }

            $title_classes = $has_related ? 'is-m-24 is-xxl-16' : 'is-xxl-24';
          ?>
          <div class="grid-item <?php echo $title_classes; ?>">
            <h6 class="js-fix-widows fs-8"><?php the_title(); ?></h6>
            <h5 class="fs-6 mt-3">
              <?php render_standfirst($post->ID); ?>
            </h5>
          </div>
          <?php
          if ($has_related) {
            ?>
              <div class="grid-item is-m-24 is-xxl-8">
                <h4 class="fs-2 font-uppercase mb-2">See Also</h4>
            <?php
                while ($related_posts->have_posts()) {
                  $related_posts->the_post();
            ?>
                <div class="mb-2">
                  <a href="<?php the_permalink(); ?>">
                    <h5 class="fs-4-sans"><?php the_title(); ?></h5>
                    <h6 class="fs-2 font-uppercase mt-1"><?php render_bylines($post->ID, false); ?></h6>
                  </a>
                </div>
            <?php
                }
            ?>
              </div>
            <?php
                wp_reset_postdata();
              }
          ?>
        </div>
      </div>
      <div class="grid-item is-s-24 is-l-10 is-xxl-8">
        <a href="<?php echo $category_link; ?>">
          <div class="layout-split-level fs-2 mb-4">
            <h5 class="font-bold font-uppercase">Recent Episodes</h5>
            <span>See All</span>
          </div>
        </a>
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
              <h6 class="js-fix-widows fs-3-sans font-bold mt-1"><?php the_title(); ?>. <?php render_standfirst($post->ID); ?></h6>
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
