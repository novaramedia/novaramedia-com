<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  if (empty($args['grid-item-classes'])) {
    return;
  }

  $meta = get_post_meta($post->ID);
  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;
  $youtube_id = !empty($meta['_cmb_utube'][0]) ? $meta['_cmb_utube'][0] : false;
?>

<article <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} post_class($args['grid-item-classes']); ?> id="post-<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_ID(); ?>">
  <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
    if ($youtube_id) {
  ?>
    <div class="u-video-embed-container background-black">
      <iframe class="youtube-player lazyload" data-src="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo generate_youtube_embed_url($youtube_id); ?>" frameborder="0" allowfullscreen></iframe>
    </div>
  <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
    }
  ?>

  <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink() ?>">
    <h5 class="index-post-title margin-top-tiny js-fix-widows"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_post_title($post->ID); ?></h5>

    <div class="index-post-description margin-top-tiny">
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        if ($description) {
          echo $description;
        } else {
          the_excerpt();
        }
      ?>
    </div>
  </a>
</article>
