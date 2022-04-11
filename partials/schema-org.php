<?php
if (is_single() && get_post_type() === 'job') {
    $meta = get_post_meta($post->ID);

    $location = !empty($meta['_nm_location'][0]) ? $meta['_nm_location'][0] : false;
    $type = !empty($meta['_nm_contract'][0]) ? $meta['_nm_contract'][0] : '';
    $deadlline = !empty($meta['_nm_deadline'][0]) ? $meta['_nm_deadline'][0] : '';
    $rate = !empty($meta['_nm_rate'][0]) ? $meta['_nm_rate'][0] : '';

    $json_ld = array(
    "@context" => "http://schema.org",
    "@type" => "JobPosting",
    "title" => get_the_title(),
    "description" => get_the_content(),
    "identifier" => array(
      "@type" => "PropertyValue",
      "name" => "Novara Media",
      "value" => get_the_ID(),
    ),
    "datePosted" => get_the_time('c'),
    "validThrough" => date('c', $deadlline),
    "employmentType" => $type,
    "hiringOrganization" => array(
      "@type" => "Organization",
      "name" => "Novara Media",
      "sameAs" => home_url(),
    ),
    "baseSalary" => array(
      "@type" => "MonetaryAmount",
      "currency" => "GBP",
      "value" => array(
        "@type" => "QuantitativeValue",
        "value" => $rate,
        "unitText" => "HOUR",
      ),
    ),
  );

    $address_london = array(
    "@type" => "Place",
    "address" => array(
      "@type" => "PostalAddress",
      "streetAddress" => "100 Drummond Rd",
      "addressLocality" => "Bermondsey",
      "addressRegion" => "London",
      "postalCode" => "SE16 4DG",
      "addressCountry" => "UK",
    ),
  );

    $address_leeds = array(
    "@type" => "Place",
    "address" => array(
      "@type" => "PostalAddress",
      "streetAddress" => "The Leeming Building",
      "addressLocality" => "Ludgate Hill",
      "addressRegion" => "Leeds",
      "postalCode" => "LS2 7HZ",
      "addressCountry" => "UK",
    ),
  );

    switch ($location) {
    case 'remote':
      $json_ld['applicantLocationRequirements']= array(
        "@type" => "Country",
        "name" => "UK",
      );
      $json_ld['jobLocationType']= "TELECOMMUTE";
      break;
    case 'flexi':
      $json_ld['jobLocation'] = array($address_london, $address_leeds);
      $json_ld['jobLocationType']= "TELECOMMUTE";
      break;
    case 'london':
      $json_ld['jobLocation'] = $address_london;
      break;
    case 'leeds':
      $json_ld['jobLocation'] = $address_leeds;
      break;
  }
} elseif (nm_is_single_article()) {
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
