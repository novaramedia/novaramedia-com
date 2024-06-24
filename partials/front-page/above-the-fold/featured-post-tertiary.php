<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  if (!isset($args['show_descriptive_text'])) {
    $args['show_descriptive_text'] = true;
  }

  $post_id = $args['post_id'];
  $show_descriptive_text = $args['show_descriptive_text'];
  $container_classes = isset($args['container_classes']) ? $args['container_classes'] : '';

  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);
  $sub_category = get_the_sub_category($post_id);
?>
  <div class="featured-posts__tertiary <?php echo $container_classes; ?>">
    <div>
      <?php render_post_ui_tags($post_id); ?>
    </div>
    <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
      <h2 class="post__title fs-5-sans font-bold mt-1"><?php echo get_the_title($post_id); ?></h2>
      <?php
        $meta = get_post_meta(get_the_ID());
      ?>
      <h5 class="fs-2 font-uppercase mt-1">
        <?php
          if ($is_article) {
            render_bylines($post_id);
          } else {
            render_standfirst($post_id);
          }
        ?>
      </h5>
      <?php
        if ($show_descriptive_text) {
      ?>
      <div class="fs-3-sans mt-1 mb-0">
        <?php
          if ($is_article) {
            render_standfirst($post_id);
          } else {
            render_short_description($post_id);
          }
        ?>
      </div>
      <?php
        }
      ?>
    </a>
  </div>
