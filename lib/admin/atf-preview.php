<?php
/**
 * ATF options preview module.
 *
 * Renders a clickable mock of the front page's above-the-fold section at the
 * top of Front Page > Above the Fold: Featured. It shows what the front page
 * will actually show for the current field values:
 *
 * - the real desktop arrangement (partials/front-page/above-the-fold.php and
 *   featured-posts-block.php): block 1 over block 2, each a main post beside
 *   a stack of 2nd/3rd/4th, block 2 mirrored, with the Latest column running
 *   down the right of both
 * - unset or unpublished slots filled the way the front page fills them
 *   (nm_fill_above_the_fold_post_ids()), marked as auto-filled
 * - the Latest column from get_latest_news_ids(), which excludes the featured
 *   posts, so it changes as slots change
 * - a "Video / Downstream" style category trail on every post
 *
 * One renderer, nm_atf_preview_render_grid(), builds the markup for both the
 * initial page load and live updates: lib/admin/js/atf-preview.js sends the
 * current field values to `GET nm/v1/atf-preview` and swaps in the returned
 * grid, then layers on the session-only states (changed, collision) and the
 * option badges. Display-only: it never writes anything.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Zone map: slot (0-7, the order of nm_fill_above_the_fold_post_ids()) =>
 * field id, visual style, thumbnail?, label.
 *
 * @return array<int, array{field:string, style:string, thumb:bool, label:string}>
 */
function nm_atf_preview_zones() {
  return array(
    0 => array( 'field' => 'nm_above_the_fold_featured_1', 'style' => 'primary', 'thumb' => true, 'label' => 'Block 1 — main' ),
    1 => array( 'field' => 'nm_above_the_fold_featured_2', 'style' => 'secondary', 'thumb' => true, 'label' => 'Block 1 — 2nd' ),
    2 => array( 'field' => 'nm_above_the_fold_featured_3', 'style' => 'line', 'thumb' => false, 'label' => 'Block 1 — 3rd' ),
    3 => array( 'field' => 'nm_above_the_fold_featured_4', 'style' => 'line', 'thumb' => false, 'label' => 'Block 1 — 4th' ),
    4 => array( 'field' => 'nm_above_the_fold_featured_5', 'style' => 'primary', 'thumb' => true, 'label' => 'Block 2 — main' ),
    5 => array( 'field' => 'nm_above_the_fold_featured_6', 'style' => 'secondary', 'thumb' => true, 'label' => 'Block 2 — 2nd' ),
    6 => array( 'field' => 'nm_above_the_fold_featured_7', 'style' => 'line', 'thumb' => false, 'label' => 'Block 2 — 3rd' ),
    7 => array( 'field' => 'nm_above_the_fold_featured_8', 'style' => 'line', 'thumb' => false, 'label' => 'Block 2 — 4th' ),
  );
}

/**
 * Category trail for a post: top-level category / first sub-category, e.g.
 * "Video / Downstream" or "Articles / Opinion". Same terms the front page
 * tags posts with (render_post_ui_tags()).
 *
 * @param int $post_id Post ID.
 * @return array{trail:string}
 */
function nm_atf_preview_category_trail( $post_id ) {
  $top = get_the_top_level_category( $post_id );
  $sub = get_the_sub_category( $post_id, true );

  $parts = array();
  if ( $top ) {
    $parts[] = $top->name;
  }
  if ( $sub ) {
    $parts[] = $sub->name;
  }

  return array( 'trail' => implode( ' / ', $parts ) );
}

/**
 * Thumbnail URL the front page would use: the alternative thumbnail when one
 * is set, as render_thumbnail() does, otherwise the featured image.
 *
 * @param int $post_id Post ID.
 * @return string URL or ''.
 */
function nm_atf_preview_thumbnail_url( $post_id ) {
  $alt_id = get_post_meta( $post_id, '_cmb_alt_thumb_id', true );

  if ( is_numeric( $alt_id ) ) {
    $alt_url = wp_get_attachment_image_url( (int) $alt_id, 'medium_large' );
    if ( $alt_url ) {
      return $alt_url;
    }
  }

  return get_the_post_thumbnail_url( $post_id, 'medium_large' ) ?: '';
}

/**
 * Slot ids with any the current user may not see replaced by 0 (unset).
 *
 * The preview route takes arbitrary ids, so they pass the same visibility
 * gate as the title lookup (nm_resolve_post()) before anything about them
 * is rendered. A hidden id is then auto-filled like an unset slot and shown
 * as "No post you can view with ID …".
 *
 * @param int[] $saved_ids Slot ids in slot order (0 = unset).
 * @return int[]
 */
function nm_atf_preview_visible_ids( $saved_ids ) {
  return array_map(
    function ( $id ) {
      return ( $id && nm_resolve_post( $id )['found'] ) ? $id : 0;
    },
    $saved_ids
  );
}

/**
 * Display state for every slot plus the Latest column, for a set of slot
 * values (saved or still being edited).
 *
 * @param array $saved_ids 8 slot values in slot order ('' or 0 = unset).
 * @return array{slots: array<int, array>, latest: array<int, array>}
 */
function nm_atf_preview_state( $saved_ids ) {
  $saved_ids = array_map( 'absint', array_pad( array_slice( array_values( $saved_ids ), 0, 8 ), 8, 0 ) );
  $effective = nm_fill_above_the_fold_post_ids( nm_atf_preview_visible_ids( $saved_ids ), nm_get_latest_featurable_post_ids() );
  $effective = is_array( $effective ) ? $effective : array_fill( 0, 8, 0 );

  $slots = array();

  foreach ( $saved_ids as $i => $saved_id ) {
    $shown_id = ! empty( $effective[ $i ] ) ? (int) $effective[ $i ] : 0;
    $slot     = array(
      'saved_id' => $saved_id,
      'shown_id' => $shown_id,
      'auto'     => $shown_id && $shown_id !== $saved_id,
      'problem'  => '',
    );

    // A saved id the front page can't show: say why, then show what fills in.
    if ( $saved_id && $shown_id !== $saved_id ) {
      $info = nm_resolve_post( $saved_id );

      if ( ! $info['found'] ) {
        $slot['problem'] = 'No post you can view with ID ' . $saved_id;
      } else {
        $slot['problem'] = ( '' !== $info['title'] ? $info['title'] : '(no title)' ) . ' is ' . strtolower( $info['status_label'] ) . ', so it won’t show';
      }
    }

    $slot['title']     = $shown_id ? wp_strip_all_tags( get_the_title( $shown_id ) ) : '';
    $slot['thumbnail'] = $shown_id ? nm_atf_preview_thumbnail_url( $shown_id ) : '';
    $slot              = array_merge( $slot, $shown_id ? nm_atf_preview_category_trail( $shown_id ) : array( 'trail' => '' ) );

    $slots[ $i ] = $slot;
  }

  $latest = array();

  foreach ( (array) get_latest_news_ids( $effective ) as $latest_id ) {
    $latest[] = array_merge(
      array( 'title' => wp_strip_all_tags( get_the_title( $latest_id ) ) ),
      nm_atf_preview_category_trail( $latest_id )
    );
  }

  return array(
    'slots'  => $slots,
    'latest' => $latest,
  );
}

/**
 * Markup for one slot.
 *
 * @param array $zone Zone map entry.
 * @param array $slot Slot state from nm_atf_preview_state().
 * @return string
 */
function nm_atf_preview_zone_html( $zone, $slot ) {
  $classes = array( 'nm-atf-preview__zone', 'nm-atf-preview__zone--' . $zone['style'] );

  if ( $slot['problem'] ) {
    $classes[] = 'is-broken';
  } elseif ( $slot['auto'] ) {
    $classes[] = 'is-auto';
  } elseif ( ! $slot['shown_id'] ) {
    $classes[] = 'is-empty';
  }

  $html  = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-nm-zone data-field="' . esc_attr( $zone['field'] ) . '" data-saved-id="' . esc_attr( $slot['saved_id'] ) . '" data-label="' . esc_attr( $zone['label'] ) . '" role="button" tabindex="0" title="Click to edit ' . esc_attr( $zone['label'] ) . '">';

  if ( $zone['thumb'] ) {
    $style = $slot['thumbnail'] ? ' style="background-image:url(&quot;' . esc_url( $slot['thumbnail'] ) . '&quot;)"' : '';
    $html .= '<span class="nm-atf-preview__thumb"' . $style . '></span>';
  }

  $html .= '<span class="nm-atf-preview__zone-body">';

  if ( $slot['problem'] ) {
    $html .= '<span class="nm-atf-preview__problem">' . esc_html( $slot['problem'] ) . '</span>';
  }

  if ( $slot['shown_id'] ) {
    $meta = $slot['auto'] ? 'Auto-filled' . ( $slot['trail'] ? ' · ' . $slot['trail'] : '' ) : $slot['trail'];
    if ( $meta ) {
      $html .= '<span class="nm-atf-preview__trail">' . esc_html( $meta ) . '</span>';
    }
    $html .= '<span class="nm-atf-preview__zone-title">' . esc_html( '' !== $slot['title'] ? $slot['title'] : '(no title)' ) . '</span>';
  } else {
    $html .= '<span class="nm-atf-preview__zone-title">Empty — click to set</span>';
  }

  $html .= '</span></div>';

  return $html;
}

/**
 * The preview grid: both featured blocks and the Latest column.
 *
 * @param array $saved_ids 8 slot values in slot order.
 * @return string
 */
function nm_atf_preview_render_grid( $saved_ids ) {
  $state = nm_atf_preview_state( $saved_ids );
  $zones = nm_atf_preview_zones();

  $zone = function ( $i ) use ( $zones, $state ) {
    return nm_atf_preview_zone_html( $zones[ $i ], $state['slots'][ $i ] );
  };

  $block = function ( $number ) use ( $zone ) {
    $first = 4 * ( $number - 1 );
    $html  = '<div class="nm-atf-preview__block' . ( 2 === $number ? ' nm-atf-preview__block--reverse' : '' ) . '" data-block="' . $number . '">';
    $html .= '<div class="nm-atf-preview__main">' . $zone( $first ) . '<div class="nm-atf-preview__badges" data-nm-badges data-block="' . $number . '"></div></div>';
    $html .= '<div class="nm-atf-preview__stack">' . $zone( $first + 1 ) . $zone( $first + 2 ) . $zone( $first + 3 ) . '</div>';
    $html .= '</div>';
    return $html;
  };

  $latest = '<div class="nm-atf-preview__latest"><span class="nm-atf-preview__latest-heading">Latest</span>';
  if ( $state['latest'] ) {
    $latest .= '<ol class="nm-atf-preview__latest-list">';
    foreach ( $state['latest'] as $item ) {
      $latest .= '<li class="nm-atf-preview__latest-item">';
      if ( $item['trail'] ) {
        $latest .= '<span class="nm-atf-preview__trail">' . esc_html( $item['trail'] ) . '</span>';
      }
      $latest .= '<span class="nm-atf-preview__latest-title">' . esc_html( $item['title'] ) . '</span></li>';
    }
    $latest .= '</ol>';
  } else {
    $latest .= '<span class="nm-atf-preview__latest-note">No recent News posts</span>';
  }
  $latest .= '</div>';

  return '<div class="nm-atf-preview__featured">' . $block( 1 ) . $block( 2 ) . '</div>' . $latest;
}

/**
 * Saved slot values from the option, in slot order.
 *
 * @return array
 */
function nm_atf_preview_saved_ids() {
  $options = get_option( 'nm_front_page_above_the_fold_featured_options', array() );

  return array_map(
    function ( $zone ) use ( $options ) {
      return isset( $options[ $zone['field'] ] ) ? $options[ $zone['field'] ] : '';
    },
    nm_atf_preview_zones()
  );
}

/**
 * REST callback: render the grid for a comma list of 8 slot values.
 *
 * @param WP_REST_Request $request Request with a 'slots' param.
 * @return WP_REST_Response
 */
function nm_atf_preview_rest( $request ) {
  $raw = $request->get_param( 'slots' );
  $raw = is_string( $raw ) ? $raw : '';

  return rest_ensure_response( array( 'html' => nm_atf_preview_render_grid( explode( ',', $raw ) ) ) );
}

/**
 * Register `GET nm/v1/atf-preview`. Same capability as the options page.
 * Output is limited to what the page already shows: published posts, plus
 * saved IDs passed through nm_resolve_post()'s visibility gate.
 */
function nm_atf_preview_register_route() {
  register_rest_route(
    'nm/v1',
    '/atf-preview',
    array(
      'methods'             => WP_REST_Server::READABLE,
      'callback'            => 'nm_atf_preview_rest',
      'permission_callback' => function () {
        return current_user_can( 'edit_posts' );
      },
      'args'                => array(
        'slots' => array(
          'required'          => true,
          'type'              => 'string',
          'sanitize_callback' => 'sanitize_text_field',
        ),
      ),
    )
  );
}
add_action( 'rest_api_init', 'nm_atf_preview_register_route' );

/**
 * Render the preview above the ATF options form and enqueue its assets.
 *
 * Hooked to cmb2_before_form and filtered to the ATF box, so it renders
 * exactly once, only on that options page.
 */
function nm_atf_preview_render( $cmb_id, $object_id, $object_type, $cmb ) {
  if ( 'nm_above_the_fold_featured_options_page' !== $cmb_id ) {
    return;
  }

  $theme_version = wp_get_theme()->get( 'Version' );

  wp_enqueue_style(
    'nm-atf-preview',
    get_template_directory_uri() . '/lib/admin/css/atf-preview.css',
    array(),
    $theme_version
  );

  wp_enqueue_script(
    'nm-atf-preview',
    get_template_directory_uri() . '/lib/admin/js/atf-preview.js',
    array( 'jquery' ),
    $theme_version,
    true
  );

  wp_localize_script(
    'nm-atf-preview',
    'nmAtfPreview',
    array(
      'endpoint' => esc_url_raw( rest_url( 'nm/v1/atf-preview' ) ),
      'nonce'    => wp_create_nonce( 'wp_rest' ),
    )
  );
  ?>
  <div class="nm-atf-preview" data-nm-atf-preview>
    <p class="nm-atf-preview__heading">Above the fold — preview of the front page on desktop. Click a post to edit its slot.</p>
    <p class="nm-atf-preview__banner" data-nm-banner hidden>Couldn&#8217;t update the preview — it may be out of date</p>
    <p class="nm-atf-preview__collisions" data-nm-collisions hidden></p>
    <div class="nm-atf-preview__grid" data-nm-grid>
      <?php echo nm_atf_preview_render_grid( nm_atf_preview_saved_ids() ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped as built. ?>
    </div>
  </div>
  <?php
}
add_action( 'cmb2_before_form', 'nm_atf_preview_render', 10, 4 );
