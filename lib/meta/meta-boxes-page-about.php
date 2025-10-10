<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
* Registers CMB2 group fields to capture roles and persons assigned for the about page.
*
* @param class $cmbInstance The CMB2 instance to register the group field with.
* @param integer $numberOfGroups The number of groups of this type to be registered.
* @param string $title The title of these group fields.
* @param string $label The description of these group fields.
*/
function createAboutColumnGroupFields($cmbInstance, $numberOfGroups, $title, $label) {
  for ($i = 0; $i < $numberOfGroups; $i++) {
    $group_field = $cmbInstance->add_field( array(
      'id'          => 'about_page_team_group_' . sanitize_title($title) . '-' . ($i + 1),
      'type'        => 'group',
      'name'        => $title . ' (column ' . ($i + 1) . ')',
      'description' => __( $label, 'cmb2' ),
      'options'     => array(
        'group_title'       => __( 'Entry {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
        'add_button'        => __( 'Add Another Entry', 'cmb2' ),
        'remove_button'     => __( 'Remove Entry', 'cmb2' ),
        'sortable'          => true,
      ),
    ) );

    $cmbInstance->add_group_field( $group_field, array(
      'name' => 'Role',
      'id'   => 'title',
      'type' => 'text',
    ) );

    $cmbInstance->add_group_field( $group_field, array(
      'name' => 'Name',
      'id'   => 'name',
      'type' => 'textarea_code',
      'repeatable' => true,
    ) );
  }
}

add_action( 'cmb2_init', 'nm_cmb_page_about_metaboxes' );

function nm_cmb_page_about_metaboxes() {
  // Start with an underscore to hide fields from custom fields list
  $prefix = '_cmb_';

  $about_page_meta_boxes = new_cmb2_box( array (
    'id'         => 'about_page_meta',
    'title'      => __( 'About Page Meta', 'cmb' ),
    'object_types' => array( 'page' ), // Post type
    'show_on' => array('key' => 'slug', 'value' => 'about'),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $about_page_meta_boxes->add_field( array(
    'name'    => __( 'Team and Associates sections', 'cmb' ),
    'id'      => $prefix . 'about_title_memebers',
    'type'    => 'title',
  ) );

  createAboutColumnGroupFields($about_page_meta_boxes, 4, 'Team roles and members', 'Use an entry for each role and fill with member names');

  createAboutColumnGroupFields($about_page_meta_boxes, 2, 'Associates roles and names', 'Use an entry for each role and fill with associate names');

  $about_page_meta_boxes->add_field( array(
    'name'    => __( 'Contact and information section', 'cmb' ),
    'id'      => $prefix . 'about_title_contact',
    'type'    => 'title',
  ) );

  $contact_group_field = $about_page_meta_boxes->add_field( array(
    'id'          => 'about_page_contact_group',
    'type'        => 'group',
    'name'        => 'Contact links',
    'description' => __( 'Contact and information links go here', 'cmb2' ),
    'options'     => array(
      'group_title'       => __( 'Entry {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
      'add_button'        => __( 'Add Another Entry', 'cmb2' ),
      'remove_button'     => __( 'Remove Entry', 'cmb2' ),
      'sortable'          => true,
    ),
  ) );

  $about_page_meta_boxes->add_group_field($contact_group_field, array(
    'name' => 'Title',
    'id'   => 'title',
    'type'    => 'text',
  ) );

  $about_page_meta_boxes->add_group_field($contact_group_field, array(
    'name' => 'Email',
    'description' => 'If set this link will create an email to this address',
    'id'   => 'email',
    'type'    => 'text_email',
  ) );

  $about_page_meta_boxes->add_group_field($contact_group_field, array(
    'name' => 'Link',
    'id'   => 'link',
    'type' => 'post_search_text',
    'post_type'   => array('page', 'notice'),
    'select_behavior' => 'replace',
  ) );

  $about_page_meta_boxes->add_field( array(
    'name'    => __( 'Funding Section', 'cmb' ),
    'id'      => $prefix . 'about_funding',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );

  $funding_group_field = $about_page_meta_boxes->add_field( array(
    'id'          => 'about_page_funding_group',
    'type'        => 'group',
    'name'        => 'Grant funds',
    'description' => __( 'Add specific grant and project funding here', 'cmb2' ),
    'options'     => array(
      'group_title'       => __( 'Entry {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
      'add_button'        => __( 'Add Another Entry', 'cmb2' ),
      'remove_button'     => __( 'Remove Entry', 'cmb2' ),
      'sortable'          => true,
    ),
  ) );

  $about_page_meta_boxes->add_group_field($funding_group_field, array(
    'name' => 'Text',
    'id'   => 'text',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 3, ),
  ) );

  $about_page_meta_boxes->add_group_field($funding_group_field, array(
    'name' => 'Image',
    'id'   => 'image',
    'type'    => 'file',
  ) );

  $about_page_meta_boxes->add_field( array(
    'name'    => __( 'Regulation Section', 'cmb' ),
    'id'      => $prefix . 'about_regulation',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );

  $about_page_meta_boxes->add_field( array(
    'name'    => __( 'Legal Section', 'cmb' ),
    'id'      => $prefix . 'about_legal',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );
}
?>
