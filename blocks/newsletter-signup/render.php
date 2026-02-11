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

$newsletter = $attributes['newsletter'] ?? null;

if ( ! $newsletter || empty( $newsletter['id'] ) ) {
  return; // No newsletter selected
}

$newsletter_id = absint( $newsletter['id'] );
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

// If no mailchimp key, don't render anything
if ( empty( $mailchimp_key ) ) {
  return;
}

$wrapper_attributes = get_block_wrapper_attributes(
    array(
    'class' => 'mb-4',
  )
);
?>
<div <?php echo $wrapper_attributes; ?>>
  <div class="background-gray-base ui-rounded-box p-4">
    <h3 class="font-size-12 font-weight-bold mb-2 text-wrap-pretty"><?php echo esc_html( $headline ); ?></h3>
    <?php if ( ! empty( $description ) ) { ?>
      <p class="font-size-10 mb-3 text-wrap-balance"><?php echo esc_html( $description ); ?></p>
      <?php
    }

    if ( function_exists( 'render_mailchimp_signup_form' ) ) {
      render_mailchimp_signup_form( $mailchimp_key, 'white', 'black' );
    }
    ?>
  </div>
</div>
