<?php
$twitter = IGV_get_option('_igv_socialmedia_twitter');

if ($twitter) {
  echo '<meta name="twitter:site" value="' . $twitter . '">';
}

$fbAppId = IGV_get_option('_igv_og_fb_app_id');

if ($fbAppId) {
  echo '<meta name="fb:app_id" value="' . $fbAppId . '">';
}

// Getting values from current post [should this be in the is_single() section?]
if (have_posts()) {
  while (have_posts()) {
    the_post();
      $excerpt = get_the_excerpt();
      if (has_post_thumbnail()) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'opengraph' );
      }
  }
}

$ogImage = IGV_get_option('_igv_og_image');

if (!empty($thumb) && is_single()) {
  echo '<meta property="og:image" content="' . $thumb[0] . '" />';
} else if (!empty($ogImage)) {
  // I think here I need to get the correct size of this image from an ID?
  echo '<meta property="og:image" content="' . $ogImage . '" />';
} else {
  echo '<meta property="og:image" content="' . get_stylesheet_directory_uri() . '/img/dist/favicon.png" />';
}

if (is_home()) {
?>
  <meta property="og:url" content="<?php bloginfo('url'); ?>"/>
  <meta property="og:title" content="<?php bloginfo('name'); ?>" />
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:description" content="<?php bloginfo('description'); ?>" />
  <meta name="twitter:card" value="<?php bloginfo('description'); ?>">
<?php
} elseif (is_single()) {
?>
  <meta property="og:url" content="<?php the_permalink(); ?>"/>
  <meta property="og:title" content="<?php the_title(); ?>" />
  <meta property="og:description" content="<?php echo htmlspecialchars($excerpt) ?>" />
  <meta property="og:type" content="article" />
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
<?php
} else {
?>
  <meta property="og:url" content="<?php the_permalink() ?>"/>
  <meta property="og:title" content="<?php the_title(); ?>" />
  <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
  <meta property="og:description" content="<?php bloginfo('description'); ?>" />
  <meta property="og:type" content="website" />
<?php
}
?>