<?php
  $meta = get_post_meta($post->ID);


  $thumbnail_id = get_post_thumbnail_id($post->ID);
  $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));

  $category_id = get_cat_ID('Wire');
  $category_link = get_category_link( $category_id );
?>

<div class="row margin-bottom-basic">
  <div class="col col24">
    <h4><a href="<?php echo $category_link; ?>">Wire</a></h4>
  </div>
</div>

<div class="row">
  <div class="col col24 text-align-center u-position-relative">
    <?php the_post_thumbnail('col24-wire-crop'); ?>
    <div id="single-wire-photo-caption" class="font-smaller">
      <?php
        if (!empty($thumbnail_image[0]->post_excerpt)) {
          echo $thumbnail_image[0]->post_excerpt;
        }
      ?>
    </div>
  </div>
</div>

<div class="row margin-top-small margin-bottom-basic">
  <div class="col col2"></div>
  <div class="col col20">
    <h1 id="single-wire-title" class="js-fix-widows"><?php the_title(); ?></h1>
  </div>
</div>

<div class="row margin-bottom-small">
  <div class="col col3"></div>
  <div class="col col13">
    <h3>by <?php
      if (!empty($meta['_cmb_author_twitter'])) {
        echo '<a id="single-wire-author" target="_blank" href="https://twitter.com/' . $meta['_cmb_author_twitter'][0] . '">';
      }

      if (!empty($meta['_cmb_author'])) {
        echo $meta['_cmb_author'][0];
      } else {
        echo 'Novara Reporters';
      }

      if (!empty($meta['_cmb_author_twitter'])) {
        echo '</a>';
      }
    ?></h3>
    <?php
      if (!empty($meta['_cmb_author'])) {
        echo '<a target="_blank" href="https://twitter.com/' . $meta['_cmb_author_twitter'][0] . '">';
        echo '<h5>@' . $meta['_cmb_author_twitter'][0] . '</h5>';
        echo '</a>';
      }
    ?>
  </div>
  <div class="col col3 font-smaller">
    <?php
      if (!empty($meta['_igv_reading_time'])) {
        echo '<span>Estimated read time: ';
        if ($meta['_igv_reading_time'][0] > 1) {
          echo $meta['_igv_reading_time'][0] . 'mins';
        } else {
          echo $meta['_igv_reading_time'][0] . 'min';
        }
        echo '</span></span> ';
      }
    ?>

    <?php get_template_part('partials/social-sharing'); ?>
  </div>
  <div class="col col3 font-smaller">
    <div class="kindleWidget" style="display:inline-block;cursor:pointer;white-space:nowrap;">Send to Kindle</div>
  </div>
</div>

<div class="row">
  <div class="col col4"></div>
  <div class="col col16">
    <div id="single-wire-copy" class="text-copy margin-top-basic margin-bottom-basic">
      <?php the_content(); ?>
    </div>

    <div id="single-wire-meta" class="font-smaller">
      <?php
      if (!empty($meta['bitly_url'])) {
        echo '<p>Share URL: <span class="u-pointer js-select">' . $meta['bitly_url'][0] . '</span></p> ';
      }
      ?>
      <p id="single-wire-publication-date">Published <?php the_time('jS F Y'); ?></p>
      <p>This work by Novara Media is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License</p>
    </div>
  </div>
</div>