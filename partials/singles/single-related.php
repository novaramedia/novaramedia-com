<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  $meta = get_post_meta($post->ID);

  if (!empty($meta['_cmb_related_posts'])) {
    $related_args = array(
      'posts_per_page' => 3,
      'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
    );

    $related_posts = new WP_Query($related_args);

    if ($related_posts->have_posts()) {
?>
<section id="single-related" class="container mt-6 mb-7">
  <div class="grid-row mb-4">
    <div class="grid-item is-xxl-24">
      <h4 class="font-size-9 text-uppercase font-weight-bold">Related</h4>
    </div>
  </div>
  <div class="grid-row related-posts">
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
      while ($related_posts->have_posts()) {
        $related_posts->the_post();
          get_template_part('partials/post-layouts/archive-post', null, array(
            'grid-item-classes' => 'grid-item is-s-24 is-xxl-8 mb-4',
            'image-size' => 'col12-16to9',
            'show-tags' => true
          ));
      }
?>
  </div>
</section>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
    }

    wp_reset_postdata();
  }
?>
