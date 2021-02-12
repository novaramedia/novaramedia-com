<?php
  $meta = get_post_meta($post->ID);
//   $is_liveblog = get_post_meta( $post->ID, 'novara_live_updates_enabled', true );
?>

<div class="flex-grid-row margin-bottom-basic">
  <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-7 flex-offset-m-1 flex-item-l-6 flex-offset-l-1 flex-item-xl-5 flex-offset-xl-1 flex-item-xxl-5 flex-offset-xxl-1 margin-bottom-basic">
    <h1 id="single-articles-title" class="margin-bottom-small js-fix-widows"><?php the_title(); ?></h1>
    
    <?php
      if (!empty($meta['_cmb_standfirst'])) {
    ?><h2 class="margin-bottom-small js-fix-widows"><?php echo $meta['_cmb_standfirst'][0]; ?></h2>
    <?php
      }
    ?>
    
    <h3>by <?php
      if (!empty($meta['_cmb_author'])) {
        echo $meta['_cmb_author'][0];
      } else {
        echo 'Novara Reporters';
      } // >>>TODO deal with twitter links
    ?></h3>
    <h3><?php the_time('j F Y'); ?></h3>
  </div>

  <div class="flex-grid-item flex-item-s-12 flex-item-m-4 flex-item-l-5 flex-item-xl-5 flex-item-xxl-5">
    <?php the_post_thumbnail('col20'); ?>
    <div class="font-smaller">
      <?php the_post_thumbnail_caption(); ?>
    </div>
  </div>
</div>