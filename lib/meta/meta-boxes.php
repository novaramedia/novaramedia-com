<?php
/**
 * Metabox for Page Slug
 *
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

  if ( $meta_box['show_on']['key'] !== 'slug' ) {
    return $display;
  }

  $post_id = 0;

  // If we're showing it based on ID, get the current ID
  if ( isset( $_GET['post'] ) ) {
    $post_id = wp_unslash( $_GET['post'] );
  } elseif ( isset( $_POST['post_ID'] ) ) {
    $post_id = wp_unslash( $_POST['post_ID'] );
  }

  if ( ! $post_id ) {
    return $display;
  }

  $slug = get_post( $post_id )->post_name;

  // See if there's a match
  return in_array( $slug, (array) $meta_box['show_on']['value'], true );
}
add_filter( 'cmb2_show_on', 'be_metabox_show_on_slug', 10, 2 );


/**
 * Retrieves post objects and formats them as an associative array for use in dropdowns or select fields.
 *
 * This function queries posts based on the provided arguments and returns an array
 * where post IDs are keys and post titles are values. This is commonly used for
 * creating dropdown options in meta boxes or custom fields.
 *
 * @param array $query_args Optional. Array of arguments to pass to get_posts().
 *                          Accepts any valid get_posts() parameters.
 *                          Default: array( 'post_type' => 'post' ).
 *
 * @return array Associative array of post options where keys are post IDs and values are post titles.
 *               Returns empty array if no posts are found.
 */
function get_post_objects( $query_args ) {
  $args = wp_parse_args(
    $query_args,
    array(
      'post_type' => 'post',
    )
  );
  $posts = get_posts( $args );
  $post_options = array();
  if ( $posts ) {
    foreach ( $posts as $post ) {
      $post_options [ $post->ID ] = $post->post_title;
    }
  }
  return $post_options;
}
