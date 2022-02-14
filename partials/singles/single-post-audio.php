<?php
  $meta = get_post_meta($post->ID);
  $resources = get_post_meta($post->ID, '_cmb_resources', true);
  $transcript = get_post_meta($post->ID, '_cmb_transcript', true);

  if (!empty($meta['bitly_url'])) {
    $share_url = $meta['bitly_url'][0];
  } else {
    $share_url = get_the_permalink($post->ID);
  }

  $show_category = get_child_level_child_category($post->ID);

  if ($show_category && get_term_meta($show_category->term_id, '_nm_podcast_url', true)) {
    $podcast_url = get_term_meta($show_category->term_id, '_nm_podcast_url', true);
  } else {
    $podcast_url = 'https://podcast.novaramedia.com';
  }
?>

<header class="flex-grid-row margin-bottom-small">
  <div class="flex-grid-item flex-item-s-12 flex-item-m-5 flex-item-xxl-6">
    <h1 class="js-fix-widows"><?php the_title(); ?></h1>
    <?php the_post_thumbnail('col4', ['class' => 'margin-top-small only-desktop']); ?>
  </div>

  <div class="flex-grid-item flex-item-s-12 flex-item-m-7 flex-item-xxl-6 text-copy padding-top-micro">
    <?php the_content(); ?>
  </div>
</header>

<div class="flex-grid-row margin-bottom-basic font-smaller">
  <div class="flex-grid-item flex-item-s-12 flex-item-m-5 flex-item-xxl-6">
    <ul class="inline-action-list">
      <li>Published <?php the_time('j F Y'); ?></li>
      <?php
        if (!empty($resources)) {
          echo '<li><a class="u-pointer" id="js-resources-toggle">Resources</a></li>';
        }
      ?>
      <li><a href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow">Subscribe to Podcast</a></li>
    <?php
      if (!empty($meta['_cmb_dl_mp3'])) {
        echo '<li><a class="font-smaller" href="' . $meta['_cmb_dl_mp3'][0] . '">Download mp3</a></li>';
      }
    ?>
    </ul>
  </div>
  <div class="flex-grid-item flex-item-s-12 flex-item-m-7 flex-item-xxl-6">
    <ul class="inline-action-list">
      <li><?php render_tweet_link($share_url, $post->post_title, 'Tweet episode'); ?></li>
      <li><?php render_facebook_share_link($share_url, 'Share this episode on Facebook'); ?></li>
      <li><?php render_email_share_link($share_url, $post->post_title, 'Email this episode');?></li>
      <li><?php render_reddit_share_link($share_url, $post->post_title, 'Post to Reddit');?></li>
    </ul>
  </div>
</div>

<?php
  if (!empty($resources)) {
    render_resources_row($resources);
  }
?>

<div class="flex-grid-row <?php if (empty($transcript)) { echo 'margin-bottom-large'; } else { echo 'margin-bottom-basic'; } ?>">
  <div class="flex-grid-item flex-item-xxl-12">
    <?php
      if (!empty($meta['_cmb_sc'][0])) {
    ?>
        <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="200" scrolling="no" frameborder="no"></iframe>

      <?php
        if (!empty($meta['_cmb_is_resonance']) && $meta['_cmb_is_resonance'][0]) {
      ?>
        <div class="font-mono font-smaller margin-top-micro">
        	<a target=_blank href="http://resonancefm.com/">powered by: Resonance FM</a>
        </div>
      <?php
        }
      } else {
        echo 'Someone messed up :/';
      }
    ?>
  </div>
</div>

<?php
  if (!empty($transcript)) {
?>
<div class="flex-grid-row margin-bottom-large">
  <div class="flex-grid-item flex-item-xxl-12 margin-bottom-small">
    <h4>Transcript</h4>
  </div>

  <div class="flex-grid-item flex-item-m-12 flex-item-xxl-9">
    <div class="text-copy margin-bottom-basic">
      <?php echo apply_filters('the_content', $transcript); ?>
    </div>
  </div>
</div>
<?php
  }
?>
