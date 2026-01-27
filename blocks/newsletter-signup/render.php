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

$newsletter_id = $newsletter['id'];

// Pull fresh meta from newsletter post
$mailchimp_key = get_post_meta( $newsletter_id, '_nm_mailchimp_key', true );
$headline      = get_post_meta( $newsletter_id, '_nm_banner_headline', true );
$description   = get_post_meta( $newsletter_id, '_nm_banner_text', true );

// Fallback to newsletter title if no custom headline set
if ( empty( $headline ) ) {
	$headline = get_the_title( $newsletter_id );
}

// If no mailchimp key, don't render anything
if ( empty( $mailchimp_key ) ) {
	return;
}

// Start output with block wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes();
?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="grid-row">
		<div class="grid-item is-xxl-24">
			<h3 class="fs-8 fs-s-6 mb-4 js-fix-widows"><?php echo esc_html( $headline ); ?></h3>
			<?php if ( ! empty( $description ) ) : ?>
				<p class="fs-6 fs-s-4-sans mr-6"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<div class="grid-item is-xxl-24">
			<?php
			// Use the theme's existing form renderer
			// This ensures consistency with other newsletter signup forms on the site
			if ( function_exists( 'render_mailchimp_signup_form' ) ) {
				render_mailchimp_signup_form( $mailchimp_key, 'white', 'black' );
			}
			?>
		</div>
	</div>
</div>
