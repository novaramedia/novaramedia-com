<?php
/**
 * Newsletter Signup Block - Server-side render
 *
 * Dynamically renders the newsletter signup form using fresh data from the
 * newsletter custom post type. This approach allows:
 * - Fresh meta data on each render (no stale content in post_content)
 * - Reuse of theme's existing render_mailchimp_signup_form() helper
 * - Consistent styling with the rest of the theme
 *
 * @param array    $attributes Block attributes from the editor.
 * @param string   $content    Block content (empty for dynamic blocks).
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$newsletter    = $attributes['newsletter'] ?? array();
$newsletter_id = absint( $newsletter['id'] ?? 0 );
if ( ! $newsletter_id ) {
  return;
}

// Pull fresh meta from newsletter post
$mailchimp_key = get_post_meta( $newsletter_id, '_nm_mailchimp_key', true );
$headline = get_post_meta( $newsletter_id, '_nm_banner_headline', true );
$description = get_post_meta( $newsletter_id, '_nm_banner_text', true );

// Check for custom overrides from block attributes
if ( ! empty( $attributes['customTitle'] ) ) {
  $headline = $attributes['customTitle'];
} elseif ( empty( $headline ) ) {
  // Fallback to newsletter title if no custom headline or override set
  $headline = get_the_title( $newsletter_id );
}

if ( ! empty( $attributes['customText'] ) ) {
  $description = $attributes['customText'];
}

// The newsletter's own button label, falling back to the generic default.
$button_label = get_post_meta( $newsletter_id, '_nm_banner_button_label', true );

if ( empty( $button_label ) ) {
  $button_label = 'Sign up';
}

// If no mailchimp key, don't render anything
if ( empty( $mailchimp_key ) ) {
  return;
}

$wrapper_attributes = get_block_wrapper_attributes(
    array(
    'class' => 'mb-4',
  )
);

// The Cortado carries its own inline-signup design: ochre box, wordmark instead of a
// headline, and the form beside the copy rather than beneath it. The copy is the short
// _nm_banner_text (or the block's customText override); the archive and front page use the
// category's longer _nm_category_formatted_description instead.
// Its bold spans render in the sans face via .newsletter-signup-cortado__copy strong.
$is_cortado = get_post_field( 'post_name', $newsletter_id ) === 'the-cortado';

if ( $is_cortado ) {
  ?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="background-ochre ui-rounded-box ui-backgrounded-box-padding">
    <div class="grid-row grid-row--nested">
      <div class="grid-item is-l-24 is-xxl-12 mb-l-4">
        <span class="newsletter-signup-cortado__wordmark">
          <?php echo nm_get_file( '/dist/img/products/the-cortado/the-cortado-wordmark.svg' ); ?>
        </span>
        <?php if ( ! empty( $description ) ) { ?>
        <p class="newsletter-signup-cortado__copy font-serif font-size-11 font-size-s-10 mt-2 text-wrap-pretty">
          <?php echo wp_kses_post( $description ); ?>
        </p>
        <?php } ?>
      </div>
      <div class="grid-item is-l-24 is-xxl-12">
        <?php
        if ( function_exists( 'render_mailchimp_signup_form' ) ) {
          render_mailchimp_signup_form( $mailchimp_key, 'white', 'white', $button_label );
        }
        ?>
      </div>
    </div>
  </div>
</div>
  <?php
  return;
}
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="background-white ui-rounded-box p-4">
    <h3 class="font-size-12 font-weight-bold mb-2 text-wrap-pretty"><?php echo esc_html( $headline ); ?></h3>
    <?php if ( ! empty( $description ) ) { ?>
      <p class="font-size-10 mb-3 text-wrap-balance"><?php echo wp_kses_post( $description ); ?></p>
      <?php
    }

    if ( function_exists( 'render_mailchimp_signup_form' ) ) {
      render_mailchimp_signup_form( $mailchimp_key, 'white', 'black', $button_label );
    }
    ?>
  </div>
</div>
