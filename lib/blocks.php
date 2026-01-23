<?php
/**
 * Gutenberg Blocks Registration
 *
 * Registers all custom Gutenberg blocks defined in the theme's blocks directory.
 * Each block should have a block.json file in its directory.
 *
 * @package flavor3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers all theme blocks from the blocks directory.
 *
 * Scans the /blocks directory for subdirectories containing block.json files
 * and registers each one as a WordPress block type.
 *
 * @return void
 */
function nm_register_theme_blocks() {
	$blocks_dir = get_template_directory() . '/blocks';

	if ( ! file_exists( $blocks_dir ) ) {
		return;
	}

	$block_directories = glob( $blocks_dir . '/*/block.json' );

	foreach ( $block_directories as $block_json ) {
		register_block_type( dirname( $block_json ) );
	}
}
add_action( 'init', 'nm_register_theme_blocks' );
