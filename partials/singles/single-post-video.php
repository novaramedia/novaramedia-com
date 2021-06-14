<?php
  $meta = get_post_meta($post->ID);
  $resources = get_post_meta($post->ID, '_cmb_resources', true);

  $category_id = get_cat_ID('Video');
  $category_link = get_category_link( $category_id );

  if (!empty($meta['bitly_url'])) {
    $share_url = $meta['bitly_url'][0];
  } else {
    $share_url = get_the_permalink($post->ID);
  }
?>

<div class="row">
  <div class="col col24 margin-bottom-basic mobile-margin-bottom-small">
    <h4><a href="<?php echo $category_link; ?>">Video</a></h4>
  </div>
</div>

<header class="row margin-bottom-small">
  <div class="col col12">
    <h1 class="js-fix-widows"><?php the_title(); ?></h1>
  </div>

  <div class="col col12 text-copy padding-top-micro">
    <?php the_content(); ?>
  </div>
</header>

<div class="row margin-bottom-basic font-smaller">
  <div class="col col12">
    <ul class="inline-action-list">
      <li>Published <?php the_time('j F Y'); ?></li>
      <?php
        if (!empty($resources)) {
          echo '<li><a class="u-pointer" id="js-resources-toggle">Resources</a></li>';
        }
      ?>
    </ul>
  </div>
  <div class="col col12">
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

<div class="row margin-bottom-large">
  <div class="col col20">
    <?php
      if (!empty($meta['_cmb_utube'])) {
        $autoplay = false;

        // soft check to see if the link was internal from another part of the website. if so enable autoplay possibility (will depend on browser and config)
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
          $autoplay = true;
        }
    ?>
      <div class="u-video-embed-container">
        <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($meta['_cmb_utube'][0], $autoplay); ?>" allow="autoplay" allowfullscreen></iframe>
      </div>
    <?php
      } else {
        echo 'Someone messed up :/';
      }
    ?>
  </div>
  <div id="single-video-related-video-holder" class="col col4">
    <h4 class="margin-bottom-small"><a href="<?php echo $category_link; ?>">More Video</a></h4>
    <div id="single-video-related-video" class="font-smaller">
      <?php
        $related_video = get_related_posts(null, 'Video', 3);

        if ($related_video->have_posts()) {
          while ($related_video->have_posts()) {
            $related_video->the_post();
      ?>
      <div class="single-video-related-video margin-bottom-small">
        <a href="<?php the_permalink(); ?>">
         <?php the_post_thumbnail('col4-16to9', array('class' => 'only-desktop')); ?>
         <?php the_post_thumbnail('col6-16to9', array('class' => 'only-mobile')); ?>
          <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
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