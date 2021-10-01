<?php
  if (empty($args['grid-item-classes'])) {
    return;
  }
  
  $text_size = 'regular';
  
  if (!empty($args['text-size'])) {
    $text_size = $args['text-size'];
  }

  $meta = get_post_meta($post->ID);
  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;
  $author = !empty($meta['_cmb_author'][0]) ? $meta['_cmb_author'][0] : false;
?>

<article <?php post_class($args['grid-item-classes']); ?> id="post-<?php the_ID(); ?>">
  <a href="<?php the_permalink() ?>">
    <?php the_post_thumbnail('col24-16to9', array('class' => 'index-post-thumbnail')); ?>
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
