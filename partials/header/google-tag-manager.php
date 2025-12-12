<?php
// Check if dataLayer exists (GTM4WP plugin is active)
// If so, push custom metadata that the plugin doesn't handle
if ( function_exists( 'nm_get_custom_metadata_for_datalayer' ) ) {
  $custom_data = nm_get_custom_metadata_for_datalayer();
  if ( ! empty( $custom_data ) ) {
    ?>
<script>
  if (typeof dataLayer !== 'undefined') {
    dataLayer.push(<?php echo wp_json_encode( $custom_data ); ?>);
  }
</script>
    <?php
  }
}
?>
