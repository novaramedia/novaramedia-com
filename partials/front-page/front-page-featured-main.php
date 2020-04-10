<?php
  $meta = get_post_meta($post->ID);
  $timestamp = get_post_time('c');
?>

<a href="<?php the_permalink() ?>">
  <article <?php post_class('front-page-featured margin-bottom-basic'); ?> id="post-<?php the_ID(); ?>">
    <?php the_post_thumbnail('col12-16to9', array('class' => 'margin-bottom-micro')); ?>

    <?php
      $sub_category = get_the_sub_category($post->ID);

      if ($sub_category) {
    ?>
    <h4 class="front-page-featured__meta font-small-caps"><?php echo $sub_category; ?> <span class="js-time-since" data-timestamp="<?php echo $timestamp; ?>"></h4>
    <?php
      }
    ?>
    <h3 class="front-page-featured__title js-fix-widows"><?php the_title(); ?></h3>
    <?php if (!empty($meta['_cmb_author'])) { ?><h6 class="front-page-featured__author font-larger">by <?php echo $meta['_cmb_author'][0]; ?></h6><?php } ?>

    <?php
      if (!empty($meta['_cmb_short_desc'])) {
    ?>
      <div class="margin-top-small"><?php echo $meta['_cmb_short_desc'][0]; ?></div>
    <?php
      }
    ?>
  </article>
</a>