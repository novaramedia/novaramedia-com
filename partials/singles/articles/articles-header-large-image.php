<?php
  $meta = get_post_meta($post->ID);
//   $is_liveblog = get_post_meta( $post->ID, 'novara_live_updates_enabled', true );
?>

<div class="grid-row mb-4">
  <div class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-18 offset-l-2 is-xl-16 offset-xl-4 is-xxl-16 offset-xxl-4">
    <h1 id="single-articles-title" class="fs-8 js-fix-widows"><?php the_title(); ?></h1>
  </div>
</div>
<div class="grid-row mb-5">
  <div class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-16 offset-l-4 is-xl-12 offset-xl-6 is-xxl-12 offset-xxl-6">
    <?php
      if (!empty($meta['_cmb_standfirst'])) {
    ?><h2 class="fs-6 mb-3 js-fix-widows"><?php echo $meta['_cmb_standfirst'][0]; ?></h2>
    <?php
      }
    ?>
    <h3 class="fs-5-sans font-weight-bold">by <?php render_bylines($post->ID, true); ?></h3>
    <h3 class="fs-5-sans font-weight-bold"><?php the_time('j F Y'); ?></h3>
  </div>
</div>
<div class="grid-row mb-4">
  <div class="grid-item is-s-24 offset-s-0 is-m-24 offset-m-0 is-l-20 offset-l-2 is-xl-20 offset-xl-2 is-xxl-20 offset-xxl-2">
    <?php render_thumbnail($post->ID, 'col20', array(
      'class' => 'ui-rounded-image',
      'data-no-lazysizes' => true,
      'loading' => 'eager'
    )); ?>
    <div class="font-size-8 font-weight-regular">
      <?php the_post_thumbnail_caption(); ?>
    </div>
  </div>
</div>
