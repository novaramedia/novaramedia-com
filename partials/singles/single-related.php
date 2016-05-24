<?php
  if (has_term(null, 'focus', $post->ID)) {
    $terms = get_the_terms($post->ID, 'focus');
    if ($terms) {
      $focusPosts = new WP_Query(array(
        'posts_per_page' => 4,
        'post__not_in' => array($post->ID),
        'tax_query' => array(
          array(
            'taxonomy' => 'focus',
            'field' => 'term_id',
            'terms' => $terms[0]->term_id
          ),
        ),
      ));
      if ($focusPosts->have_posts()) {
        $focusIds = [];
?>
<div class="row margin-bottom-small">
  <div class="col col24">
    <h4><a href="<?php echo get_term_link($terms[0]); ?>">More in this Focus on <?php echo $terms[0]->name; ?></a></h4>
  </div>
</div>
<div class="row margin-bottom-basic">
<?php
        while ($focusPosts->have_posts()) {
          $focusPosts->the_post();
          $focusIds[] = $post->ID;
          get_template_part('partials/post-layouts/post-col6');
        }
?>
</div>
<?php
      wp_reset_postdata();
      }
    }
  }

  if (isset($focusIds)) {
    $related = get_related_posts($focusIds);
  } else {
    $related = get_related_posts();
  }

  if ($related->have_posts()) {
?>
<div class="row margin-bottom-small">
  <div class="col col24">
    <h4>Related</h4>
  </div>
</div>
<div class="row">
<?php
    while ($related->have_posts()) {
      $related->the_post();
      get_template_part('partials/post-layouts/post-col6');
    }
?>
</div>
<?php
  wp_reset_postdata();
  }
?>
