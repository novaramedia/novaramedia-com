<?php
$twitter = IGV_get_option('_igv_socialmedia_twitter');

if ($twitter) {
  echo '<meta name="twitter:site" value="' . $twitter . '">';
}

$fbAppId = IGV_get_option('_igv_og_fb_app_id');

if ($fbAppId) {
  echo '<meta name="fb:app_id" value="' . $fbAppId . '">';
}

$og_image_id = IGV_get_option('_igv_og_image_id');

if (!empty($og_image_id)) {
  $thumb = wp_get_attachment_image_src($og_image_id, 'opengraph');
  $open_graph_image = $thumb[0];
} else {
  $open_graph_image = get_stylesheet_directory_uri() . '/img/dist/favicon.png';
}

if (is_home()) {
?>
  <meta property="og:image" content="<?php echo $open_graph_image; ?>" />
  <meta property="og:url" content="<?php bloginfo('url'); ?>"/>
  <meta property="og:title" content="<?php wp_title('|', true, 'right'); bloginfo('name'); ?>" />
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:description" content="<?php bloginfo('description'); ?>" />
  <meta name="twitter:card" value="<?php bloginfo('description'); ?>">
<?php
} elseif (is_single()) {
    global $post;
    $meta = get_post_meta($post->ID);
    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'opengraph');

    if (!empty($meta['_cmb_short_desc'])) {
      $excerpt = htmlspecialchars($meta['_cmb_short_desc'][0]);
    } else {
      $excerpt = htmlspecialchars(apply_filters('the_excerpt', get_post_field('post_excerpt', $post->ID)));
    }

?>
  <meta property="og:image" content="<?php echo $thumb[0]; ?>" />
  <meta property="og:url" content="<?php the_permalink(); ?>"/>
  <meta property="og:title" content="<?php wp_title('|', true, 'right'); bloginfo('name'); ?>" />
  <meta property="og:description" content="<?php echo $excerpt; ?>" />
  <meta property="og:type" content="article" />
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
  <meta property="og:article:published_time" content="<?php echo get_the_time('c', $post->ID); ?>" />
<?php
  if (!empty($meta['_cmb_author'])) {
?>
  <meta property="og:article:author" content="<?php echo $meta['_cmb_author'][0]; ?>" />
<?php
  }

} else {
?>
  <meta property="og:image" content="<?php echo $open_graph_image; ?>" />
  <meta property="og:url" content="<?php the_permalink() ?>"/>
  <meta property="og:title" content="<?php wp_title('|', true, 'right'); bloginfo('name'); ?>" />
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
  <meta property="og:description" content="<?php bloginfo('description'); ?>" />
  <meta property="og:type" content="website" />
<?php
}
?>