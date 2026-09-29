<?php
add_action( 'cmb2_init', 'nm_cmb_dyor_metaboxes' );

/**
 * Only display a box if the current category is Do Your Own Research
 * @param  object $cmb Current box object
 * @return bool
 */
function nm_cmb_is_category_dyor( $cmb ) {
  $term = get_term( $cmb->object_id );

  if ( isset( $term->slug ) && ( $term->slug === 'do-your-own-research' ) ) {
    return true;
  }

  return false;
}

/**
 * Declares CMB2 metaboxes for the Do Your Own Research category
 *
 * @deprecated 4.11.0 Map settings moved to Products → Do Your Own Research (per season). Kept as the seed source for nm_get_dyor_seasons_seed().
 */
function nm_cmb_dyor_metaboxes() {
  $prefix = '_nm_';

  $cmb_category_dyor = new_cmb2_box( array(
    'id'           => $prefix . 'dyor_edit',
    'title'        => esc_html__( 'Do Your Own Research Metabox', 'cmb2' ),
    'object_types' => array( 'term' ),
    'taxonomies'   => array( 'category' ),
    'show_on_cb'   => 'nm_cmb_is_category_dyor',
  ) );

  $cmb_category_dyor->add_field( array(
    'name' => esc_html__( 'Figma file key', 'cmb2' ),
    'desc' => esc_html__( 'Deprecated: set per season under Products → Do Your Own Research. The file key from the FigJam board URL (e.g. Twc9z7w8yaEzaO6m0PM1Kj). Required for the map embed to display.', 'cmb2' ),
    'id'   => $prefix . 'dyor_figma_file_key',
    'type' => 'text',
  ) );

  $cmb_category_dyor->add_field( array(
    'name' => esc_html__( 'Default map node ID', 'cmb2' ),
    'desc' => esc_html__( 'Deprecated: set per season under Products → Do Your Own Research. Figma node ID for the default map view. Use an invisible bounding rectangle to control the zoom level.', 'cmb2' ),
    'id'   => $prefix . 'dyor_figma_default_node_id',
    'type' => 'text',
  ) );
}
