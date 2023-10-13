<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  $post_id = $args['post_id'];
  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);
  $sub_category = get_the_sub_category($post_id);

  $is_product_linked = !empty($args['is_product_linked']) && $args['is_product_linked'] === 'on' ? true : false;
  $more_on_section = !empty($args['more_on_section']) && $args['more_on_section'] !== 'none' ? $args['more_on_section'] : false;
?>
<div class="layout-split-vertical">
  <div>
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <span class="ui-tag ui-tag--no-border"><?php if ($sub_category) { echo $sub_category; } ?></span>
      </div>
      <?php render_thumbnail($post_id, 'col12-16to9'); ?>
    </div>
    <div>
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
  <div class="ui-stick-bottom fs-2">
    <a href="<?php echo $link; ?>"><span class="ui-dot ui-dot--red"></span><?php echo $label; ?></a>
  </div>
  <?php
    }
  ?>
</div>
