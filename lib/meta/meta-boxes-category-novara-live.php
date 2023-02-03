<?php
add_action( 'cmb2_init', 'nm_cmb_novara_live_metaboxes' );

/**
 * Only display a box if the current category is Novara Live
 * @param  object $cmb Current box object
 * @return bool
 */
function nm_cmb_is_category_novara_live( $cmb ) {
  $term = get_term($cmb->object_id);

  if (isset($term->slug) && ($term->slug === 'novara-live')) {
    return true;
  }

  return false;
}

/**
 * Declares CMB2 metaboxes for the Novara Live category
 */
function nm_cmb_novara_live_metaboxes() {
  $prefix = '_nm_';

  $cmb_category_novara_live = new_cmb2_box( array(
    'id'               => $prefix . 'novara-live_edit',
    'title'            => esc_html__( 'Novara Live Archive Page Metabox', 'cmb2' ), // Doesn't output for term boxes
    'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
    'taxonomies'       => array( 'category' ), // Tells CMB2 which taxonomies should have these fields
    'show_on_cb' => 'nm_cmb_is_category_novara_live'
  ) );

  $cmb_category_novara_live->add_field( array(
    'name'     => esc_html__( 'Novara Live specific meta', 'cmb2' ),
    'desc'      => esc_html__( 'Settings unique to Novara Live', 'cmb2' ),
    'id'       => $prefix . 'extra_novaralive',
    'type'     => 'title',
  ) );

  $cmb_category_novara_live->add_field( array(
    'name' => esc_html__( 'Latest livestream embed ID', 'cmb2' ),
    'desc' => esc_html__( 'YouTube video ID. First embed on the Novara Live page', 'cmb2' ),
    'id'   => $prefix . 'novaralive_latest_youtube_id',
    'type' => 'text',
  ) );

  $cmb_category_novara_live->add_field( array(
    'name' => esc_html__( 'Front page image', 'cmb2' ),
    'desc' => esc_html__( 'Displays above the fold on the front page', 'cmb2' ),
    'id'   => $prefix . 'novaralive_frontpage_image',
    'type' => 'file',
  ) );

  $cmb_category_novara_live->add_field( array(
    'name' => esc_html__( 'Front page title', 'cmb2' ),
    'desc' => esc_html__( 'Displays above the fold on the front page', 'cmb2' ),
    'id'   => $prefix . 'novaralive_frontpage_title',
    'type' => 'text',
  ) );

  $cmb_category_novara_live->add_field( array(
    'name' => esc_html__( 'Front page copy', 'cmb2' ),
    'desc' => esc_html__( 'Displays above the fold on the front page (optional)', 'cmb2' ),
    'id'   => $prefix . 'novaralive_frontpage_copy',
    'type' => 'textarea_small',
  ) );

  $cmb_category_novara_live->add_field( array(
    'name' => esc_html__( 'Team image', 'cmb2' ),
    'desc' => esc_html__( 'Displays on the Novara Live page.', 'cmb2' ),
    'id'   => $prefix . 'novaralive_team_image',
    'type' => 'file',
  ) );
}
