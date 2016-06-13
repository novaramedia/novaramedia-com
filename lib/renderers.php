<?php
function render_tv_query($query) {
  global $post;

  // First large video
  if ($query->have_posts()) {
    $query->the_post();
  ?>
  <div class="col col20">
    <?php
    $meta = get_post_meta($post->ID);
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
    <a href="<?php the_permalink(); ?>">
      <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
    </a>
  </div>
  <div class="col col4">
    <?php

    // Side 3 remaining vids
    if ($query->have_posts()) {
      while($query->have_posts()) {
        $query->the_post();
    ?>
    <a href="<?php the_permalink(); ?>">
     <div class="single-tv-related-tv margin-bottom-small">
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
<?php
  }
}

function render_tweet_link($url, $title = null, $link_text = 'Tweet', $hashtag = null) {
  $twitter_url = 'https://twitter.com/intent/tweet?via=novaramedia';

  if ($hashtag) {
    $twitter_url .= '&hashtags=' . $hashtag;
  }

  if ($title) {
    $twitter_url .= '&text=' . $title;
  }

  $twitter_url .= '&url=' . urlencode($url);

  echo '<a href="' . $twitter_url . '" target="_blank">' . $link_text . '</a>';
}

function render_facebook_share_link($url, $link_text = 'Facebook Share') {
  $facebook_url = 'https://www.facebook.com/sharer/sharer.php?';

  $facebook_url .= '&u=' . urlencode($url);

  echo '<a href="' . $facebook_url . '" target="_blank">' . $link_text . '</a>';
}




