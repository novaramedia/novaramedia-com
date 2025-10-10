<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  if (!is_numeric($args['post_id'])) {
    return;
  }

  $post_id = $args['post_id'];
  $container_classes = $args['container_classes'];
  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);
?>
  <div class="featured-posts__secondary <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $container_classes; ?>">
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_post_ui_tags($post_id, true, true, 'no-border'); ?>
      </div>
      <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo get_permalink($post_id); ?>" class="ui-hover">
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_thumbnail($post_id, 'col12-16to9', array(
          'class' => 'ui-rounded-image',
          'data-no-lazysizes' => true,
          'loading' => 'eager'
        )); ?>
      </a>
    </div>
    <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo get_permalink($post_id); ?>" class="ui-hover">
      <div class="mt-2">
        <h2 class="post__title font-size-11 font-weight-bold"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo get_the_title($post_id); ?></h2>
        <h5 class="font-size-8 font-weight-bold text-uppercase mt-1">
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            if ($is_article) {
              render_bylines($post_id);
            } else {
              render_standfirst($post_id);
            }
          ?>
        </h5>
        <div class="font-size-9 mt-1 mb-0">
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            if ($is_article) {
              render_standfirst($post_id);
            } else {
              render_short_description($post_id);
            }
          ?>
        </div>
      </div>
    </a>
  </div>
