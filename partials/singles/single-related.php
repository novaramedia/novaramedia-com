<?php
  $meta = get_post_meta($post->ID);

  if (!empty($meta['_cmb_related_posts'])) {
      $related_args = array(
      'posts_per_page' => 3,
      'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
    );

      $related_posts = new WP_Query($related_args);

      if ($related_posts->have_posts()) {
          ?>
<section id="single-related" class="container margin-top-mid margin-bottom-large">
  <div class="row margin-bottom-small">
    <div class="col col24">
      <h4>Related</h4>
    </div>
  </div>
  <div class="related-posts row">
<?php
      while ($related_posts->have_posts()) {
          $related_posts->the_post();
          get_template_part('partials/post-layouts/post-col8');
      } ?>
  </div>
</section>
<?php
      }

      wp_reset_postdata();
  }
?>
