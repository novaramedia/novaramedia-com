<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action( 'cmb2_init', 'nm_cmb_page_support_metaboxes' );

function nm_cmb_page_support_metaboxes() {
  $prefix = '_nm_';

  $support_page_meta_boxes = new_cmb2_box( array (
    'id'         => 'support_page_metabox',
    'title'      => __( 'Support Page Meta', 'cmb' ),
    'object_types' => array( 'page' ), // Post type
    'show_on' => array('key' => 'slug', 'value' => 'support'),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $support_page_meta_boxes->add_field( array(
    'name'    => __( 'Youtube video', 'cmb' ),
    'desc'    => __( '(optional)', 'cmb' ),
    'id'      => $prefix . 'support_youtube',
    'type'    => 'text',
  ) );

  $support_page_meta_boxes->add_field( array(
    'name'    => __( 'Header tag override', 'cmb' ),
    'desc'    => __( 'Replaces Support Us in the top left of the header if set (optional)', 'cmb' ),
    'id'      => $prefix . 'support_tag_override',
    'type'    => 'text',
  ) );

  $support_page_meta_boxes->add_field( array(
    'name'    => __( 'Header title', 'cmb' ),
    'desc'    => __( 'Main title on page', 'cmb' ),
    'id'      => $prefix . 'support_header_title',
    'type'    => 'text',
  ) );

  $support_page_meta_boxes->add_field( array(
    'name'    => __( 'Header subtitle', 'cmb' ),
    'id'      => $prefix . 'support_header_subtitle',
    'type'    => 'text',
  ) );

  $support_page_meta_boxes->add_field( array(
    'name'    => __( 'Donate form tag override', 'cmb' ),
    'id'      => $prefix . 'support_form_tag_override',
    'type'    => 'text',
  ) );

  $support_page_meta_boxes->add_field( array(
    'name'    => __( 'Donate form copy override', 'cmb' ),
    'id'      => $prefix . 'support_form_copy_override',
    'type'    => 'textarea_small',
  ) );
}
