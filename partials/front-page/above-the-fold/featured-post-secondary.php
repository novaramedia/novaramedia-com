<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  $post_id = $args['post_id'];
  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);
  $sub_category = get_the_sub_category($post_id);
?>
<div class="featured-posts__secondary">
  <div class="layout-thumbnail-frame">
    <div class="layout-thumbnail-frame__inner mt-1 ml-1">
      <span class="ui-tag ui-tag--no-border"><?php if ($sub_category) { echo $sub_category; } ?></span>
    </div>
    <?php render_thumbnail($post_id, 'col12-16to9'); ?>
  </div>
  <div>
    <h2 class="fs-5-sans font-bold js-fix-widows"><?php echo get_the_title($post_id); ?></h2>
    <h5 class="fs-2 font-uppercase mt-1">
      <?php
        if ($is_article) {
          render_bylines($post_id);
        } else {
          render_standfirst($post_id);
        }
      ?>
    </h5>
    <p class="mt-1 mb-0">
      <?php
        if ($is_article) {
          render_standfirst($post_id);
        } else {
          render_short_description($post_id);
        }
      ?>
    </p>
  </div>
</div>
