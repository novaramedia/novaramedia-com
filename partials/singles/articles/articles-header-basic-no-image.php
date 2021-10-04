<?php
  $meta = get_post_meta($post->ID);
//   $is_liveblog = get_post_meta( $post->ID, 'novara_live_updates_enabled', true );
?>

<div class="flex-grid-row margin-bottom-basic">
  <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-10 flex-offset-l-1 flex-item-xl-8 flex-offset-xl-2 flex-item-xxl-6 flex-offset-xxl-3">
    <h1 id="single-articles-title" class="margin-bottom-small"><?php the_title(); ?></h1>
    
    <?php
      if (!empty($meta['_cmb_standfirst'])) {
    ?><h2 class="margin-bottom-small js-fix-widows"><?php echo $meta['_cmb_standfirst'][0]; ?></h2>
    <?php
      }
    ?>
    
    <?php get_template_part('partials/singles/articles/components/articles-header-author'); ?>
    <h3><?php the_time('j F Y'); ?></h3>
  </div>
</div>