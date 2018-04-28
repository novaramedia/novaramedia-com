<?php

function render_home_focus($focus, $classes) {
  $focus_object = get_term($focus);
  $focus_link = get_term_link($focus_object);
?>
  <section id="home-focus-posts" class="container <?php echo $classes; ?>">
    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $focus_link; ?>">Focus: <?php echo $focus_object->name; ?></a></h4>
      </div>
    </div>

    <div class="row margin-bottom-small mobile-margin-bottom-none">
<?php
      $focusPosts = new WP_Query(array(
        'posts_per_page' => 3,
        'tax_query' => array(
          array(
            'taxonomy' => 'focus',
            'field' => 'term_id',
            'terms' => $focus
          ),
        ),
      ));
      if ($focusPosts->have_posts()) {
        $i = 0;
        while ($focusPosts->have_posts()) {
          $focusPosts->the_post();

          if ($i % 3 === 0 && $i !== 0) {
            echo "</div>\n<div class=\"row margin-bottom-small mobile-margin-bottom-none\">";
          }

          get_template_part('partials/post-layouts/home-focus-post-col8');

          $i++;
        }
      }
?>
    </div>
  </section>
 <?php
}

function render_video_query($query) {
  global $post;

  // First large video
  if ($query->have_posts()) {
    $query->the_post();
  ?>
  <div class="col col20 video-block-main-video mobile-margin-bottom-basic">
    <?php
    $meta = get_post_meta($post->ID);
    if (!empty($meta['_cmb_utube'])) {
    ?>
    <div class="u-video-embed-container">
      <iframe class="youtube-player" type="text/html" src="https://www.youtube.com/embed/<?php echo $meta['_cmb_utube'][0]; ?>?autohide=2&amp;modestbranding=1&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
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
     <div class="video-related-video margin-bottom-small">
       <?php the_post_thumbnail('col4-16to9', array('class' => 'only-desktop')); ?>
       <?php the_post_thumbnail('col6-16to9', array('class' => 'only-mobile')); ?>
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

function render_resources_row($resources) {
?>
<div id="single-resources-section" class="row margin-bottom-basic">
  <div class="col col24">
    <ul class="inline-action-list">
      <?php
        foreach($resources as $resource) {
          if (!empty($resource['title']) && !empty($resource['link'])) {
            echo '<li><a target="_black" href="' . $resource['link'] . '">' . $resource['title'] . '</a><li>';
          }
        }
      ?>
    </ul>
  </div>
</div>
<?php
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
