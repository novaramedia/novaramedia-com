<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  $post_id = $args['post_id'];
  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);
  $sub_category = get_the_sub_category($post_id);
?>
<a href="<?php the_permalink(); ?>" class="ui-post-hover">
  <div class="featured-posts__tertiary mt-5">
    <div>
      <?php render_post_ui_tags($post_id); ?>
    </div>
    <h2 class="post__title fs-5-sans font-bold mt-2 js-fix-widows"><?php echo get_the_title($post_id); ?></h2>
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
</a>
