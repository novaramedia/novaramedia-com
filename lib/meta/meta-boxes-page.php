<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
add_action( 'cmb2_init', 'nm_cmb_page_metaboxes' );

function nm_cmb_page_metaboxes() {
  // Start with an underscore to hide fields from custom fields list
  $prefix = '_cmb_';

  $page_meta_boxes = new_cmb2_box( array (
    'id'         => 'page_metabox',
    'title'      => __( 'Page Meta', 'cmb' ),
    'object_types'      => array( 'page' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $page_meta_boxes->add_field( array(
    'name'    => __( 'Short description', 'cmb' ),
    'desc'    => __( 'A few sentences to describe this page. (optional)', 'cmb' ),
    'id'      => $prefix . 'short_desc',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );

  $page_meta_boxes->add_field( array(
    'name'    => __( '2nd Column', 'cmb' ),
    'desc'    => __( '(optional) (on Support page this shows under the Already a supporter? heading)', 'cmb' ),
    'id'      => $prefix . 'page_extra',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );

  $page_meta_boxes->add_field( array(
    'name'    => __( 'Extra Section Title', 'cmb' ),
    'desc'    => __( '(for About page)', 'cmb' ),
    'id'      => $prefix . 'page_extra_section_title',
    'type'    => 'text',
  ) );

  $page_meta_boxes->add_field( array(
    'name'    => __( 'Extra Section', 'cmb' ),
    'desc'    => __( '(for About page)', 'cmb' ),
    'id'      => $prefix . 'page_extra_section',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );
}
?>
