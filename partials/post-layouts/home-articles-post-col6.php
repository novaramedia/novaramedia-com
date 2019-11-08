<?php
  $meta = get_post_meta($post->ID);
?>
<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col6'); ?> id="post-<?php the_ID(); ?>">

    <?php the_post_thumbnail('col6-16to9', array('class' => 'related-post-thumbnail only-desktop')); ?>
    <?php the_post_thumbnail('mobile-21to9', array('class' => 'related-post-thumbnail only-mobile')); ?>

    <h5 class="margin-top-tiny mobile-margin-bottom-micro js-fix-widows"><?php render_post_title($post->ID); ?></h5>
<?php
    if (!empty($meta['_cmb_author'])) {
?>
    <h6>by <?php echo $meta['_cmb_author'][0]; ?></h6>
<?php
    }
?>
  </article>
</a>
