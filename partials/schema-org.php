<?php
if (nm_is_single_article()) {
  $json_ld = array(
    "@context" => "http://schema.org",
    "@type" => "NewsArticle",
    "headline" => get_the_title(),
    "datePublished" => get_the_time('c'),
    "image" => array(
      get_the_post_thumbnail_url(null, 'col24-16to9'),
    )
  );
} else {
  $facebook = IGV_get_option('_igv_socialmedia_facebook_url');
  $twitter = IGV_get_option('_igv_socialmedia_twitter');
  $instagram = IGV_get_option('_igv_socialmedia_instagram');

  $logo = IGV_get_option('_igv_metadata_logo_id');

  $json_ld = array(
    "@context" => "http://schema.org",
    "@type" => "Organization",
    "url" => home_url(),
  );

  if ($logo) {
    $image = wp_get_attachment_image_src($logo, 'opengraph');

    $json_ld['logo'] = $image[0];
  }

  $same_as_array = array();

  if ($facebook) {
    $same_as_array[] = $facebook;
  }

  if ($twitter) {
    $same_as_array[] = 'https://twitter.com/' . $twitter;
  }

  if ($instagram) {
    $same_as_array[] = 'https://instagram.com/' . $instagram;
  }

  if (count($same_as_array) > 0) {
    $json_ld['sameAs'] = $same_as_array;
  }
}
?>
<script type="application/ld+json"><?php echo json_encode($json_ld); ?></script>