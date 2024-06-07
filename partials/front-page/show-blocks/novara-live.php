<?php
  $novara_life_category = get_term_by('slug', 'novara-live', 'category');

  if ($novara_life_category) {
    $category_link = get_category_link($novara_life_category->term_id);
    $posts_to_show = 17;

    $args = array(
      'posts_per_page' => $posts_to_show,
      'cat' => $novara_life_category->term_id,
    );

    $latest_video = new WP_Query($args);
?>
<div class="background-black font-color-white">
  <section class="front-page-novara-live container pt-6 pb-6 pt-s-5 pb-s-5">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-4">
        <a href="<?php echo $category_link; ?>">
          <div class="layout-split-level">
            <h3 class="fs-7 layout-flex-no-shrink mr-4"><span class="ui-dot ui-dot--red"></span>Novara Live</h3>
            <div class="layout-flex-grow layout-overflow-hidden fs-7 font-weight-regular">
              <div class="ui-ticker">
                <div class="ui-ticker__fade-left"></div>
                <div class="ui-ticker__fade-right"></div>
                <div class="ui-ticker__inner">
                  <div class="ui-ticker__item">The biggest stories and guests from the UK and international left. Livestreamed on YouTube weeknights at 6PM GMT.</div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="grid-item is-s-24 is-l-15 is-xl-17 is-xxl-18">
        <?php
          if ($latest_video->have_posts()) {
            $latest_video->the_post();
            $meta = get_post_meta($post->ID);
        ?>
          <div class="grid-row grid--nested">
            <div class="grid-item is-l-24 is-xxl-16">
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
            </div>
            <div class="grid-item is-l-24 is-xxl-8 mt-l-3">
              <a href="<?php the_permalink(); ?>" class="ui-hover">
                <h6 class="fs-7"><?php the_title(); ?></h6>
                <p class="fs-4-sans font-weight-regular mt-3">
                  <?php render_short_description($post->ID); ?>
                </p>
              </a>
              <?php
                if (!empty($meta['_cmb_related_posts'])) {
                  $related_args = array(
                    'posts_per_page' => 1,
                    'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
                  );

                  $related_posts = new WP_Query($related_args);

                  if ($related_posts->have_posts()) {
                ?>
                  <h4 class="fs-2 font-uppercase ui-border-top pt-3 mt-3 mb-2">See Also</h4>
                <?php
                    while ($related_posts->have_posts()) {
                      $related_posts->the_post();
                ?>
                    <div class="mb-2">
                      <a href="<?php the_permalink(); ?>" class="ui-hover">
                        <h5 class="fs-4-sans"><?php the_title(); ?></h5>
                        <h6 class="fs-2 font-uppercase mt-1"><?php render_bylines($post->ID, false); ?></h6>
                      </a>
                    </div>
                <?php
                    }
                  }
                  wp_reset_postdata();
                }
            ?>
            </div>
          </div>
        <?php
          }
        ?>
      </div>
      <div class="grid-item is-s-24 is-l-9 is-xl-7 is-xxl-6 mt-s-5">
        <a href="<?php echo $category_link; ?>">
          <div class="layout-split-level fs-2 mb-4">
            <h5 class="font-bold font-uppercase">Full Episodes</h5>
            <span>See All</span>
          </div>
        </a>
        <div class="front-page-novara-live__posts-scroller ux-scroller">
          <div class="front-page-novara-live__posts-scroller__fade-top ux-scroller__fade-top"></div>
          <div class="front-page-novara-live__posts-scroller__fade-bottom ux-scroller__fade-bottom"></div>
          <div class="front-page-novara-live__posts-scroller__inner ux-scroller__inner">
          <?php
            if ($latest_video->have_posts()) {
              $i = 1;
              while($latest_video->have_posts()) {
                $latest_video->the_post();
                $meta = get_post_meta($post->ID);
                $timestamp = get_post_time('c');
          ?>
            <div class="pb-3 mb-3 <?php if ($i < $posts_to_show - 1) {echo 'ui-border-bottom';} ?>">
              <div class="grid-row grid--nested">
                <div class="grid-item is-s-10 is-xxl-8">
                  <a href="<?php the_permalink(); ?>" class="ui-hover">
                    <?php render_thumbnail($post->ID, 'col24-16to9', array(
                      'class' => 'ui-rounded-image'
                    )); ?>
                  </a>
                </div>
                <div class="grid-item is-s-14 is-xxl-16">
                  <div class="layout-split-level fs-2 mb-1">
                    <?php render_post_ui_tags($post->ID, false, true, 'no-fill--white'); ?>
                    <a href="<?php the_permalink(); ?>" class="ui-hover"><?php if ($i < 6) { ?>
                      <span class="js-time-since" data-timestamp="<?php echo $timestamp; ?>"></span>
                    <?php } else { ?>
                      <span><?php the_time('j F Y'); ?></span>
                    <?php } ?></a>
                  </div>
                  <a href="<?php the_permalink(); ?>" class="ui-hover">
                    <h4 class="post__title fs-2 fs-s-4-sans font-bold">
                      <?php the_title(); ?>
                    </h4>
                  </a>
                </div>
              </div>
            </div>
          <?php
                $i++;
              }
            }
          ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php
  }
