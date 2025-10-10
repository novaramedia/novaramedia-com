<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Metabox for Page Slug
 * @author Tom Morton
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters
 *
 * @param bool $display
 * @param array $meta_box
 * @return bool display metabox
 */
function be_metabox_show_on_slug( $display, $meta_box ) {
  if ( ! isset( $meta_box['show_on']['key'], $meta_box['show_on']['value'] ) ) {
    return $display;
  }

  if ( 'slug' !== $meta_box['show_on']['key'] ) {
    return $display;
  }

  $post_id = 0;

  // If we're showing it based on ID, get the current ID
  if ( isset( $_GET['post'] ) ) {
    $post_id = $_GET['post'];
  } elseif ( isset( $_POST['post_ID'] ) ) {
    $post_id = $_POST['post_ID'];
  }

  if ( ! $post_id ) {
    return $display;
  }

  $slug = get_post( $post_id )->post_name;

  // See if there's a match
  return in_array( $slug, (array) $meta_box['show_on']['value']);
}
add_filter( 'cmb2_show_on', 'be_metabox_show_on_slug', 10, 2 );

/* Get post objects for select field options */
function get_post_objects( $query_args ) {
  $args = wp_parse_args( $query_args, array(
    'post_type' => 'post',
  ) );
  $posts = get_posts( $args );
  $post_options = array();
  if ( $posts ) {
    foreach ( $posts as $post ) {
      $post_options [ $post->ID ] = $post->post_title;
    }
  }
  return $post_options;
}


add_action( 'cmb2_init', 'igv_cmb_metaboxes' );

function igv_cmb_metaboxes() {
  // Start with an underscore to hide fields from custom fields list
  $prefix = '_cmb_';

  // Event

  $meta_boxes = new_cmb2_box( array (
    'id'         => 'event_metabox',
    'title'      => __( 'Event Meta', 'cmb' ),
    'object_types'      => array( 'event' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Event time', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'time',
    'type' => 'text_datetime_timestamp',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Venue name', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'venue_name',
    'type' => 'text',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Venue postcode', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'venue_postcode',
    'type' => 'text',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Speakers', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'speakers',
    'type' => 'text',
    'repeatable' => true,
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Host', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'host',
    'type' => 'text',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Tickets link', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'tickets',
    'type' => 'text_url',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Sold out', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'tickets_sold_out',
    'type' => 'checkbox',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'YouTube recording', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'youtube',
    'type' => 'text',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Gallery', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'gallery',
    'type' => 'wysiwyg',
  ) );
}
?>
