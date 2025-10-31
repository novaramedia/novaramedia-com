<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Each newsletter has metadata set to make choices about how the signup block renders,
 * however there is also the ability to override some of those options by passing them in as arguments to the partial.
 * There are also defaults set for some of these options in case they are not set in the metadata.
 */

$newsletter_post = get_post( $args['newsletter_post_id'] );

if ( ! $newsletter_post ) {
  return;
}

$meta = get_post_meta( $newsletter_post->ID );
$mailchimp_key = ! empty( $meta['_nm_mailchimp_key'] ) ? $meta['_nm_mailchimp_key'][0] : false;

if ( ! $mailchimp_key ) {
  return; // if there isn't a mailchimp key then don't render the signup block
}

// get metadata with fallback defaults
$background_color = ! empty( $meta['_nm_banner_background'] ) ? $meta['_nm_banner_background'][0] : 'black';
$text_color = ! empty( $meta['_nm_banner_text_color'] ) ? $meta['_nm_banner_text_color'][0] : 'white';
$button_color = ! empty( $meta['_nm_banner_button_color'] ) ? $meta['_nm_banner_button_color'][0] : 'red';

$headline = ! empty( $meta['_nm_banner_headline'] ) ? $meta['_nm_banner_headline'][0] : 'Sign up to our newsletter ' . $mailchimp_key;
$copy = ! empty( $meta['_nm_banner_text'] ) ? $meta['_nm_banner_text'][0] : false;
$image_id = ! empty( $meta['_nm_banner_image_id'] ) ? $meta['_nm_banner_image_id'][0] : false;

// override colours if set on the partial $args
if ( ! empty( $args['background-color'] ) ) {
  $background_color = $args['background-color'];
}

if ( ! empty( $args['text-color'] ) ) {
  $text_color = $args['text-color'];
}

if ( ! empty( $args['button-color'] ) ) {
  $button_color = $args['button-color'];
}

$hide_discover = false;

if ( ! empty( $args['hide-discover'] ) ) {
  $hide_discover = $args['hide-discover'];
}
?>
<div class="email-signup mt-4 mb-4">
  <div class="container">
    <div class="grid-row">
      <?php
      if ( $background_color !== 'white' ) { // if the background color is not white, wrap in a box
        ?>
      <div class="grid-item is-xxl-24">
        <div class="grid-row <?php echo 'background-' . $background_color . ' font-color-' . $text_color; ?> ui-rounded-box ui-backgrounded-box-padding">
        <?php
      }
      ?>
          <div class="grid-item is-s-24 is-l-12 is-xxl-10 mb-s-4">
            <h3 class="font-size-14 font-size-s-12 font-weight-bold mb-4 text-wrap-pretty"><?php echo esc_html( $headline ); ?></h3>
            <p class="font-size-12 font-size-s-10 font-weight-bold mr-5 text-wrap-balance">
              <?php echo wp_kses_post( $copy ); ?>
            </p>
            <?php if ( ! $hide_discover ) { ?>
              <div class="mt-3 font-size-8 font-weight-bold">
                <a href="<?php echo site_url( 'newsletters/' ); ?>" class="ui-hover"><span class="ui-dot ui-dot--red"></span>Discover all our newsletters</a>
              </div>
            <?php } ?>
          </div>
          <div class="grid-item offset-l-0 offset-xxl-2 <?php echo $image_id === false ? 'is-s-24 is-m-12 is-l-10 is-xxl-8' : 'is-s-16 is-xxl-8'; ?>">
            <?php render_mailchimp_signup_form( $mailchimp_key, $background_color, $button_color ); ?>
          </div>
          <?php if ( $image_id ) { ?>
            <div class="grid-item is-s-8 is-xxl-4">
              <?php echo wp_get_attachment_image( $image_id, 'col4-square', false, array( 'class' => 'email-signup__image' ) ); ?>
            </div>
          <?php } ?>
        </div>
        <?php
        if ( $background_color !== 'white' ) { // close the box divs if we opened them
          ?>
      </div>
    </div>
          <?php
        }
        ?>
  </div>
</div>
