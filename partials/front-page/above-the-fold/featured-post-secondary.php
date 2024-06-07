<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  $post_id = $args['post_id'];
  $container_classes = $args['container_classes'];
  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);
  $sub_category = get_the_sub_category($post_id);
?>
  <div class="featured-posts__secondary <?php echo $container_classes; ?>">
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <?php render_post_ui_tags($post_id, true, true, 'no-border'); ?>
      </div>
      <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
        <?php render_thumbnail($post_id, 'col12-16to9', array(
          'class' => 'ui-rounded-image',
          'data-no-lazysizes' => true,
          'loading' => 'eager'
        )); ?>
      </a>
    </div>
    <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
      <div class="mt-2">
        <h2 class="post__title fs-5-sans font-bold"><?php echo get_the_title($post_id); ?></h2>
        <h5 class="fs-2 font-uppercase mt-1">
          <?php
            if ($is_article) {
              render_bylines($post_id);
            } else {
              render_standfirst($post_id);
            }
          ?>
        </h5>
        <p class="fs-3-sans mt-1 mb-0">
          <?php
            if ($is_article) {
              render_standfirst($post_id);
            } else {
              render_short_description($post_id);
            }
          ?>
        </p>
      </div>
    </a>
  </div>
