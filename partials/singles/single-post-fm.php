<?php
  $meta = get_post_meta($post->ID);
?>

<div class="row">
  <div class="col col24">
    <h4>FM</h4>
  </div>
</div>

<div class="row">
  <div class="col col12">
    <h1 class="js-fix-widows"><?php the_title(); ?></h1>
    // social sharing
    <?php
      if (!empty($meta['_cmb_dl_mp3'])) {
        echo '<a href="' . $meta['_cmb_dl_mp3'][0] . '">Download mp3</a>';
      }
    ?>
    <p>Published <?php the_time('jS F Y'); ?></p>
  </div>

  <div class="col col12">
    <?php the_content(); ?>
  </div>
</div>

<div class="row">
  <div class="col col24">
    <?php
      if (!empty($meta['_cmb_sc'][0])) {
    ?>
        <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="200" scrolling="no" frameborder="no"></iframe>

      <?php
        if (!empty($meta['_cmb_is_resonance']) && $meta['_cmb_is_resonance'][0]) {
      ?>
        <div class="font-mono font-smaller">
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
