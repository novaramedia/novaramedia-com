<?php
  $meta = get_post_meta($post->ID);
?>
<div class="grid-row mb-4">
  <div class="grid-item is-s-24 offset-s-0 is-m-14 offset-m-2 is-l-12 is-xxl-10 offset-xxl-2 mb-4">
    <h1 id="single-articles-title" class="font-size-15 font-weight-bold mb-4"><?php the_title(); ?></h1>
    <?php
      if (!empty($meta['_cmb_standfirst'])) {
    ?><h2 class="font-size-12 font-weight-bold mb-3 text-wrap-pretty"><?php echo $meta['_cmb_standfirst'][0]; ?></h2>
    <?php
      }
    ?>
    <h3 class="font-size-11 font-weight-bold">by <?php render_bylines($post->ID, true); ?></h3>
    <h3 class="font-size-11 font-weight-bold"><?php the_time('j F Y'); ?></h3>
  </div>
  <div class="grid-item is-s-24 is-m-8 is-xxl-10">
    <?php render_thumbnail($post->ID, 'col20', array(
      'class' => 'ui-rounded-image',
      'data-no-lazysizes' => true,
      'loading' => 'eager'
    )); ?>
    <div class="font-size-8">
      <?php the_post_thumbnail_caption(); ?>
    </div>
  </div>
</div>
