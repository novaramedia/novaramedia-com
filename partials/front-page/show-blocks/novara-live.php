<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
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
        <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $category_link; ?>">
          <div class="layout-split-level">
            <h3 class="font-size-13 font-weight-bold layout-flex-no-shrink mr-4"><span class="ui-dot ui-dot--red"></span>Novara Live</h3>
            <div class="layout-flex-grow layout-overflow-hidden font-size-13">
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
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          if ($latest_video->have_posts()) {
            $latest_video->the_post();
            $meta = get_post_meta($post->ID);
        ?>
          <div class="grid-row grid--nested">
            <div class="grid-item is-l-24 is-xxl-16">
              <div class="layout-thumbnail-frame">
                <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                  <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_post_ui_tags($post->ID, true, true, 'no-border'); ?>
                </div>
                <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink(); ?>" class="ui-hover">
                  <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_thumbnail($post->ID, 'col24-16to9', array(
                    'class' => 'ui-rounded-image'
                  )); ?>
                </a>
              </div>
            </div>
            <div class="grid-item is-l-24 is-xxl-8 mt-l-3">
              <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink(); ?>" class="ui-hover">
                <h6 class="font-size-13 font-weight-bold"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?></h6>
                <div class="font-size-10 text-paragraph-breaks mt-3">
                  <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_short_description($post->ID); ?>
                </div>
              </a>
              <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
                if (!empty($meta['_cmb_related_posts'])) {
                  $related_args = array(
                    'posts_per_page' => 1,
                    'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
                  );

                  $related_posts = new WP_Query($related_args);

                  if ($related_posts->have_posts()) {
                    render_see_also($related_posts);
                  }
                  wp_reset_postdata();
                }
            ?>
            </div>
          </div>
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          }
        ?>
      </div>
      <div class="grid-item is-s-24 is-l-9 is-xl-7 is-xxl-6 mt-s-5">
        <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $category_link; ?>">
          <div class="layout-split-level font-size-8 font-weight-bold mb-4">
            <h5 class="font-weight-bold text-uppercase">Full Episodes</h5>
            <span>See All</span>
          </div>
        </a>
        <div class="front-page-novara-live__posts-scroller ux-scroller">
          <div class="front-page-novara-live__posts-scroller__fade-top ux-scroller__fade-top"></div>
          <div class="front-page-novara-live__posts-scroller__fade-bottom ux-scroller__fade-bottom"></div>
          <div class="front-page-novara-live__posts-scroller__inner ux-scroller__inner">
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            if ($latest_video->have_posts()) {
              $i = 1;
              while($latest_video->have_posts()) {
                $latest_video->the_post();
                $meta = get_post_meta($post->ID);
                $timestamp = get_post_time('c');
          ?>
            <div class="pb-3 mb-3 <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} if ($i < $posts_to_show - 1) {echo 'ui-border-bottom';} ?>">
              <div class="grid-row grid--nested">
                <div class="grid-item is-s-10 is-xxl-8">
                  <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink(); ?>" class="ui-hover">
                    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_thumbnail($post->ID, 'col24-16to9', array(
                      'class' => 'ui-rounded-image'
                    )); ?>
                  </a>
                </div>
                <div class="grid-item is-s-14 is-xxl-16">
                  <div class="layout-split-level font-size-8 font-weight-bold mb-1">
                    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_post_ui_tags($post->ID, false, true, 'no-fill--white'); ?>
                    <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink(); ?>" class="ui-hover"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} if ($i < 6) { ?>
                      <span class="js-time-since" data-timestamp="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $timestamp; ?>"></span>
                    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} } else { ?>
                      <span><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_time('j F Y'); ?></span>
                    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} } ?></a>
                  </div>
                  <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink(); ?>" class="ui-hover">
                    <h4 class="post__title font-size-8 font-size-S-10 font-weight-bold">
                      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?>
                    </h4>
                  </a>
                </div>
              </div>
            </div>
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
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
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  }
