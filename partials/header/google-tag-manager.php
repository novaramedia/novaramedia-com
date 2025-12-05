<?php
  $google_tag_manager_id = IGV_get_option('_igv_gtm_id');

  if (!empty($google_tag_manager_id)) {
    // Get custom metadata for dataLayer
    $custom_data = nm_get_custom_metadata_for_datalayer();
?>
<script>
  dataLayer = [<?php echo json_encode( $custom_data ); ?>];
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $google_tag_manager_id; ?>');
</script>
<?php
  }
?>