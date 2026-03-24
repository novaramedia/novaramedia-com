<?php
add_action( 'cmb2_init', 'nm_cmb_dyor_post_metaboxes' );

/**
 * Only display if the post belongs to the Do Your Own Research category
 * @param  object $cmb Current box object
 * @return bool
 */
function nm_cmb_is_post_in_dyor_category() {
  global $post;

  if ( ! $post ) {
    return false;
  }

  return has_category( 'do-your-own-research', $post->ID );
}

/**
 * Declares CMB2 metaboxes for posts in the Do Your Own Research category
 */
function nm_cmb_dyor_post_metaboxes() {
  $prefix = '_nm_';

  $cmb_dyor_post = new_cmb2_box( array(
    'id'           => $prefix . 'dyor_post_edit',
    'title'        => esc_html__( 'Do Your Own Research – Map', 'cmb2' ),
    'object_types' => array( 'post' ),
    'context'      => 'normal',
    'priority'     => 'high',
    'show_on_cb'   => 'nm_cmb_is_post_in_dyor_category',
  ) );

  $cmb_dyor_post->add_field( array(
    'name' => esc_html__( 'Figma node ID', 'cmb2' ),
    'desc' => esc_html__( 'FigJam node ID for this episode on the map (e.g. 125-70)', 'cmb2' ),
    'id'   => $prefix . 'dyor_figma_node_id',
    'type' => 'text',
  ) );
}
