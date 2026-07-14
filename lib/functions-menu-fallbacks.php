<?php
/**
 * Menu Fallback Functions
 *
 * Provides fallback content for WordPress menus when no custom menu is assigned.
 * These functions ensure the site always displays navigation even if menus aren't configured.
 *
 * Each callback receives the wp_nav_menu() args array and honours the
 * menu_class requested by the calling template.
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
 * @param array $args wp_nav_menu() arguments passed to the fallback.
 * @return void
 */
function nm_header_general_fallback( $args = array() ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'font-weight-bold mb-3';
	?>
	<ul class="<?php echo esc_attr( $menu_class ); ?>">
		<li><a href="<?php echo esc_url( site_url( 'about/' ) ); ?>">About Us</a></li>
		<li><a href="<?php echo esc_url( site_url( 'support/' ) ); ?>">Support Us</a></li>
		<li><a href="<?php echo esc_url( site_url( 'newsletters/' ) ); ?>">Newsletters</a></li>
		<li><a href="<?php echo esc_url( site_url( 'about/how-we-are-funded/' ) ); ?>">How We Are Funded</a></li>
		<li><a href="<?php echo esc_url( 'https://shop.novaramedia.com' ); ?>">Merch Shop</a></li>
	</ul>
	<?php
}

/**
 * Fallback for footer general menu.
 *
 * Displays default navigation links for the footer general section.
 * Uses the same links as header general, plus Pitching and Jobs.
 *
 * @param array $args wp_nav_menu() arguments passed to the fallback.
 * @return void
 */
function nm_footer_general_fallback( $args = array() ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'font-weight-bold mb-4';
	?>
	<ul class="<?php echo esc_attr( $menu_class ); ?>">
		<li><a href="<?php echo esc_url( site_url( 'about/' ) ); ?>">About Us</a></li>
		<li><a href="<?php echo esc_url( site_url( 'support/' ) ); ?>">Support Us</a></li>
		<li><a href="<?php echo esc_url( site_url( 'newsletters/' ) ); ?>">Newsletters</a></li>
		<li><a href="<?php echo esc_url( site_url( 'about/how-we-are-funded/' ) ); ?>">How We Are Funded</a></li>
		<li><a href="<?php echo esc_url( 'https://shop.novaramedia.com' ); ?>">Merch Shop</a></li>
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
 * @param array $args wp_nav_menu() arguments passed to the fallback.
 * @return void
 */
function nm_manage_donation_fallback( $args = array() ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'font-weight-bold mb-4';
	?>
	<ul class="<?php echo esc_attr( $menu_class ); ?>">
		<li><a href="<?php echo esc_url( 'https://donate.novaramedia.com/profile' ); ?>">&#10142; Manage Donation</a></li>
	</ul>
	<?php
}

/**
 * Fallback for footer legal menu.
 *
 * Displays default legal links (Terms & Conditions, Privacy Policy).
 *
 * @param array $args wp_nav_menu() arguments passed to the fallback.
 * @return void
 */
function nm_footer_legal_fallback( $args = array() ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'font-weight-bold';
	?>
	<ul class="<?php echo esc_attr( $menu_class ); ?>">
		<li><a href="<?php echo esc_url( site_url( 'terms-and-conditions/' ) ); ?>">Terms &amp; Conditions</a></li>
		<li><a href="<?php echo esc_url( site_url( 'privacy-policy/' ) ); ?>">Privacy Policy</a></li>
	</ul>
	<?php
}
