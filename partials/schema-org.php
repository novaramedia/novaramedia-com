<?php
  $facebook = IGV_get_option('_igv_socialmedia_facebook_url');
  $twitter = IGV_get_option('_igv_socialmedia_twitter');
  $instagram = IGV_get_option('_igv_socialmedia_instagram');

  $logo = IGV_get_option('_igv_metadata_logo');
?>
<script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "Organization",
    "url": "<?php echo site_url(); ?>",
    <?php
      if ($logo) {
        // I think here I need to get the correct size of this image from an ID?
        echo '"logo": "' . $logo . '",';
      }
    ?>
    "sameAs" : [
      <?php
        if (!empty($facebook)) {
          echo '"' . $facebook . '",';
        }

        if (!empty($twitter)) {
          echo '"https://twitter.com/' . $twitter . '",';
        }

        if (!empty($instagram)) {
          echo '"https://instagram.com/' . $instagram . '",';
        }
      ?>
      ]
  }
</script>