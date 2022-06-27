<?php
  $meta = get_post_meta($post->ID);
//   $is_liveblog = get_post_meta( $post->ID, 'novara_live_updates_enabled', true );
?>

<div class="flex-grid-row margin-bottom-basic">
  <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-9 flex-offset-l-1 flex-item-xl-8 flex-offset-xl-2 flex-item-xxl-8 flex-offset-xxl-2">
    <h1 id="single-articles-title" class="js-fix-widows"><?php the_title(); ?></h1>
  </div>
</div>

<div class="flex-grid-row margin-bottom-mid">
  <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-8 flex-offset-l-2 flex-item-xl-6 flex-offset-xl-3 flex-item-xxl-6 flex-offset-xxl-3">

    <?php
      if (!empty($meta['_cmb_standfirst'])) {
    ?><h2 class="margin-bottom-small js-fix-widows"><?php echo $meta['_cmb_standfirst'][0]; ?></h2>
    <?php
      }
    ?>

    <h3>by <?php render_bylines($post->ID, true); ?></h3>
    <h3><?php the_time('j F Y'); ?></h3>
  </div>
</div>

<div class="flex-grid-row margin-bottom-basic">
  <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-12 flex-offset-m-0 flex-item-l-10 flex-offset-l-1 flex-item-xl-10 flex-offset-xl-1 flex-item-xxl-10 flex-offset-xxl-1">
    <?php the_post_thumbnail('col20'); ?>
    <div class="font-smaller">
      <?php the_post_thumbnail_caption(); ?>
    </div>
  </div>
</div>
