<?php
  $meta = get_post_meta($post->ID);
?>

<div class="row">
  <div class="col col24">
    <h4>TV</h4>
  </div>
</div>

<div class="row">
  <div class="col col12">
    <h1 class="js-fix-widows"><?php the_title(); ?></h1>
    // social sharing
    <p>Published <?php the_time('jS F Y'); ?></p>
  </div>

  <div class="col col12">
    <?php the_content(); ?>
  </div>
</div>

<div class="row">
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
  <div class="col col4">
    <h4>Related TV</h4>
    <div id="single-tv-related-tv">
      // related tv goes here
    </div>
  </div>
</div>