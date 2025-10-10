<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
add_action( 'cmb2_init', 'nm_cmb_metaboxes_posttype_contributor' );

function nm_cmb_metaboxes_posttype_contributor() {
  $prefix = '_nm_contributor_';

  $contributor_meta_boxes = new_cmb2_box( array (
    'id'         => 'contributor_metabox',
    'title'      => __( 'Contributor Meta', 'cmb' ),
    'object_types'      => array( 'contributor' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $contributor_meta_boxes->add_field( array(
    'name'    => __( 'Web link (optional)', 'cmb' ),
    'desc'    => 'Full URL. Expectation is Twitter but can be personal website or organisation website.',
    'id'      => $prefix . 'link',
    'type'    => 'text_url',
  ) );

  $contributor_meta_boxes->add_field( array(
    'name'    => __( 'Short bio', 'cmb' ),
    'desc'    => 'E.g. for use at the end of an article.',
    'id'      => $prefix . 'short_bio',
    'type'    => 'textarea',
  ) );

}
