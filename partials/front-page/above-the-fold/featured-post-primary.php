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
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <?php render_post_ui_tags($post_id, true, true, 'no-border'); ?>
      </div>
      <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
        <?php render_thumbnail($post_id, 'col24-16to9', array(
          'class' => 'ui-rounded-image',
          'data-no-lazysizes' => true,
          'loading' => 'eager'
        )); ?>
      </a>
    </div>

    <div class="grid-row grid--nested mt-3">
      <div class="grid-item is-s-24 <?php echo ($show_related && !empty($meta['_cmb_related_posts'])) ? 'is-l-16 is-xxl-18' : 'is-xxl-24'; ?>">
        <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
          <h2 class="post__title fs-8 fs-m-7"><?php echo get_the_title($post_id); ?></h2>
          <h5 class="fs-2 font-uppercase mt-3">
            <?php
              if ($is_article) {
                render_bylines($post_id);
              } else {
                render_standfirst($post_id);
              }
            ?>
          </h5>
          <div class="fs-3-sans mt-2 mb-0">
            <?php render_short_description($post_id); ?>
          </div>
        </a>
      </div>
      <?php
        if ($show_related) {
          if (!empty($meta['_cmb_related_posts'])) {
            $related_args = array(
              'posts_per_page' => 2,
              'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
            );

            $related_posts = new WP_Query($related_args);

            if ($related_posts->have_posts()) {
      ?>
      <div class="grid-item is-s-24 is-l-8 is-xxl-6 ui-border-left mt-s-3 ui-border--not-s">
        <h4 class="fs-2 font-uppercase mb-2 mb-s-1">See Also</h4>
        <div class="related-posts">
      <?php
          $i = 0;
          while ($related_posts->have_posts()) {
            $related_posts->the_post();
      ?>
          <div class="mb-2 <?php if ($i != 0) { echo 'only-desktop'; } ?>">
            <a href="<?php the_permalink(); ?>" class="ui-hover">
              <h5 class="fs-4-sans"><?php the_title(); ?></h5>
              <h6 class="fs-2 font-uppercase mt-1"><?php render_bylines($post->ID, false); ?></h6>
            </a>
          </div>
      <?php
            $i++;
          }
      ?>
        </div>
      </div>
      <?php
            }
            wp_reset_postdata();
          }
        }
      ?>
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
  <div class="mt-2 fs-2 only-desktop">
    <a href="<?php echo $link; ?>" class="ui-hover"><span class="ui-dot ui-dot--red"></span><?php echo $label; ?></a>
  </div>
  <?php
    }
  ?>
