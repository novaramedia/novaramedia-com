<?php
  $meta = get_post_meta($post->ID);

  $thumbnail_id = get_post_thumbnail_id($post->ID);
  $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));
?>

<div class="row">
  <div class="col col24">
    <h4>Wire</h4>
  </div>
</div>

<div class="row">
  <div class="col col24 text-align-center u-position-relative">
    <?php the_post_thumbnail('col24-wire-crop'); ?>
    <div id="single-wire-photo-caption">
      <?php
        if (!empty($thumbnail_image[0]->post_excerpt)) {
          echo $thumbnail_image[0]->post_excerpt;
        }
      ?>
    </div>
  </div>
</div>

<div class="row">
  <div class="col col2"></div>
  <div class="col col20">
    <h1 class="js-fix-widows"><?php the_title(); ?></h1>
  </div>
</div>

<div class="row">
  <div class="col col3"></div>
  <div class="col col14">
    <h5>by <?php
      if (!empty($meta['_cmb_author_twitter'])) {
        echo '<a href="https://twitter.com' . $meta['_cmb_author_twitter'][0] . '">';
      }

      if (!empty($meta['_cmb_author'])) {
        echo $meta['_cmb_author'][0];
      } else {
        echo 'Novara Reporters';
      }

      if (!empty($meta['_cmb_author_twitter'])) {
        echo '</a>';
      }
    ?></h5>
  </div>
  <div class="col col4">
    // social sharing
  </div>
</div>

<div class="row">
  <div class="col col4"></div>
  <div class="col col16">
    <div id="single-wire-copy" class="margin-bottom-basic">
      <?php the_content(); ?>
    </div>

    <div id="single-wire-meta" class="font-smaller">
      <p>Published <?php the_time('jS F Y'); ?></p>
      <p>This work by Novara Media is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License</p>
    </div>
  </div>
</div>