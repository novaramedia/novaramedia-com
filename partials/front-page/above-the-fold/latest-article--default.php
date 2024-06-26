<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  if (!isset($args['has_bottom_border'])) {
    $args['has_bottom_border'] = false;
  }

  $post_id = $args['post_id'];
  $has_bottom_border = $args['has_bottom_border'];

  $meta = get_post_meta($post_id);
  $timestamp = get_post_time('c', false, $post_id);

  $is_article = nm_is_article($post_id);
?>
<div class="margin-bottom-small padding-bottom-small <?php if ($has_bottom_border) {echo 'ui-border-bottom';} ?>">
  <div class="layout-split-level fs-2 mb-1">
    <?php render_post_ui_tags($post_id); ?>
    <!-- <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
      <span class="js-time-since" data-timestamp="<?php echo $timestamp; ?>"></span>
    </a> -->
  </div>
  <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
    <h4 class="post__title fs-5-sans fs-s-6 font-condensed"><?php echo get_the_title($post_id); ?></h4>
    <!-- <h5 class="fs-3-serif mt-1">
      <?php render_standfirst($post_id); ?>
    </h5> -->
    <h5 class="fs-2 font-uppercase mt-1">
      <?php
        if ($is_article) {
          render_bylines($post_id);
        } else {
          render_standfirst($post_id);
        }
      ?>
    </h5>
  </a>
</div>
