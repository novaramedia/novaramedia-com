<?php
add_action( 'cmb2_init', 'nm_cmb_tyskysour_metaboxes' );

/**
 * Only display a box if the current category is TyskySour
 * @param  object $cmb Current box object
 * @return bool
 */
function nm_cmb_is_category_tyskysour( $cmb ) {
  $term = get_term($cmb->object_id);

  if (isset($term->slug) && ($term->slug === 'tyskysour-video')) {
    return true;
  }

  return false;
}

/**
 * Declares CMB2 metaboxes for the TyskySour category
 */
function nm_cmb_tyskysour_metaboxes() {
  $prefix = '_nm_';

  $cmb_category_tyskysour = new_cmb2_box( array(
    'id'               => $prefix . 'tyksysour_edit',
    'title'            => esc_html__( 'TyskySour Archive Metabox', 'cmb2' ), // Doesn't output for term boxes
    'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
    'taxonomies'       => array( 'category' ), // Tells CMB2 which taxonomies should have these fields
    'show_on_cb' => 'nm_cmb_is_category_tyskysour'
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name'     => esc_html__( 'TyskySour specific meta', 'cmb2' ),
    'desc'      => esc_html__( 'TS only metadata', 'cmb2' ),
    'id'       => $prefix . 'extra_ts',
    'type'     => 'title',
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name' => esc_html__( 'Latest livestream embed ID', 'cmb2' ),
    'desc' => esc_html__( 'YouTube video ID. First embed on the TyksySour page', 'cmb2' ),
    'id'   => $prefix . 'ts_latest_youtube_id',
    'type' => 'text',
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name' => esc_html__( 'Front page image', 'cmb2' ),
    'desc' => esc_html__( 'Displays above the fold on the front page', 'cmb2' ),
    'id'   => $prefix . 'ts_frontpage_image',
    'type' => 'file',
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name' => esc_html__( 'Front page title', 'cmb2' ),
    'desc' => esc_html__( 'Displays above the fold on the front page', 'cmb2' ),
    'id'   => $prefix . 'ts_frontpage_title',
    'type' => 'text',
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name' => esc_html__( 'Front page copy', 'cmb2' ),
    'desc' => esc_html__( 'Displays above the fold on the front page (optional)', 'cmb2' ),
    'id'   => $prefix . 'ts_frontpage_copy',
    'type' => 'textarea_small',
  ) );

  $cmb_category_tyskysour->add_field( array(
    'name' => esc_html__( 'Team image', 'cmb2' ),
    'desc' => esc_html__( 'Displays on the TyskySour page.', 'cmb2' ),
    'id'   => $prefix . 'ts_team_image',
    'type' => 'file',
  ) );
}
