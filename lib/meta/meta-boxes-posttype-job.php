<?php
  
add_action( 'cmb2_init', 'nm_cmb_metaboxes_posttype_job' );

function nm_cmb_metaboxes_posttype_job() {
  $prefix = '_nm_';

  $people_meta_boxes = new_cmb2_box( array (
    'id'         => 'job_metabox',
    'title'      => __( 'Job Meta', 'cmb' ),
    'object_types'      => array( 'job' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $people_meta_boxes->add_field( array(
    'name'    => __( 'Application deadline', 'cmb' ),
    'id'      => $prefix . 'deadline',
    'type'    => 'text_date_timestamp',
  ) );
  
  $people_meta_boxes->add_field( array(
    'name'    => __( 'Contract Type', 'cmb' ),
    'id'      => $prefix . 'contract',
    'type'             => 'select',
    'default'          => 'custom',
    'options'          => array(
        'FULL_TIME' => __( 'Full Time', 'cmb2' ),
        'PART_TIME'   => __( 'Part Time', 'cmb2' ),
        'TEMPORARY'     => __( 'Temporary contract', 'cmb2' ),
      ),
  ) );

  $people_meta_boxes->add_field( array(
    'name'    => __( 'Location', 'cmb' ),
    'id'      => $prefix . 'location',
    'type'             => 'select',
    'default'          => 'custom',
    'options'          => array(
        'remote' => __( 'Remote', 'cmb2' ),
        'flexi'   => __( 'Fully flexible', 'cmb2' ),
        'london'     => __( 'London', 'cmb2' ),
        'leeds'     => __( 'Leeds', 'cmb2' ),
      ),
  ) );
  
  $people_meta_boxes->add_field( array(
    'name'    => __( 'NM Living Wage', 'cmb' ),
    'desc' => esc_html__( 'The hourly rate at time of job posting. Or other rate if temp contract etc', 'cmb2' ),
    'id'      => $prefix . 'rate',
    'type' => 'text_money',
     'before_field' => '£', // Replaces default '$'  
  ) );
}
