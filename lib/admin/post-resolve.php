<?php
/**
 * Post-resolve helper + REST endpoint.
 *
 * Resolves post IDs to the display metadata the admin UI needs (title,
 * status, date, thumbnail). Shared by the post-search field's title hints
 * (server render + live JS updates) and the ATF options preview module.
 * Read-only; returns nothing an editor cannot already see in wp-admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Resolve one post ID to hint/preview metadata.
 *
 * 'found' is false only when no post object exists for the ID. A trashed
 * or draft post is found — its status tells the caller it won't display
 * publicly (spec: red = unresolvable OR status !== publish).
 *
 * @param int $post_id Post ID.
 * @return array{id:int, found:bool, title:?string, status:?string, date:?string, thumbnail:?string}
 */
function nm_resolve_post( $post_id ) {
  $post_id = absint( $post_id );
  $post    = get_post( $post_id );

  if ( ! $post ) {
    return array(
      'id'        => $post_id,
      'found'     => false,
      'title'     => null,
      'status'    => null,
      'date'      => null,
      'thumbnail' => null,
    );
  }

  return array(
    'id'        => $post_id,
    'found'     => true,
    'title'     => get_the_title( $post ),
    'status'    => get_post_status( $post ),
    'date'      => get_the_date( 'j M Y', $post ),
    'thumbnail' => get_the_post_thumbnail_url( $post, 'thumbnail' ) ?: null,
  );
}

/**
 * REST callback: resolve a comma list of post IDs (capped at 20).
 *
 * @param WP_REST_Request $request Request with an 'ids' param.
 * @return WP_REST_Response|WP_Error
 */
function nm_resolve_posts_rest( $request ) {
  $ids = array_filter( array_map( 'absint', explode( ',', (string) $request->get_param( 'ids' ) ) ) );
  $ids = array_slice( array_values( array_unique( $ids ) ), 0, 20 );

  if ( ! $ids ) {
    return new WP_Error( 'nm_resolve_no_ids', 'No valid post IDs supplied.', array( 'status' => 400 ) );
  }

  return rest_ensure_response( array_map( 'nm_resolve_post', $ids ) );
}

function nm_register_resolve_posts_route() {
  register_rest_route(
    'nm/v1',
    '/resolve-posts',
    array(
      'methods'             => WP_REST_Server::READABLE,
      'callback'            => 'nm_resolve_posts_rest',
      'permission_callback' => function () {
        return current_user_can( 'edit_posts' );
      },
      'args'                => array(
        'ids' => array(
          'required'          => true,
          'type'              => 'string',
          'sanitize_callback' => 'sanitize_text_field',
        ),
      ),
    )
  );
}
add_action( 'rest_api_init', 'nm_register_resolve_posts_route' );
