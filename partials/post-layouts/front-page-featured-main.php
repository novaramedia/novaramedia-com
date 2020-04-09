<?php
  $meta = get_post_meta($post->ID);
?>

<a href="<?php the_permalink() ?>">
  <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
    <?php the_post_thumbnail('col12-16to9'); ?>

    <h3 class="js-fix-widows mobile-margin-bottom-micro"><?php render_post_title($post->ID); ?></h3>
    <?php
      if (!empty($meta['_cmb_author'])) {
    ?>
      <h5 class="margin-top-tiny">by <?php echo $meta['_cmb_author'][0]; ?></h5>
    <?php
      }

      if (!empty($meta['_cmb_short_desc'])) {
    ?>
      <p><?php echo $meta['_cmb_short_desc'][0]; ?></p>
    <?php
      }
    ?>
  </article>
</a>