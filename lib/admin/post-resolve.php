<?php
/**
 * Post-resolve utility: post IDs -> admin display metadata.
 *
 * Standalone service in three parts:
 *  - nm_resolve_post() resolves one post ID to {id, found, title, status,
 *    status_label, date, thumbnail} for PHP callers
 *  - `GET nm/v1/resolve-posts` serves the same data over REST
 *  - the `nm-post-resolve` script handle (lib/admin/js/post-resolve.js) is
 *    the browser client for that endpoint, registered here with its
 *    endpoint+nonce global; consumers enqueue the handle or list it as a
 *    script dependency and render the results themselves
 *
 * Read-only, and gated so it never returns more than the current user can
 * already see in wp-admin: published posts resolve for anyone who can hit
 * the endpoint, non-published posts (draft/private/trash/etc) only resolve
 * for users who can edit that specific post.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Resolve one post ID to hint/preview metadata.
 *
 * 'found' is false when no post object exists for the ID, or when the post
 * exists but isn't published and the current user can't edit it — in either
 * case there is nothing here the caller couldn't already see in wp-admin.
 * A trashed or draft post the user CAN edit is still found — its status
 * tells the caller it won't display publicly (spec: red = unresolvable OR
 * status !== publish).
 *
 * @param int $post_id Post ID.
 * @return array{id:int, found:bool, title:?string, status:?string, status_label:?string, date:?string, thumbnail:?string}
 */
function nm_resolve_post( $post_id ) {
  $post_id = absint( $post_id );
  $post    = get_post( $post_id );

  $not_found = array(
    'id'           => $post_id,
    'found'        => false,
    'title'        => null,
    'status'       => null,
    'status_label' => null,
    'date'         => null,
    'thumbnail'    => null,
  );

  if ( ! $post ) {
    return $not_found;
  }

  // Mirror what this user can already see in wp-admin: published posts are
  // visible to everyone there; anything else only if they can edit that post.
  if ( 'publish' !== get_post_status( $post ) && ! current_user_can( 'edit_post', $post_id ) ) {
    return $not_found;
  }

  $status        = get_post_status( $post );
  $status_object = get_post_status_object( $status );
  $status_label  = ( $status_object && ! empty( $status_object->label ) ) ? $status_object->label : $status;

  return array(
    'id'           => $post_id,
    'found'        => true,
    'title'        => get_the_title( $post ),
    'status'       => $status,
    'status_label' => $status_label,
    'date'         => get_the_date( 'j M Y', $post ),
    'thumbnail'    => get_the_post_thumbnail_url( $post, 'thumbnail' ) ?: null,
  );
}

/**
 * REST callback: resolve a comma list of post IDs (capped at 20).
 *
 * @param WP_REST_Request $request Request with an 'ids' param.
 * @return WP_REST_Response|WP_Error
 */
function nm_resolve_posts_rest( $request ) {
  $raw = $request->get_param( 'ids' );
  $raw = is_string( $raw ) ? $raw : '';

  $ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
  $ids = array_slice( array_values( array_unique( $ids ) ), 0, 20 );

  if ( ! $ids ) {
    return new WP_Error( 'nm_resolve_no_ids', 'No valid post IDs supplied.', array( 'status' => 400 ) );
  }

  return rest_ensure_response( array_map( 'nm_resolve_post', $ids ) );
}

/**
 * Register the `GET nm/v1/resolve-posts` REST route.
 */
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

/**
 * Register (not enqueue) the browser client for the resolve endpoint.
 *
 * Registered early on both enqueue hooks so any consumer can enqueue the
 * `nm-post-resolve` handle or depend on it. The endpoint URL and nonce ride
 * along on the nmPostResolve global.
 */
function nm_register_post_resolve_client() {
  wp_register_script(
    'nm-post-resolve',
    get_template_directory_uri() . '/lib/admin/js/post-resolve.js',
    array(),
    wp_get_theme()->get( 'Version' ),
    true
  );
  wp_localize_script(
    'nm-post-resolve',
    'nmPostResolve',
    array(
      'endpoint' => esc_url_raw( rest_url( 'nm/v1/resolve-posts' ) ),
      'nonce'    => wp_create_nonce( 'wp_rest' ),
    )
  );
}
add_action( 'admin_enqueue_scripts', 'nm_register_post_resolve_client', 1 );
add_action( 'wp_enqueue_scripts', 'nm_register_post_resolve_client', 1 );
