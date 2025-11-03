<?php
  if (!is_numeric($args['post_id'])) {
    return;
  }

  if (!isset($args['has_huge_headline'])) {
    $args['has_huge_headline'] = true;
  }

  if (!isset($args['has_embed'])) {
    $args['has_embed'] = false;
  }

  $post_id = $args['post_id'];
  $has_huge_headline = $args['has_huge_headline'];
  $has_embed = $args['has_embed'];

  $the_title = get_the_title($post_id);

  if (str_word_count($the_title) > 14) { // if the title if long then no huge headline
    $has_huge_headline = false;
  }

  $meta = get_post_meta($post_id);
  $is_article = nm_is_article($post_id);

  if (empty($meta['_cmb_utube'])) {
    $has_embed = false;
  }

  $show_related = !empty($args['show_related']) && $args['show_related'] !== 'none' ? $args['show_related'] : false;

  if ($show_related && !empty($meta['_cmb_related_posts'])) {
    $related_args = array(
      'posts_per_page' => 1,
      'post__in' => explode(', ', $meta['_cmb_related_posts'][0])
    );

    $related_posts = new WP_Query($related_args);

    if ($related_posts->have_posts()) {
      $has_related = true;
    } else {
      $has_related = false;
    }
  } else {
    $has_related = false;
  }

  $is_product_linked = !empty($args['is_product_linked']) && $args['is_product_linked'] === 'on' ? true : false;
  $more_on_section = !empty($args['more_on_section']) && $args['more_on_section'] !== 'none' ? $args['more_on_section'] : false;

  if ($has_embed) {
?>
<div class="u-video-embed-container background-black">
  <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url($meta['_cmb_utube'][0]); ?>" allow="<?php echo get_youtube_iframe_allow_attr(); ?>" allowfullscreen></iframe>
</div>
<?php
  } else {
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
<?php
  }
?>
<div class="grid-row grid--nested mt-3">
  <div class="grid-item is-s-24 <?php echo ($show_related && !empty($meta['_cmb_related_posts'])) ? 'is-l-16 is-xxl-18' : 'is-xl-24 is-xxl-22'; ?>">
    <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
      <h2 class="post__title <?php echo $has_huge_headline ? 'font-size-15 font-size-m-13' : 'font-size-13'; ?> font-weight-bold mb-3"><?php echo $the_title; ?></h2>
<?php
  if (!$has_related) {
  // surprizing conditional here: this is so that the title can either have it's own wider box or not depending on the display of related posts
?>
    </a>
  </div>
  <div class="grid-item is-s-24 <?php echo ($has_related) ? 'is-m-16 is-xxl-18' : 'is-m-20 is-xxl-18'; ?>">
    <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
<?php
  // end of surprizing conditional
  }
?>
      <h5 class="font-size-8 font-weight-bold text-uppercase">
        <?php
          if ($is_article) {
            render_bylines($post_id);
          } else {
            render_standfirst($post_id);
          }
        ?>
      </h5>
      <div class="font-size-9 mt-2 mb-0">
        <?php render_short_description($post_id); ?>
      </div>
    </a>
  </div>
  <?php
    if ($has_related) {
      if ($related_posts->have_posts()) {
  ?>
  <div class="grid-item is-s-24 is-m-8 is-xxl-6 ui-border-left mt-s-3 ui-border--not-s">
    <?php render_see_also($related_posts); ?>
  </div>
  <?php
      }
      wp_reset_postdata();
    }
  ?>
</div>
<?php
if ($is_product_linked || $more_on_section) {
  if ($is_product_linked) {
    $product_term_object = get_the_sub_category($post_id, true);

    if (empty($product_term_object)) {
      return;
    }

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
<div class="mt-2 font-size-8 font-weight-bold only-desktop">
  <a href="<?php echo $link; ?>" class="ui-hover"><span class="ui-dot ui-dot--red"></span><?php echo $label; ?></a>
</div>
<?php
}
?>
