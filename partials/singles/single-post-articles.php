<?php
  $meta = get_post_meta($post->ID);

  $is_liveblog = get_post_meta( $post->ID, 'novara_live_updates_enabled', true );

  $thumbnail_id = get_post_thumbnail_id($post->ID);
  $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));

  $category_id = get_cat_ID('Articles');
  $category_link = get_category_link( $category_id );
?>

<div class="row margin-bottom-basic mobile-margin-bottom-small">
  <div class="col col24">
    <h4><a href="<?php echo $category_link; ?>">Articles</a></h4>
  </div>
</div>

<div class="row">
  <div class="col col24 text-align-center u-position-relative">
    <?php the_post_thumbnail('col24-widescreen-crop'); ?>
    <div id="single-article-photo-caption" class="font-smaller">
      <?php
        if (!empty($thumbnail_image[0]->post_excerpt)) {
          echo $thumbnail_image[0]->post_excerpt;
        }
      ?>
    </div>
  </div>
</div>

<?php
  if ($is_liveblog === 'on') {
    get_template_part('partials/singles/single-post-articles-liveblog-header');
  } else {
    get_template_part('partials/singles/single-post-articles-header');
  }
?>

<div id="single-articles-copy-row" class="row">
  <div class="col col4"></div>
  <div class="col col16">
    <div id="single-articles-copy" class="text-copy margin-top-basic margin-bottom-basic">
      <?php the_content(); ?>
    </div>

    <div id="single-articles-meta" class="font-smaller">
      <?php
      if (!empty($meta['bitly_url'])) {
        echo '<p>Share URL: <span class="u-pointer js-select">' . $meta['bitly_url'][0] . '</span></p> ';
      }
      ?>
      <p id="single-articles-publication-date">Published <?php the_time('jS F Y'); ?></p>
      <p>This work by Novara Media is licenced under a Creative Commons Attribution-ShareAlike 4.0 International Licence</p>
    </div>
  </div>
</div>