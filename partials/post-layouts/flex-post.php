<?php
  if (empty($args['grid-item-classes'])) { // if no classes set for grid item don't render
    return;
  }

  $content_type = get_the_top_level_category(get_the_ID()); // get top level catergory for content type

  $is_article = !empty($content_type) && $content_type->category_nicename === 'articles' ? true : false; // check if is article for display layout

  $text_size = !empty($args['text-size']) ? $args['text-size'] : 'regular'; // get size size parameter with regular text size as default. string matching descriptive values
  $image_size = !empty($args['image-size']) ? $args['image-size'] : 'col24-16to9'; // get image size parameter, with max possible size as fallback default. string name of image size

  $meta = get_post_meta($post->ID);

  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;
  $render_description = isset($args['render-description']) && empty($args['render-description']) ? false : true; // $args array turns false on an index to an empty value and true to 1-so check if set but empty gives us the false value
?>
<article <?php post_class($args['grid-item-classes']); ?> id="post-<?php the_ID(); ?>">
  <a href="<?php the_permalink() ?>">
    <?php the_post_thumbnail($image_size, array('class' => 'index-post-thumbnail')); ?>
<?php
    switch ($text_size) {
      case 'regular':
?>
    <h5 class="index-post-title font-size-9 font-weight-bold margin-top-tiny js-fix-widows"><?php render_post_title($post->ID); ?></h5>
    <?php
      if ($is_article) {
    ?>
    <h6 class="font-size-8 font-weight-bold text-uppercase margin-top-micro"><?php render_bylines($post->ID, false); ?></h6>
    <?php } ?>
    <div class="index-post-description font-size-9 margin-top-tiny">
      <?php
        if ($render_description) {
          if ($description) {
            echo $description;
          } else {
            the_excerpt();
          }
        }
      ?>
    </div>
<?php
        break;
      case 'large':
?>
    <h3 class="font-size-10 font-weight-bold margin-top-tiny js-fix-widows"><?php render_post_title($post->ID); ?></h3>
    <?php
      if ($is_article) {
    ?>
    <h3 class="font-size-10 font-weight-bold font-weight-bold">by <?php render_bylines($post->ID, false); ?></h3>
    <?php } ?>
    <div class="index-post-description margin-top-tiny">
      <?php
        if ($render_description) {
          if ($description) {
            echo $description;
          } else {
            the_excerpt();
          }
        }
      ?>
    </div>
<?php
        break;
    }
?>
  </a>
</article>
