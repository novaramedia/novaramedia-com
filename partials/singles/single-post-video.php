<?php
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
    <h1 class="fs-8"><?php the_title(); ?></h1>
  </div>
  <div class="grid-item is-s-24 is-m-14 is-xxl-12 text-copy pt-1">
    <?php the_content(); ?>
  </div>
</header>
<div class="grid-row mb-4 fs-3-sans">
  <div class="grid-item is-s-24 is-m-10 is-xxl-12 mb-s-3">
    <ul class="inline-action-list">
      <li>Published <?php the_time('j F Y'); ?></li>
      <?php
        if (!empty($resources)) {
          echo '<li><a class="u-pointer" id="js-resources-toggle">Resources</a></li>';
        }
      ?>
    </ul>
  </div>
  <div class="grid-item is-s-24 is-m-14 is-xxl-12">
    <ul class="inline-action-list">
      <li><?php render_tweet_link($share_url, $post->post_title, 'Tweet video'); ?></li>
      <li><?php render_facebook_share_link($share_url, 'Share this video on Facebook'); ?></li>
      <li><?php render_email_share_link($share_url, $post->post_title, 'Email this video');?></li>
      <li><?php render_reddit_share_link($share_url, $post->post_title, 'Post to Reddit');?></li>
    </ul>
  </div>
</div>
<?php
  if (!empty($resources)) {
    render_resources_row($resources);
  }
?>
<div class="grid-row mb-6">
  <div class="grid-item is-s-24 is-xxl-20 mb-4">
    <?php
      if (!empty($meta['_cmb_utube'])) {
        $autoplay = false;

        // soft check to see if the link was internal from another part of the website. if so enable autoplay possibility (will depend on browser and config)
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
          $autoplay = true;
        }
    ?>
      <div class="u-video-embed-container">
        <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url($meta['_cmb_utube'][0], $autoplay); ?>" frameborder="0" allowfullscreen></iframe>
      </div>
    <?php
      } else {
        echo 'Someone messed up :/';
      }
    ?>
  </div>
  <div class="grid-item is-s-24 is-xxl-4">
    <div class="grid-row grid--nested fs-3-sans">
      <?php
        $related_video = get_related_posts(null, 'Video', 3);

        if ($related_video->have_posts()) {
          while ($related_video->have_posts()) {
            $related_video->the_post();
      ?>
      <div class="grid-item is-s-8 is-xxl-24 mb-3">
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
    <a class="nm-button nm-button--red" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow">Subscribe on YouTube</a>
  </div>
</div>
