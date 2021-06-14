<?php
  if (empty($args['grid-item-classes'])) {
    return;
  }

  $meta = get_post_meta($post->ID);
  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;
  $youtube_id = !empty($meta['_cmb_utube'][0]) ? $meta['_cmb_utube'][0] : false;
?>

<article <?php post_class($args['grid-item-classes']); ?> id="post-<?php the_ID(); ?>">
  <?php
    if ($youtube_id) {
  ?>
    <div class="u-video-embed-container">
      <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($youtube_id); ?>" allow="autoplay" allowfullscreen></iframe>
    </div>
  <?php
    }
  ?>

  <a href="<?php the_permalink() ?>">
    <h5 class="index-post-title margin-top-tiny js-fix-widows"><?php render_post_title($post->ID); ?></h5>
  
    <div class="index-post-description margin-top-tiny">
      <?php
        if ($description) {
          echo $description;
        } else {
          the_excerpt();
        }
      ?>
    </div>
  </a>
</article>
