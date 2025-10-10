<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  $description = get_post_meta($post->ID, '_cmb_short_desc');
?>
<a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink() ?>">
  <article <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} post_class('col col6'); ?> id="post-<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_ID(); ?>">
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_post_thumbnail('col6-16to9', array('class' => 'related-post-thumbnail only-desktop')); ?>
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_post_thumbnail('mobile-16to9', array('class' => 'related-post-thumbnail only-mobile')); ?>
    <h5 class="margin-top-tiny margin-bottom-tiny js-fix-widows"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_post_title($post->ID); ?></h5>
    <div class="post-description">
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        if (!empty($description)) {
          echo $description[0];
        } else {
          the_excerpt();
        }
      ?>
    </div>
  </article>
</a>
