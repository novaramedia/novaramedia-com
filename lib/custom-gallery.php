<?php
remove_shortcode('gallery', 'gallery_shortcode');
function my_gallery_shortcode($attr) {
  $post = get_post();

  static $instance = 0;
  $instance++;

  if ( !empty( $attr['ids'] ) ) {

    // 'ids' is explicitly ordered, unless you specify otherwise.
    if ( empty( $attr['orderby'] ) ) {
      $attr['orderby'] = 'post__in';
    }

    $attr['include'] = $attr['ids'];
  }

  // Allow plugins/themes to override the default gallery template.
  $output = apply_filters('post_gallery', '', $attr);
  if ( $output != '' ) {
    return $output;
  }

  // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
  if ( isset( $attr['orderby'] ) ) {
    $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
    if ( !$attr['orderby'] ) {
      unset( $attr['orderby'] );
    }
  }

  // Setup attributes from options reverting to default

  extract(shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post->ID,
    'itemtag'    => 'li',
    'icontag'    => 'li',
    'captiontag' => 'div',
    'columns'    => 3,
    'size'       => 'gallery',
    'include'    => '',
    'exclude'    => ''
  ), $attr));

  $id = intval($id);

  if ( 'RAND' == $order ) {
    $orderby = 'none';
  }

  // Get array of attachments from gallery options

  if ( !empty($include) ) {
    $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

    $attachments = array();

    foreach ( $_attachments as $key => $val ) {
      $attachments[$val->ID] = $_attachments[$key];
    }
  } elseif ( !empty($exclude) ) {

    $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

  } else {

    $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

  }

  // If no attachments return empty string

  if ( empty($attachments) ) {
    return '';
  }

  // If is feed render list of links [?]

  if ( is_feed() ) {
    $output = "\n";
    foreach ( $attachments as $att_id => $attachment )
      $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
    return $output;
  }

  // Set default values if options are invalid

  $itemtag = tag_escape($itemtag);
  $captiontag = tag_escape($captiontag);
  $icontag = tag_escape($icontag);
  $valid_tags = wp_kses_allowed_html('post');

  if ( !isset( $valid_tags[$itemtag] ) ) {
    $itemtag = 'dl';
  }

  if ( !isset( $valid_tags[$captiontag] ) ) {
    $captiontag = 'dd';
  }

  if ( !isset( $valid_tags[$icontag] ) ) {
    $icontag = 'dt';
  }

  // Setup gallery containing markup

  $selector = "gallery-{$instance}";

  $gallery_div = "<div id='$selector' class='swiper-container gallery galleryid-{$id}'><div class='swiper-wrapper'>";
  $output = $gallery_div;

  $i = 0;

  // Build markup from attachments array

  foreach ($attachments as $id => $attachment) {

    $tag = '';

    $img = wp_get_attachment_image($id, $size);

    // If caption is set make a variable of it

    if ( $captiontag && trim($attachment->post_excerpt) ) {
      $tag = "
        <{$captiontag} class='font-smaller'>
        " . wptexturize($attachment->post_excerpt) . "
        </{$captiontag}>";
    } else {
      $tag = null;
    }

    $output .= "<div class='swiper-slide text-align-center u-pointer'>{$img}{$tag}</div>";
  }

  // Finish markup and return

  $output .= "</div></div>\n";

  return $output;
}
add_shortcode('gallery', 'my_gallery_shortcode');