<?php
/**
 * Menu Fallback Functions
 *
 * Provides fallback content for WordPress menus when no custom menu is assigned.
 * These functions ensure the site always displays navigation even if menus aren't configured.
 *
 * @package NovaraMedia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fallback for header general menu.
 *
 * Displays default navigation links for the main header menu.
 *
 * @return void
 */
function nm_header_general_fallback() {
	?>
	<ul class="font-weight-bold mb-3">
		<li><a href="<?php echo esc_url( site_url( 'about/' ) ); ?>">About Us</a></li>
		<li><a href="<?php echo esc_url( site_url( 'support/' ) ); ?>">Support Us</a></li>
		<li><a href="<?php echo esc_url( site_url( 'newsletters/' ) ); ?>">Newsletters</a></li>
		<li><a href="<?php echo esc_url( site_url( 'about/how-we-are-funded/' ) ); ?>">How We Are Funded</a></li>
		<li><a href="https://shop.novaramedia.com">Merch Shop</a></li>
	</ul>
	<?php
}

/**
 * Fallback for footer general menu.
 *
 * Displays default navigation links for the footer general section.
 *
 * @return void
 */
function nm_footer_general_fallback() {
	?>
	<ul class="font-weight-bold mb-4">
		<li><a href="<?php echo esc_url( site_url( 'about/' ) ); ?>">About</a></li>
		<li><a href="<?php echo esc_url( site_url( 'support/' ) ); ?>">Support Us</a></li>
		<li><a href="<?php echo esc_url( site_url( 'about/how-we-are-funded/' ) ); ?>">How We Are Funded</a></li>
		<li><a href="https://shop.novaramedia.com">Merch Shop</a></li>
		<li><a href="<?php echo esc_url( site_url( 'pitching/' ) ); ?>">Pitching</a></li>
		<li><a href="<?php echo esc_url( site_url( 'jobs/' ) ); ?>">Jobs</a></li>
	</ul>
	<?php
}

/**
 * Fallback for manage donation menu.
 *
 * Displays the manage donation link.
 *
 * @return void
 */
function nm_manage_donation_fallback() {
	?>
	<ul class="font-weight-bold mb-4">
		<li><a href="https://donate.novaramedia.com/profile">&#10142; Manage Donation</a></li>
	</ul>
	<?php
}

/**
 * Fallback for footer legal menu.
 *
 * Displays default legal links (Terms & Conditions, Privacy Policy).
 *
 * @return void
 */
function nm_footer_legal_fallback() {
	?>
	<ul class="font-weight-bold">
		<li><a href="<?php echo esc_url( site_url( 'terms-and-conditions/' ) ); ?>">Terms & Conditions</a></li>
		<li><a href="<?php echo esc_url( site_url( 'privacy-policy/' ) ); ?>">Privacy Policy</a></li>
	</ul>
	<?php
}
