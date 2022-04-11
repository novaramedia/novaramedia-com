<?php
  if (empty($args['grid-item-classes'])) { // if no classes set for grid item don't render
      return;
  }

  $text_size = !empty($args['text-size']) ? $args['text-size'] : 'regular'; // get size size parameter with regular text size as default. string matching descriptive values
  $image_size = !empty($args['image-size']) ? $args['image-size'] : 'col24-16to9'; // get image size parameter, with max possible size as fallback default. string name of image size

  $meta = get_post_meta($post->ID);

  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;
  $author = !empty($meta['_cmb_author'][0]) ? $meta['_cmb_author'][0] : false;
?>
<article <?php post_class($args['grid-item-classes']); ?> id="post-<?php the_ID(); ?>">
  <a href="<?php the_permalink() ?>">
    <?php the_post_thumbnail($image_size, array('class' => 'index-post-thumbnail')); ?>
<?php
    switch ($text_size) {
      case 'regular':
?>
    <h5 class="index-post-title margin-top-tiny js-fix-widows"><?php render_post_title($post->ID); ?></h5>
    <?php if ($author) { ?>
    <h6 class="font-bold">by <?php echo $author; ?></h6>
    <?php } ?>
    <div class="index-post-description margin-top-tiny">
      <?php
        if ($description) {
            echo $description;
        } else {
            the_excerpt();
        }
      ?>
    </div>
<?php
        break;

      case 'large':
?>
    <h3 class="font-size-2 margin-top-tiny js-fix-widows"><?php render_post_title($post->ID); ?></h3>
    <?php if ($author) { ?>
    <h3 class="font-size-2 font-bold">by <?php echo $author; ?></h3>
    <?php } ?>
    <div class="index-post-description margin-top-tiny">
      <?php
        if ($description) {
            echo $description;
        } else {
            the_excerpt();
        }
      ?>
    </div>
<?php
        break;
    }
?>
  </a>
</article>
