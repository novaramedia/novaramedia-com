<?php
  $meta = get_post_meta($post->ID);
?>

<a href="<?php the_permalink() ?>">
  <article <?php post_class('margin-bottom-small'); ?> id="post-<?php the_ID(); ?>">
    <?php
      if (!empty($meta['_cmb_alt_thumb_id'])) {
        echo wp_get_attachment_image($meta['_cmb_alt_thumb_id'][0], 'col6-16to9', false, array('class' => 'margin-bottom-micro only-desktop'));
        echo wp_get_attachment_image($meta['_cmb_alt_thumb_id'][0], 'mobile-16to9', false, array('class' => 'only-mobile'));
      } else {
        the_post_thumbnail('col6-16to9', array('class' => 'margin-bottom-micro only-desktop'));
        the_post_thumbnail('mobile-16to9', array('class' => 'only-mobile'));
      }
    ?>

    <?php
      $sub_category = get_the_sub_category($post->ID);

      if ($sub_category) {
    ?>
    <h4 class="font-small-caps"><?php echo $sub_category; ?></h4>
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