<?php
  $description = get_post_meta($post->ID, '_cmb_short_desc');
?>
<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col6'); ?> id="post-<?php the_ID(); ?>">

    <?php the_post_thumbnail('col6-16to9', array('class' => 'related-post-thumbnail')); ?>

    <h5 class="margin-top-tiny margin-bottom-tiny js-fix-widows"><?php the_title(); ?></h5>

    <div class="post-description">
      <?php
        if (!empty($description)) {
          echo $description[0];
        } else {
          the_excerpt();
        }
      ?>
    </div>

  </article>
</a>
