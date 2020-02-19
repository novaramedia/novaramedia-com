<?php
$twitter = IGV_get_option('_igv_socialmedia_twitter');

if ($twitter) {
  echo '<meta name="twitter:site" value="' . $twitter . '">';
}

$fbAppId = IGV_get_option('_igv_og_fb_app_id');

if ($fbAppId) {
  echo '<meta name="fb:app_id" value="' . $fbAppId . '">';
}

?>
  <meta property="og:title" content="<?php wp_title('|', true, 'right'); bloginfo('name'); ?>" />
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
  <meta name="twitter:card" value="summary_large_image">
<?php
global $post;

if (has_post_thumbnail($post)) {
  $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'opengraph');
}

$ogImage = wp_get_attachment_image_src(IGV_get_option('_igv_og_image_id'), 'opengraph');

if (!empty($thumb) && is_single()) {
  echo '<meta property="og:image" content="' . $thumb[0] . '" />';
} else if (!empty($ogImage)) {
  echo '<meta property="og:image" content="' . $ogImage[0] . '" />';
} else {
  echo '<meta property="og:image" content="' . get_stylesheet_directory_uri() . '/img/dist/favicon.png" />';
}

if (is_home()) {
?>
  <meta property="og:url" content="<?php bloginfo('url'); ?>"/>
  <meta property="og:description" content="<?php bloginfo('description'); ?>" />
  <meta property="og:type" content="website" />
<?php
} else if (is_single()) {
  global $post;

  $twitterAuthor = get_post_meta($post->ID, '_cmb_author_twitter', true);

  if ($twitterAuthor) {
    echo '<meta name="twitter:creator" value="' . $twitterAuthor . '">';
  }

  $description = get_post_meta($post->ID, '_cmb_short_desc', true);

  if ($description) {
    // strip shortcodes from short_desc
    $excerpt = strip_shortcodes($description);
    // strip html tags
    $excerpt = strip_tags(html_entity_decode($excerpt));
  } else {
    // strip shortcodes from post_content
    $excerpt = strip_shortcodes($post->post_content);
    // strip html tags
    $excerpt = strip_tags(html_entity_decode($excerpt));
    // trim post content by 600 chars
    $excerpt = substr($excerpt, 0, 600);
    // add ... to end
    $excerpt = $excerpt . '…';
  }

  // clean special cars
  $excerpt = htmlspecialchars($excerpt);
?>
  <meta property="og:url" content="<?php the_permalink(); ?>"/>
  <meta property="og:description" content="<?php echo $excerpt; ?>" />
  <meta property="og:type" content="article" />
<?php
} else {
?>
  <meta property="og:url" content="<?php the_permalink() ?>"/>
  <meta property="og:description" content="<?php bloginfo('description'); ?>" />
  <meta property="og:type" content="website" />
<?php
}
?>