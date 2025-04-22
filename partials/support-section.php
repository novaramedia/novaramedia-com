<?php
$override_text = isset( $args['override_text'] ) ? $args['override_text'] : false;
$heading_copy = ! empty( $args['heading_copy'] ) ? $args['heading_copy'] : NM_get_option( 'nm_fundraising_settings_support_section_title', 'nm_fundraising_options' );
$support_section_text = NM_get_option( 'nm_fundraising_settings_support_section_text', 'nm_fundraising_options' );

?>

<div class="font-color-white">
  <div class="background-red p-4 support-form__box-radius m-2">
    <div class="grid-row font-size-10 font-size-s-9 support-section__text-container">
      <div class="grid-row is-m-24 is-l-16 is-xl-12 is-xxl-12">
      <div class="grid-item">
        <a href="<?php echo home_url( 'support/' ); ?>">
          <h4 class="font-size-12 font-weight-bold mb-3"><?php echo $heading_copy; ?></h4>
        </a>
        <?php
        if ( $support_section_text || $override_text ) {
          ?>
          <div class="margin-top-micro margin-bottom-small">
            <a href="<?php echo home_url( 'support/' ); ?>" class="js-fix-widows">
              <?php
              if ( $override_text ) {
                echo $override_text;
              } else {
                echo $support_section_text;
              }
              ?>
              </a>
          </div>
          <?php
        }
        ?>
      </div>
      <div class="mt-2 support-form__payment-type-container grid-item">
        <img class="support-form__payment-type mr-2 ui-rounded-image" src="<?php echo get_template_directory_uri(); ?>/dist/img/support-form/Visa.jpg" alt="visa icon" />
        <img class="support-form__payment-type mr-2 ui-rounded-image" src="<?php echo get_template_directory_uri(); ?>/dist/img/support-form/Mastercard.jpg" alt="mastercard icon" />
        <img class="support-form__payment-type mr-2 ui-rounded-image" src="<?php echo get_template_directory_uri(); ?>/dist/img/support-form/Stripe.jpg" alt="stripe icon" />
        <img class="support-form__payment-type mr-2 ui-rounded-image" src="<?php echo get_template_directory_uri(); ?>/dist/img/support-form/Paypal.jpg" alt="paypal icon" />
        <img class="support-form__payment-type mr-2 ui-rounded-image" src="<?php echo get_template_directory_uri(); ?>/dist/img/support-form/ApplePay.jpg" alt="apple pay icon" />
        <img class="support-form__payment-type mr-2 ui-rounded-image" src="<?php echo get_template_directory_uri(); ?>/dist/img/support-form/GooglePay.jpg" alt="google pay icon" />
      </div>
      </div>
      <div class="is-l-24 offset-xl-0 is-xxl-12 grid-item">
        <?php
        // Reneders the support form from renderers.php
        render_support_form();
        ?>
      </div>
    </div>
  </div>
</div>
