<?php

add_action( 'cmb2_init', 'nm_cmb_tyskysour_metaboxes' );

/**
 * Only display a box if the current category is TyskySour
 * @param  object $cmb Current box object
 * @return bool
 */
function nm_is_category_tyskysour( $cmb ) {
  $term = get_term($cmb->object_id);

  if ($term->slug === 'tyskysour-video') {
    return true;
  }

  return false;
}

function nm_cmb_tyskysour_metaboxes() {
  $prefix = '_nm_';

  $cmb_category_tyskysour = new_cmb2_box( array(
    'id'               => $prefix . 'tyksysour_edit',
    'title'            => esc_html__( 'TyskySour Archive Metabox', 'cmb2' ), // Doesn't output for term boxes
    'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
    'taxonomies'       => array( 'category' ), // Tells CMB2 which taxonomies should have these fields
    'show_on_cb' => 'nm_is_category_tyskysour'
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name'     => esc_html__( 'TyskySour specific meta', 'cmb2' ),
    'id'       => $prefix . 'extra_ts',
    'type'     => 'title',
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name' => esc_html__( 'Latest livestream embed ID', 'cmb2' ),
    'desc' => esc_html__( 'YouTube video ID.', 'cmb2' ),
    'id'   => $prefix . 'ts_latest_youtube_id',
    'type' => 'text',
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name' => esc_html__( 'Team image', 'cmb2' ),
    'desc' => esc_html__( '...', 'cmb2' ),
    'id'   => $prefix . 'ts_team_image',
    'type' => 'file',
  ) );

}
