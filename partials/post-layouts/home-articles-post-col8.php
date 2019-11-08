<?php
  $meta = get_post_meta($post->ID);
?>

<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">

    <?php the_post_thumbnail('col8-16to9', array('class' => 'related-post-thumbnail')); ?>

    <h5 class="margin-top-tiny margin-bottom-micro font-larger js-fix-widows"><?php render_post_title($post->ID); ?></h5>
<?php
    if (!empty($meta['_cmb_author'])) {
?>
    <h5>by <?php echo $meta['_cmb_author'][0]; ?></h5>
<?php
    }
?>
  </article>
</a>
