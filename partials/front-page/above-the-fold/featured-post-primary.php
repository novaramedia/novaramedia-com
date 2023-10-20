<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  $post_id = $args['post_id'];
  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);
  $sub_category = get_the_sub_category($post_id);

  $show_related = !empty($args['show_related']) && $args['show_related'] !== 'none' ? $args['show_related'] : false;

  $is_product_linked = !empty($args['is_product_linked']) && $args['is_product_linked'] === 'on' ? true : false;
  $more_on_section = !empty($args['more_on_section']) && $args['more_on_section'] !== 'none' ? $args['more_on_section'] : false;
?>
<div class="layout-split-vertical">
  <div>
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <?php render_post_ui_tags($post_id, true, true); ?>
      </div>
      <?php render_thumbnail($post_id, 'col24-16to9', array(
        'class' => 'ui-rounded-image',
        'data-no-lazysizes' => true,
        'loading' => 'eager'
      )); ?>
    </div>
    <div class="grid-row grid--nested mt-3">
      <div class="grid-item is-xxl-18">
        <h2 class="fs-8 js-fix-widows"><?php echo get_the_title($post_id); ?></h2>
        <h5 class="fs-2 font-uppercase mt-3">
          <?php
            if ($is_article) {
              render_bylines($post_id);
            } else {
              render_standfirst($post_id);
            }
          ?>
        </h5>
        <p class="mt-2 mb-0">
          <?php
            if ($is_article) {
              render_standfirst($post_id);
            } else {
              render_short_description($post_id);
            }
          ?>
        </p>
      </div>
      <div class="grid-item is-xxl-6">
        <?php
          if ($show_related) {
            $meta = get_post_meta($post_id);

            if (!empty($meta['_cmb_related_posts'])) {
              $related_args = array(
                'posts_per_page' => 2,
                'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
              );

              $related_posts = new WP_Query($related_args);

              if ($related_posts->have_posts()) {
            ?>
              <h4 class="fs-2 font-uppercase mb-2">See Also</h4>
              <div class="related-posts">
            <?php
                while ($related_posts->have_posts()) {
                  $related_posts->the_post();
            ?>
                <div class="mb-2">
                  <a href="<?php the_permalink(); ?>">
                    <h5 class="fs-4-sans"><?php the_title(); ?></h5>
                    <h6 class="fs-2 font-uppercase mt-1"><?php render_bylines($post->ID, false); ?></h6>
                  </a>
                </div>
            <?php
                }
            ?>
              </div>
            <?php
              }
              wp_reset_postdata();
            }
          }
        ?>
      </div>
    </div>
  <?php
    if ($is_product_linked || $more_on_section) {
      if ($is_product_linked) {
        $product_term_object = get_the_sub_category($post_id, true);
        $link = get_term_link($product_term_object);
        $media_type = get_the_top_level_category($post_id);

        switch ($media_type->slug) {
          case 'audio':
            $label = 'Listen to more ' . $product_term_object->name;
            break;
          case 'video':
            $label = 'Watch more ' . $product_term_object->name;
            break;
          default:
            $label = 'Read more ' . $product_term_object->name;
        }
      } else {
        $more_on_section = get_term_by('id', $more_on_section, 'section');
        $link = get_term_link($more_on_section);
        $label = 'More ' . $more_on_section->name;
      }
  ?>
  </div>
  <div class="mt-2 fs-2">
    <a href="<?php echo $link; ?>"><span class="ui-dot ui-dot--red"></span><?php echo $label; ?></a>
  </div>
  <?php
    }
  ?>
</div>
