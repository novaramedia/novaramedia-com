<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  $meta = get_post_meta($post->ID);
  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;

  $content_type = get_the_top_level_category(get_the_ID()); // get top level catergory for content type
  $is_article = $content_type->category_nicename === 'articles' ? true : false; // check if is article for display layout
?>
<a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink() ?>">
  <article <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} post_class('col col8'); ?> id="post-<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_ID(); ?>">
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_post_thumbnail('col8-16to9', array('class' => 'index-post-thumbnail')); ?>
    <h5 class="index-post-title margin-top-tiny js-fix-widows"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_post_title($post->ID); ?></h5>
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} if ($is_article) { ?>
    <h6 class="margin-top-micro font-weight-bold">by <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_bylines($post->ID, false); ?></h6>
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} } ?>
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
  </article>
</a>
