<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  $meta = get_post_meta($post->ID);
  $resources = get_post_meta($post->ID, '_cmb_resources', true);

  if (!empty($meta['bitly_url'])) {
    $share_url = $meta['bitly_url'][0];
  } else {
    $share_url = get_the_permalink($post->ID);
  }
?>
<header class="grid-row mb-4">
  <div class="grid-item is-s-24 is-m-10 is-xxl-12 mb-s-4">
    <h1 class="font-size-15 font-weight-bold"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?></h1>
  </div>
  <div class="grid-item is-s-24 is-m-14 is-xxl-12 text-copy pt-1">
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_content(); ?>
  </div>
</header>
<div class="grid-row mb-4 font-size-9">
  <div class="grid-item is-s-24 is-m-10 is-xxl-12 mb-s-3">
    <ul class="inline-action-list">
      <li>Published <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_time('j F Y'); ?></li>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        if (!empty($resources)) {
          echo '<li><a class="u-pointer" id="js-resources-toggle">Resources</a></li>';
        }
      ?>
    </ul>
  </div>
  <div class="grid-item is-s-24 is-m-14 is-xxl-12">
    <ul class="inline-action-list">
      <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_tweet_link($share_url, $post->post_title, 'Tweet video'); ?></li>
      <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_facebook_share_link($share_url, 'Share this video on Facebook'); ?></li>
      <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_email_share_link($share_url, $post->post_title, 'Email this video');?></li>
      <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_reddit_share_link($share_url, $post->post_title, 'Post to Reddit');?></li>
    </ul>
  </div>
</div>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  if (!empty($resources)) {
    render_resources_row($resources);
  }
?>
<div class="grid-row mb-6">
  <div class="grid-item is-s-24 is-xxl-20 mb-4">
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
      if (!empty($meta['_cmb_utube'])) {
        $autoplay = false;

        // soft check to see if the link was internal from another part of the website. if so enable autoplay possibility (will depend on browser and config)
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
          $autoplay = true;
        }
    ?>
      <div class="u-video-embed-container">
        <iframe class="youtube-player lazyload" data-src="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo generate_youtube_embed_url($meta['_cmb_utube'][0], $autoplay); ?>" frameborder="0" allowfullscreen></iframe>
      </div>
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
      } else {
        echo 'Someone messed up :/';
      }
    ?>
  </div>
  <div class="grid-item is-s-24 is-xxl-4">
    <div class="grid-row grid--nested font-size-9">
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        $related_video = get_related_posts(null, 'Video', 3);

        if ($related_video->have_posts()) {
          while ($related_video->have_posts()) {
            $related_video->the_post();
            $meta = get_post_meta($post->ID);
      ?>
      <div class="grid-item is-s-8 is-xxl-24 mb-3">
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
        <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink(); ?>" class="ui-hover">
          <h6 class="js-fix-widows font-size-9 font-weight-bold mt-1">
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_video_title_and_standfirst($post->ID); ?>
          </h6>
        </a>
      </div>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          }
        }
        wp_reset_postdata();
      ?>
    </div>
    <a class="ui-button ui-button--red ui-button--small ui-button--auto-height" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow">Subscribe on YouTube</a>
  </div>
</div>
