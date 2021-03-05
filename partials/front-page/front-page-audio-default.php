<?php
  $meta = get_post_meta($post->ID);
?>
<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col6 margin-bottom-small'); ?> id="post-<?php the_ID(); ?>">
    <?php the_post_thumbnail('col6-16to9', array('class' => 'margin-bottom-micro only-desktop')); ?>
    <?php the_post_thumbnail('mobile-16to9', array('class' => 'only-mobile')); ?>

    <?php
      $sub_category = get_the_sub_category($post->ID);

      if ($sub_category) {
    ?>
    <h5 class="font-small-caps"><?php echo $sub_category; ?></h5>
    <?php
      }
    ?>

    <h5 class="js-fix-widows"><?php the_title(); ?></h5>

    <?php
      if (!empty($meta['_cmb_short_desc'])) {
    ?>
      <div class="margin-top-micro"><?php echo $meta['_cmb_short_desc'][0]; ?></div>
    <?php
      }
    ?>
  </article>
</a>
