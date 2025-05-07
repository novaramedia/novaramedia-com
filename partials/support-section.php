<?php
$override_text = isset( $args['override_text'] ) ? $args['override_text'] : false;
$heading_copy = ! empty( $args['heading_copy'] ) ? $args['heading_copy'] : NM_get_option( 'nm_fundraising_settings_support_section_title', 'nm_fundraising_options' );
$support_section_text = NM_get_option( 'nm_fundraising_settings_support_section_text', 'nm_fundraising_options' );

?>

<div class="font-color-white">
  <div class="background-red support-form__box-radius m-2">
     <?php
      // Call the function to render the full support form
      render_support_form( $heading_copy, $support_section_text, $override_text );
      ?>
  </div>
</div>
