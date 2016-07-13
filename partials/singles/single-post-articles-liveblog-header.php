<?php
  if (!empty($meta['bitly_url'])) {
    $share_url = $meta['bitly_url'][0];
  } else {
    $share_url = get_the_permalink($post->ID);
  }
?>

<div class="row margin-top-small margin-bottom-small">
  <div class="col col2"></div>
  <div class="col col2 font-smaller margin-top-small">
    Live Blog:
  </div>
  <div class="col col12">
    <h1 id="single-articles-title" class="js-fix-widows"><?php the_title(); ?></h1>
  </div>
  <div class="col col4 font-smaller margin-top-small">
    <ul class="inline-action-list">
      <li><?php render_tweet_link($share_url, $post->post_title, 'Tweet Article'); ?></li>
      <li><?php render_facebook_share_link($share_url); ?></li>
    </ul>
  </div>
</div>