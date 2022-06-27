<?php
  $meta = get_post_meta($post->ID);
  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;

  $content_type = get_the_top_level_category(get_the_ID()); // get top level catergory for content type
  $is_article = $content_type->category_nicename === 'articles' ? true : false; // check if is article for display layout
?>
<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">
    <?php the_post_thumbnail('col8-16to9', array('class' => 'index-post-thumbnail')); ?>
    <h5 class="index-post-title margin-top-tiny js-fix-widows"><?php render_post_title($post->ID); ?></h5>
    <?php if ($is_article) { ?>
    <h6 class="margin-top-micro font-bold">by <?php render_bylines($post->ID, false); ?></h6>
    <?php } ?>
    <div class="index-post-description margin-top-tiny">
      <?php
        if ($description) {
          echo $description;
        } else {
          the_excerpt();
        }
      ?>
    </div>
  </article>
</a>
