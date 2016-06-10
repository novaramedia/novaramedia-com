<?php
  $meta = get_post_meta($post->ID);
?>

<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col12'); ?> id="post-<?php the_ID(); ?>">

    <div class="post-col12-image">
    <?php the_post_thumbnail('col12-1to2point3', array('class' => 'u-display-block')); ?>

      <div class="post-col12-text font-color-white">
        <h2 class="js-fix-widows"><?php the_title(); ?></h2>
    <?php
        if (!empty($meta['_cmb_author'])) {
    ?>
        <h3>by <?php echo $meta['_cmb_author'][0]; ?></h3>
    <?php
        }
    ?>

      </div>
    </div>
  </article>
</a>