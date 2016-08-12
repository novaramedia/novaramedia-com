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

  <div class="col col12 text-copy font-italic padding-top-micro">
    <?php the_content(); ?>
  </div>
</header>

<div class="row margin-bottom-basic font-smaller">
  <div class="col col12">
    <ul class="inline-action-list">
      <li>Published <?php the_time('jS F Y'); ?></li>
      <?php
        if (!empty($resources)) {
          echo '<li><a class="u-pointer" id="js-resources-toggle">Resources</a></li>';
        }
      ?>
      <li><a href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank">Subscribe on YouTube</a></li>
    </ul>
  </div>
  <div class="col col12">
    <ul class="inline-action-list">
      <li><?php render_tweet_link($share_url, $post->post_title, 'Tweet Episode'); ?></li>
      <li><?php render_facebook_share_link($share_url); ?></li>
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
    ?>
      <div class="u-video-embed-container">
        <iframe class="youtube-player" type="text/html" src="http://www.youtube.com/embed/<?php echo $meta['_cmb_utube'][0]; ?>?autohide=2&amp;modestbranding=1&amp;origin=http://novaramedia.com&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
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
      <a href="<?php the_permalink(); ?>">
        <div class="single-video-related-video margin-bottom-small">
          <?php the_post_thumbnail('col4-16to9'); ?>
          <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
        </div>
      </a>


      <?php
          }
        }
        wp_reset_postdata();
      ?>
    </div>
  </div>
</div>