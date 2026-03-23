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
    'name' => esc_html__( 'Map embed URL', 'cmb2' ),
    'desc' => esc_html__( 'Figma embed URL for the interactive map section', 'cmb2' ),
    'id'   => $prefix . 'dyor_map_embed_url',
    'type' => 'text_url',
  ) );
}
