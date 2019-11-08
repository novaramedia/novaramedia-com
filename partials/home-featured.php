<?php
  $home_featured = IGV_get_option('_igv_front_feature');

  $home_featured_ids = explode(', ', $home_featured);
  $post_id = $home_featured_ids[0];

  // Check for alt_thumb first
  $thumb_id = get_post_meta($post_id, '_cmb_alt_thumb_id', true);

  // If alt_thumb is empty, fallback to default thumb
  if (empty($thumb_id)) {
    $thumb_id = get_post_thumbnail_id($post_id);
  }

  // Check if has author meta
  $author = get_post_meta($post_id, '_cmb_author', true);

?>
<section id="home-featured" class="container margin-bottom-large mobile-margin-bottom-basic">
  <div class="row">
     <div class="col col24 margin-bottom-small">
      <h4><a href="<?php echo get_permalink($post_id); ?>">Featured</a></h4>
    </div>
  </div>
  <div class="row">
    <a href="<?php echo get_permalink($post_id); ?>">
      <article id="featured-post" class="col col24">
        <?php
          echo wp_get_attachment_image($thumb_id, 'col24-featured-crop', null, array('class' => 'featured-post-thumbnail only-desktop'));
          echo wp_get_attachment_image($thumb_id, 'mobile-5to4', null, array('class' => 'featured-post-thumbnail only-mobile'));
        ?>
        <div id="featured-post-title-holder" class="text-align-center u-flex-center">
          <h1 id="featured-post-title" class="font-color-white text-shadow-gray js-fix-widows"><?php
            echo render_post_title($post_id);

            if ($author) {
              echo ' by '. $author;
            }

          ?></h1>
        </div>
      </article>
    </a>
  </div>
</section>