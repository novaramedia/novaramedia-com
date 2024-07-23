<?php
  if (empty($args['grid-item-classes'])) { // if no classes set for grid item don't render
    return;
  }

  $post_id = get_the_ID();

  $text_size = !empty($args['text-size']) ? $args['text-size'] : 'regular'; // get size size parameter with regular text size as default. string matching descriptive values
  $image_size = !empty($args['image-size']) ? $args['image-size'] : 'col24-16to9'; // get image size parameter, with max possible size as fallback default. string name of image size

  $is_show_tags = !empty($args['show-tags']) ? $args['show-tags'] : false;
  $is_article = nm_is_article($post_id); // check if post is article

  $meta = get_post_meta($post->ID);
?>
<article <?php post_class($args['grid-item-classes']); ?> id="post-<?php the_ID(); ?>">
  <?php if ($is_show_tags) { ?>
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <?php render_post_ui_tags($post_id, true, true); ?>
      </div>
      <a href="<?php the_permalink() ?>" class="ui-hover">
        <?php render_thumbnail($post_id, $image_size, array(
          'class' => 'ui-rounded-image',
        )); ?>
      </a>
    </div>
  <?php } else { ?>
    <a href="<?php the_permalink() ?>" class="ui-hover">
      <?php render_thumbnail($post_id, $image_size, array(
        'class' => 'ui-rounded-image',
      )); ?>
    </a>
  <?php } ?>
<?php
  switch ($text_size) {
    case 'regular':
?>
  <a href="<?php the_permalink() ?>" class="ui-hover">
    <h5 class="index-post-title fs-3-sans font-weight-bold mt-2 js-fix-widows"><?php the_title(); ?></h5>
    <?php
      if ($is_article) {
    ?>
    <h6 class="fs-2 font-weight-bold text-uppercase mt-1 js-fix-widows"><?php
      if ($is_article) {
        render_bylines($post_id);
      } else {
        render_standfirst($post_id);
      }
    ?></h6>
    <?php } ?>
    <div class="fs-3-sans mt-1">
      <?php
        if ($is_article) {
          render_standfirst($post_id);
        } else {
          render_short_description($post_id);
        }
      ?>
    </div>
  <?php
        break;
      case 'large':
  ?>
    <h3 class="font-size-2 mt-2 js-fix-widows"><?php the_title(); ?></h3>
    <?php
      if ($is_article) {
    ?>
  <h3 class="fs-3-sans font-weight-bold js-fix-widows"><?php
      if ($is_article) {
        render_bylines($post_id);
      } else {
        render_standfirst($post_id);
      }
    ?></h3>
    <?php } ?>
    <div class="mt-1">
      <?php
        if ($is_article) {
          render_standfirst($post_id);
        } else {
          render_short_description($post_id);
        }
      ?>
    </div>
  <?php
        break;
    }
  ?>
  </a>
</article>
