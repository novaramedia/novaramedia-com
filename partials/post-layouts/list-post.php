<?php
/*
  This post layout is for list views of posts when they need to be displayed for archival type reasons.
  It is used on the single contributor page when the full archive setting is true.
*/

  if (empty($args['grid-item-classes'])) { // if no classes set for grid item don't render
    return;
  }
?>
<div <?php post_class($args['grid-item-classes']); ?>>
  <a href="<?php the_permalink() ?>">
    <?php the_time('j F Y'); ?>
    <h3 class="font-size-1 font-semibold"><?php the_title(); ?></h3>
  </a>
</div>
