<?php
/**
 * Meta field validation loader.
 *
 * Required / conditional validation of CMB2 meta fields runs client-side;
 * the JS lives in src/admin/meta-validation/ and builds to dist/admin/.
 * This file builds the category slug => term-IDs map both editor adapters
 * consume and enqueues the right bundle per editor.
 *
 * To enable on a CMB2 meta field set the attributes parameters
 * [note that booleans must be strings]
 *
 * 'attributes' => array(
 *   'data-validation' => 'true',
 *   'data-validation-word-length' => 14,
 *   'data-validation-required' => 'true',
 *   'data-validation-required-category' => 'video',
 * )
 *
 * Non-group wysiwyg fields render via wp_editor(), which does not output the
 * CMB2 'attributes' array — mark those with an editor class instead:
 *
 * 'options' => array(
 *   'editor_class' => 'nm-validation-required'
 * )
 *
 * Rules: data-validation-required = must be non-empty (whitespace-only and
 * markup-only count as empty); data-validation-required-category = required
 * only when the post is in that category slug or a descendant;
 * data-validation-word-length = maximum word count. Drafts and previews
 * save freely; only publish-type saves validate.
 */

/**
 * Slug-keyed map of category term IDs (self + descendants) so field markup
 * can name categories by slug — term IDs drift across installs. Only post
 * edit screens can use category-conditional rules, so the map stays empty
 * elsewhere rather than querying terms on options pages.
 *
 * @return array<string, int[]>
 */
function nm_meta_validation_category_map() {
  $category_map = array();

  $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

  if ( ! $screen || 'post' !== $screen->base ) {
    return $category_map;
  }

  foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
    $ids = array_map( 'intval', get_term_children( $category->term_id, 'category' ) );

    array_unshift( $ids, (int) $category->term_id );

    $category_map[ $category->slug ] = $ids;
  }

  return $category_map;
}

/**
 * Enqueue one admin validation bundle with its wp-scripts asset metadata.
 *
 * @param string $handle Script handle.
 * @param string $entry  Entry basename under dist/admin/.
 */
function nm_meta_validation_enqueue( $handle, $entry ) {
  $asset_path = get_template_directory() . '/dist/admin/' . $entry . '.asset.php';

  if ( ! file_exists( $asset_path ) ) {
    return;
  }

  $asset = require $asset_path;

  wp_enqueue_script(
    $handle,
    get_template_directory_uri() . '/dist/admin/' . $entry . '.js',
    $asset['dependencies'],
    $asset['version'],
    true
  );

  wp_localize_script(
    $handle,
    'nmMetaValidation',
    array( 'categoryMap' => nm_meta_validation_category_map() )
  );
}

/**
 * Classic bundle: hook cmb2_after_form so the script loads exactly where a
 * CMB2 form rendered (post edit screens, Links Bar and fundraising options
 * pages) — same reach as the old inline print. The bundle self-no-ops when
 * no known form / no validated fields exist, and the block editor page has
 * no #post form, but skip it there explicitly anyway.
 */
function nm_meta_validation_enqueue_classic( $post_id, $cmb ) {
  $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

  if ( $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
    return;
  }

  nm_meta_validation_enqueue( 'nm-meta-validation-classic', 'meta-validation-classic' );
}

add_action( 'cmb2_after_form', 'nm_meta_validation_enqueue_classic', 10, 2 );
