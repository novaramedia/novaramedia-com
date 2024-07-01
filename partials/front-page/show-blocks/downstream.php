<?php
  $has_red_background = false;
  $has_border_bottom = true;
?>
<div <?php if ($has_red_background) { echo 'class="background-red font-color-white"'; } ?>>
  <section class="container <?php if ($has_red_background) { echo 'pt-6 pb-6'; } else { echo 'mt-5 mb-5'; } ?>">
    <?php
      $downstream_category = get_term_by('slug', 'downstream', 'category');

    if ($downstream_category) {
      $category_link = get_category_link($downstream_category->term_id);
  ?>
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-5">
        <h4 class="fs-7 font-weight-regular"><a href="<?php echo $category_link; ?>"><strong>Downstream</strong> is an in-depth interview show featuring conversations with activists, authors, economists, politicians, scientists, philosophers and thinkers of all stripes.</a></h4>
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
        <div class="layout-thumbnail-frame">
          <div class="layout-thumbnail-frame__inner mt-1 ml-1">
            <?php render_post_ui_tags($post->ID, true, true, 'no-border'); ?>
          </div>
          <a href="<?php the_permalink(); ?>" class="ui-hover">
            <?php render_thumbnail($post->ID, 'col24-16to9', array(
              'class' => 'ui-rounded-image'
            )); ?>
          </a>
        </div>

        <div class="grid-row grid--nested mt-4">
          <?php
            $meta = get_post_meta($post->ID);

            $has_related = false;

            if (!empty($meta['_cmb_related_posts'])) {
              $related_args = array(
                'posts_per_page' => 1,
                'post__in' => explode(', ', $meta['_cmb_related_posts'][0]),
                'orderby' => 'rand',
              );

              $related_posts = new WP_Query($related_args);
              $has_related = $related_posts->have_posts();
            }

            $title_classes = $has_related ? 'is-m-24 is-xxl-16' : 'is-xxl-24';
          ?>
          <div class="grid-item <?php echo $title_classes; ?>">
            <a href="<?php the_permalink(); ?>" class="ui-hover">
              <h6 class="fs-8 fs-m-7"><?php the_title(); ?></h6>
              <h5 class="fs-6 mt-3 mt-s-2">
                <?php render_standfirst($post->ID); ?>
              </h5>
            </a>
          </div>
          <?php
          if ($has_related) {
            ?>
              <div class="grid-item is-m-24 is-xxl-8 ui-border-left ui-border--not-m mt-m-4">
                <?php render_see_also($related_posts); ?>
              </div>
            <?php
                wp_reset_postdata();
              }
          ?>
        </div>
      </div>
      <div class="grid-item is-s-24 is-l-10 is-xxl-8">
        <a href="<?php echo $category_link; ?>" class="ui-hover">
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
          <div class="grid-item is-xxl-12 mb-4">
            <div class="layout-thumbnail-frame">
              <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                <?php render_post_ui_tags($post->ID, false, true, 'no-border'); ?>
              </div>
              <a href="<?php the_permalink(); ?>" class="ui-hover">
                <?php render_thumbnail($post->ID, 'col24-16to9', array(
                  'class' => 'ui-rounded-image'
                )); ?>
              </a>
            </div>
            <a href="<?php the_permalink(); ?>" class="ui-hover">
              <h6 class="fs-3-sans font-bold mt-1"><?php the_title(); ?>. <?php render_standfirst($post->ID); ?></h6>
            </a>
          </div>
        <?php
          }
        }
        wp_reset_postdata();
        ?>
        </div>
      </div>
      <?php if ($has_border_bottom) { ?>
        <div class="grid-item is-xxl-24 mt-5">
          <hr />
        </div>
      <?php } ?>
    <?php
      }

      ?>
    </div>
  <?php
    }
    ?>
  </section>
</div>
