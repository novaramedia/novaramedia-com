<?php
  $novara_life_category = get_term_by('slug', 'novara-live', 'category');

  if ($novara_life_category) {
    $category_link = get_category_link($novara_life_category->term_id);

    $args = array(
      'posts_per_page' => 8,
      'cat' => $novara_life_category->term_id,
    );

    $latest_video = new WP_Query($args);
?>
<div class="background-black font-color-white">
  <section id="front-page-novara-live-posts" class="container pt-6 pb-6">
    <div class="grid-row">
      <div class="grid-item is-s-24 is-xxl-18">
        <div>
          <a href="<?php echo $category_link; ?>">
            <h3 class="fs-7 mb-4"><span class="ui-dot ui-dot--red"></span>Novara Live</h3>
            <p class="fs-4">The biggest stories and guests from the UK and international left. Livestreamed on YouTube weeknights at 6PM GMT.</p>
          </a>
        </div>

        <?php
          if ($latest_video->have_posts()) {
            $latest_video->the_post();
            $meta = get_post_meta($post->ID);
        ?>
        <a href="<?php the_permalink(); ?>">
          <div class="mt-5">
            <div class="layout-thumbnail-frame">
              <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                <?php render_post_ui_tags($post->ID, true, true, 'no-border'); ?>
              </div>
              <?php render_thumbnail($post->ID, 'col24-16to9', array(
                'class' => 'ui-rounded-image'
              )); ?>
            </div>
            <div class="grid-row grid--nested mt-4">
              <div class="grid-item is-s-24 is-xxl-12">
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
      <div class="grid-item is-s-24 is-xxl-6">
        <a href="<?php echo $category_link; ?>">
          <div class="layout-split-level fs-2 mb-5">
            <h5 class="font-bold font-uppercase">Full Episodes</h5>
            <span>See All</span>
          </div>
        </a>
        <?php
          if ($latest_video->have_posts()) {
            while($latest_video->have_posts()) {
              $latest_video->the_post();
              $meta = get_post_meta($post->ID);
        ?>
        <a href="<?php the_permalink(); ?>">
          <div class="pb-4 mb-4 ui-border-bottom">
            <div class="layout-split-level fs-2 mb-3">
              <?php render_post_ui_tags($post_id, true, false, 'no-fill--white'); ?>
              <span><?php the_time('j F Y'); ?></span>
            </div>
            <h4 class="post__title fs-4-sans">
              <?php the_title(); ?>
            </h4>
          </div>
        </a>
        <?php
            }
          }
        ?>
      </div>
    </div>
  </section>
</div>
<?php
  }
