<?php
  $novara_life_category = get_term_by('slug', 'novara-live', 'category');

  if ($novara_life_category) {
    $category_link = get_category_link($novara_life_category->term_id);

    $args = array(
      'posts_per_page' => 14,
      'cat' => $novara_life_category->term_id,
    );

    $latest_video = new WP_Query($args);
?>
<div class="background-black font-color-white">
  <section class="front-page-novara-live container pt-6 pb-6">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-5">
        <a href="<?php echo $category_link; ?>">
          <div class="layout-split-level">
            <h3 class="fs-7 layout-flex-no-shrink mr-4"><span class="ui-dot ui-dot--red"></span>Novara Live</h3>
            <div class="ui-ticker__wrapper layout-flex-grow fs-7 font-weight-regular">
              <div class="ui-ticker">
                <div class="ui-ticker__inner">
                  The biggest stories and guests from the UK and international left. Livestreamed on YouTube weeknights at 6PM GMT.
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="grid-item is-s-24 is-l-16 is-xxl-18">
        <?php
          if ($latest_video->have_posts()) {
            $latest_video->the_post();
            $meta = get_post_meta($post->ID);
        ?>
        <a href="<?php the_permalink(); ?>">
          <div>
            <div class="layout-thumbnail-frame">
              <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                <?php render_post_ui_tags($post->ID, true, true, 'no-border'); ?>
              </div>
              <?php render_thumbnail($post->ID, 'col24-16to9', array(
                'class' => 'ui-rounded-image'
              )); ?>
            </div>
            <div class="grid-row grid--nested mt-4">
              <div class="grid-item is-m-24 is-xl-18 is-xxl-12">
                <h6 class="js-fix-widows fs-7"><?php the_title(); ?></h6>
                <p class="fs-3-sans mt-2 mb-0">
                  <?php render_short_description($post->ID); ?>
                </p>
              </div>
            </div>
          </div>
        </a>
        <?php
          }
        ?>
      </div>
      <div class="grid-item is-s-24 is-l-8 is-xxl-6 mt-s-5">
        <a href="<?php echo $category_link; ?>">
          <div class="layout-split-level fs-2 mb-4">
            <h5 class="font-bold font-uppercase">Full Episodes</h5>
            <span>See All</span>
          </div>
        </a>
        <div class="front-page-novara-live__posts-scroller">
        <?php
          if ($latest_video->have_posts()) {
            while($latest_video->have_posts()) {
              $latest_video->the_post();
              $meta = get_post_meta($post->ID);
        ?>
          <a href="<?php the_permalink(); ?>">
            <div class="pb-4 mb-4 ui-border-bottom">
              <div class="fs-2 mb-3">
                <?php the_time('j F Y'); ?>
              </div>
              <h4 class="post__title fs-4-sans">
                <?php render_post_ui_tags($post->ID, false, true, 'no-fill--white'); ?> <?php the_title(); ?>
              </h4>
            </div>
          </a>
        <?php
            }
          }
        ?>
          <a href="<?php echo $category_link; ?>" class="fs-2">
            <span>See All</span>
          </a>
        </div>
      </div>
    </div>
  </section>
</div>
<?php
  }
