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
    $podcast_url = 'https://podfollow.com/novaramedia/view';
  }
?>

<header class="grid-row mb-4">
  <div class="grid-item is-s-24 is-m-10 is-xxl-12">
    <h1 class="fs-8 js-fix-widows mb-4"><?php the_title(); ?></h1>
    <?php the_post_thumbnail([500, 400]); ?>
  </div>
  <div class="grid-item is-s-24 is-m-14 is-xxl-12 text-copy mt-1 mt-s-4">
    <?php the_content(); ?>
  </div>
</header>

<div class="grid-row mb-4 fs-3-sans">
  <div class="grid-item is-s-24 is-m-10 is-xxl-12 mb-s-2">
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
        echo '<li><a href="' . $meta['_cmb_dl_mp3'][0] . '">Download mp3</a></li>';
      }
    ?>
    </ul>
  </div>
  <div class="grid-item is-s-24 is-m-14 is-xxl-12">
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

<div class="grid-row <?php if (empty($transcript)) { echo 'mb-6'; } else { echo 'mb-4'; } ?>">
  <div class="grid-item is-xxl-24">
    <?php
      if (!empty($meta['_cmb_sc'][0])) {
    ?>
        <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="200" scrolling="no" frameborder="no"></iframe>

      <?php
        if (!empty($meta['_cmb_is_resonance']) && $meta['_cmb_is_resonance'][0]) {
      ?>
        <div class="font-mono font-smaller mt-1">
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
<div class="grid-row mb-6">
  <div class="grid-item is-xxl-24 mb-4">
    <h4>Transcript</h4>
  </div>

  <div class="grid-item is-m-24 is-xxl-18">
    <div class="text-copy mb-4">
      <?php echo apply_filters('the_content', $transcript); ?>
    </div>
  </div>
</div>
<?php
  }
?>
