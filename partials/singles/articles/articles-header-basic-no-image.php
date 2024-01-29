<?php
  $meta = get_post_meta($post->ID);
?>
<div class="grid-row mb-4">
  <div class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-18 offset-l-2 is-xl-16 offset-xl-4 is-xxl-16 offset-xxl-4">
    <h1 id="single-articles-title" class="fs-8 mb-3"><?php the_title(); ?></h1>
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
