<?php
    $categories = get_the_category();

    $topLevelCategory = array_filter($categories, 'only_top_level_category_filter');
    $topLevelCategory = array_values($topLevelCategory);
    $topLevelCategory = $topLevelCategory[0]->name;
?>
<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">

    <?php the_post_thumbnail('col8-16to9', array('class' => 'index-post-thumbnail')); ?>

    <h5 class="index-post-title margin-top-tiny margin-bottom-tiny js-fix-widows"><?php echo $topLevelCategory . ': '; the_title(); ?></h5>

  </article>
</a>