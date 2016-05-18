<?php

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

/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */
/**
 * Hook in and add metaboxes. Can only happen on the 'cmb2_init' hook.
 */

add_action( 'cmb2_admin_init', 'igv_cmb_metaboxes' );

function igv_cmb_metaboxes() {
  // Start with an underscore to hide fields from custom fields list
  $prefix = '_cmb_';


	$meta_boxes = new_cmb2_box( array ( 
		'id'         => 'post_metabox',
		'title'      => __( 'Post Meta', 'cmb' ),
		'object_types'      => array( 'post' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
  ) );


  $meta_boxes->add_field( array(
    'name'    => __( 'Short description', 'cmb' ),
    'desc'    => __( '...', 'cmb' ),
    'id'      => $prefix . 'short_desc',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Misc download', 'cmb' ),
    'desc' => __( 'Upload an file or enter a URL.', 'cmb' ),
    'id'   => $prefix . 'dl',
    'type' => 'file',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Redirect', 'cmb' ),
    'desc' => __( 'For TXT posts to new Wire reposts', 'cmb' ),
    'id'   => $prefix . 'redirect',
    'type' => 'text_url',
  ) );

  // FM
	$fm_metabox = new_cmb2_box( array (
		'id'         => 'fm_metabox',
		'title'      => __( 'FM Meta', 'cmb' ),
		'object_types'      => array( 'post' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
  ) );

  $fm_metabox->add_field( array(
    'name' => __( 'Soundcloud URL', 'cmb' ),
    'desc' => __( 'Enter a URL.', 'cmb' ),
    'id'   => $prefix . 'sc',
    'type' => 'text_url',
  ) );

  $fm_metabox->add_field( array(
    'name' => __( 'Download URL', 'cmb' ),
    'desc' => __( 'Enter a URL.', 'cmb' ),
    'id'   => $prefix . 'dl_mp3',
    'type' => 'text_url',
  ) );

  // TV
	$tv_metabox = new_cmb2_box( array (
		'id'         => 'tv_metabox',
		'title'      => __( 'TV Meta', 'cmb' ),
		'object_types'      => array( 'post' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
  ) );

  $tv_metabox->add_field( array(
    'name' => __( 'YouTube ID', 'cmb' ),
    'desc' => __( 'Id of youtube video. for example if this is the url https://www.youtube.com/watch?v=CmuDcXfBqTg&feature=c4-overview&list=UUOzMAa6IhV6uwYQATYG_2kg then the Id is the value after the ?v= and before the &, for this link CmuDcXfBqTg', 'cmb' ),
    'id'   => $prefix . 'utube',
    'type' => 'text',
  ) );

  $tv_metabox->add_field( array(
    'name' => __( 'Secondary YouTube ID', 'cmb' ),
    'desc' => __( 'For extended interviews etc', 'cmb' ),
    'id'   => $prefix . 'utube_2',
    'type' => 'text',
  ) );

  // Resources

	$resources_metabox = new_cmb2_box( array(
		'id'         => 'resources_metabox',
		'title'      => __( 'Post Resources', 'cmb' ),
		'object_types' => array( 'post' ),
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
  ) );

  $resources_group_field = $resources_metabox->add_field( array(
    'id'          => $prefix . 'resources',
    'type'        => 'group',
    'options'     => array(
      'group_title'   => __( 'Resource {#}', 'cmb' ), // {#} gets replaced by row number
      'add_button'    => __( 'Add Another Resource', 'cmb' ),
      'remove_button' => __( 'Remove Resource', 'cmb' ),
      'sortable'      => true,
    )
  ) );

  $resources_metabox->add_group_field( $resources_group_field, array(
    'name' => 'Resource Title',
    'id'   => 'title',
    'type' => 'text',
  ) );

  $resources_metabox->add_group_field( $resources_group_field, array(
    'name' => 'Resource Link',
    'id'   => 'link',
    'type' => 'text_url',
  ) );

}
?>
