
<div class="grid-row grid--nested<?php if ($args['layout_direction'] === 'rtl') { echo " grid-row--reverse"; } ?>">
  <div class="featured-posts__primary grid-item is-xxl-16">
    <?php
    if (is_numeric($args['post_1'])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-primary', null, array(
        'post_id' => $args['post_1'],
      ));
    }
    ?>
  </div>
  <div class="grid-item is-xxl-8">
  <?php
    if (is_numeric($args['post_2'])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-secondary', null, array(
        'post_id' => $args['post_2'],
      ));
    }

    if (is_numeric($args['post_3'])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
        'post_id' => $args['post_3'],
      ));
    }

    if (is_numeric($args['post_4'])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
        'post_id' => $args['post_4'],
      ));
    }
  ?>
  </div>
</div>
