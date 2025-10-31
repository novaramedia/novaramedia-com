<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action( 'cmb2_init', 'nm_cmb_posttype_event_metaboxes' );

/**
 * Registers custom meta boxes for the Event post type in the WordPress admin.
 */
function nm_cmb_posttype_event_metaboxes() {
  $prefix = '_cmb_';

  $event_meta_boxes = new_cmb2_box(
    array(
      'id'           => 'event_metabox',
      'title'        => __( 'Event Meta', 'cmb' ),
      'object_types' => array( 'event' ), // Post type
      'context'      => 'normal',
      'priority'     => 'high',
      'show_names'   => true, // Show field names on the left
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'Event time', 'cmb' ),
      'id'   => $prefix . 'time',
      'type' => 'text_datetime_timestamp',
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'Venue name', 'cmb' ),
      'id'   => $prefix . 'venue_name',
      'type' => 'text',
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'Venue postcode', 'cmb' ),
      'id'   => $prefix . 'venue_postcode',
      'type' => 'text',
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name'       => __( 'Speakers', 'cmb' ),
      'id'         => $prefix . 'speakers',
      'type'       => 'text',
      'repeatable' => true,
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'Host', 'cmb' ),
      'id'   => $prefix . 'host',
      'type' => 'text',
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'Tickets link', 'cmb' ),
      'id'   => $prefix . 'tickets',
      'type' => 'text_url',
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'Sold out', 'cmb' ),
      'id'   => $prefix . 'tickets_sold_out',
      'type' => 'checkbox',
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'YouTube recording', 'cmb' ),
      'desc' => __( 'Enter the YouTube video ID for the event recording.', 'cmb' ),
      'id'   => $prefix . 'youtube',
      'type' => 'text',
    )
  );

  $event_meta_boxes->add_field(
    array(
      'name' => __( 'Gallery', 'cmb' ),
      'id'   => $prefix . 'gallery',
      'type' => 'wysiwyg',
    )
  );
}
