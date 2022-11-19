<?php
  $content_type = get_the_top_level_category(get_the_ID()); // get top level catergory for content type
  $is_article = $content_type->category_nicename === 'articles' ? true : false; // check if is article for display layout

  $meta = get_post_meta($post->ID);
?>

<a href="<?php the_permalink() ?>">
  <article <?php post_class('margin-bottom-small'); ?> id="post-<?php the_ID(); ?>">
    <?php the_post_thumbnail('col6-16to9', array('class' => 'margin-bottom-micro only-desktop')); ?>
    <?php the_post_thumbnail('mobile-16to9', array('class' => 'only-mobile')); ?>

    <?php
      $sub_category = get_the_sub_category($post->ID);

      if ($sub_category) {
    ?>
    <h4 class="font-small-caps"><?php echo $sub_category; ?></h4>
    <?php
      }
    ?>
    <h5 class="font-size-1 js-fix-widows"><?php the_title(); ?></h5>
    <?php
      if ($is_article) {
    ?>
      <h6>by <?php render_bylines($post->ID, false); ?></h6>
    <?php
      }

      if (!empty($meta['_cmb_short_desc'])) {
    ?>
      <div class="margin-top-micro"><?php echo $meta['_cmb_short_desc'][0]; ?></div>
    <?php
      }
    ?>
  </article>
</a>
