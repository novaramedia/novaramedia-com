<?php
/**
 * ATF options preview module.
 *
 * Renders a clickable mock layout of the front page's above-the-fold
 * section at the top of the "Above the Fold: Featured" options page
 * (Front Page > Above the Fold: Featured), server-filled from the saved
 * option so there is no flash of empty zones. lib/admin/js/atf-preview.js
 * keeps it live as fields change. Display-only: it never writes anything.
 *
 * Zone structure mirrors partials/front-page/above-the-fold.php: two
 * featured blocks (primary + 2nd with thumbnails, 3rd/4th title-only)
 * around the automatic latest-news column.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Zone map: preview zone => field id, block, visual style, thumbnail?
 * The single source for both the skeleton and the JS (via data attributes).
 *
 * @return array<string, array{field:string, block:int, style:string, thumb:bool, label:string}>
 */
function nm_atf_preview_zones() {
  return array(
    'b1-primary' => array( 'field' => 'nm_above_the_fold_featured_1', 'block' => 1, 'style' => 'primary', 'thumb' => true, 'label' => 'Featured 1 — main' ),
    'b1-second'  => array( 'field' => 'nm_above_the_fold_featured_2', 'block' => 1, 'style' => 'secondary', 'thumb' => true, 'label' => 'Featured 1 — 2nd' ),
    'b1-third'   => array( 'field' => 'nm_above_the_fold_featured_3', 'block' => 1, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 1 — 3rd' ),
    'b1-fourth'  => array( 'field' => 'nm_above_the_fold_featured_4', 'block' => 1, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 1 — 4th' ),
    'b2-primary' => array( 'field' => 'nm_above_the_fold_featured_5', 'block' => 2, 'style' => 'primary', 'thumb' => true, 'label' => 'Featured 2 — main' ),
    'b2-second'  => array( 'field' => 'nm_above_the_fold_featured_6', 'block' => 2, 'style' => 'secondary', 'thumb' => true, 'label' => 'Featured 2 — 2nd' ),
    'b2-third'   => array( 'field' => 'nm_above_the_fold_featured_7', 'block' => 2, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 2 — 3rd' ),
    'b2-fourth'  => array( 'field' => 'nm_above_the_fold_featured_8', 'block' => 2, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 2 — 4th' ),
  );
}

/**
 * Display state for one zone value.
 *
 * String contract shared with title_hint_html() (PHP hints), renderHint()
 * (lib/meta/js/cmb2-post-search-field-hints.js) and zoneText()
 * (lib/admin/js/atf-preview.js) — keep them in sync.
 *
 * @param string $value Raw saved field value (single post ID for ATF zones).
 * @return array{text:string, broken:bool, empty:bool, thumbnail:?string}
 */
function nm_atf_preview_zone_display( $value ) {
  $id = absint( is_scalar( $value ) ? $value : 0 );

  if ( ! $id ) {
    return array( 'text' => 'Empty — click to set', 'broken' => false, 'empty' => true, 'thumbnail' => null );
  }

  if ( ! function_exists( 'nm_resolve_post' ) ) {
    return array( 'text' => '', 'broken' => false, 'empty' => false, 'thumbnail' => null );
  }

  $info = nm_resolve_post( $id );

  if ( ! $info['found'] ) {
    return array( 'text' => 'No post with ID ' . $id, 'broken' => true, 'empty' => false, 'thumbnail' => null );
  }

  if ( 'publish' !== $info['status'] ) {
    return array(
      'text'      => ( '' !== $info['title'] ? $info['title'] : '(no title)' ) . ' — ' . $info['status_label'] . ', won’t display publicly',
      'broken'    => true,
      'empty'     => false,
      'thumbnail' => $info['thumbnail'],
    );
  }

  return array( 'text' => ( '' !== $info['title'] ? $info['title'] : '(no title)' ), 'broken' => false, 'empty' => false, 'thumbnail' => $info['thumbnail'] );
}

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

  // nm-post-resolve is the shared endpoint client registered by
  // lib/admin/post-resolve.php (early on admin_enqueue_scripts); it carries
  // the nmPostResolveClient API the preview JS resolves post data through.
  wp_enqueue_script(
    'nm-atf-preview',
    get_template_directory_uri() . '/lib/admin/js/atf-preview.js',
    array( 'jquery', 'nm-post-resolve' ),
    $theme_version,
    true
  );

  $options = get_option( 'nm_front_page_above_the_fold_featured_options', array() );
  $zones   = nm_atf_preview_zones();

  $render_zone = function ( $key ) use ( $zones, $options ) {
    $zone    = $zones[ $key ];
    $value   = isset( $options[ $zone['field'] ] ) ? $options[ $zone['field'] ] : '';
    $display = nm_atf_preview_zone_display( $value );

    $classes = array( 'nm-atf-preview__zone', 'nm-atf-preview__zone--' . $zone['style'] );
    if ( $display['empty'] ) {
      $classes[] = 'is-empty';
    }
    if ( $display['broken'] ) {
      $classes[] = 'is-broken';
    }

    $thumb_style = ( $zone['thumb'] && $display['thumbnail'] )
      ? ' style="background-image:url(&quot;' . esc_url( $display['thumbnail'] ) . '&quot;)"'
      : '';

    echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-nm-zone data-field="' . esc_attr( $zone['field'] ) . '" data-thumb="' . ( $zone['thumb'] ? '1' : '0' ) . '" data-label="' . esc_attr( $zone['label'] ) . '" role="button" tabindex="0" title="Click to edit ' . esc_attr( $zone['label'] ) . '">';
    if ( $zone['thumb'] ) {
      echo '<span class="nm-atf-preview__thumb"' . $thumb_style . '></span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_url above.
    }
    echo '<span class="nm-atf-preview__zone-title">' . esc_html( $display['text'] ) . '</span>';
    echo '</div>';
  };
  ?>
  <div class="nm-atf-preview" data-nm-atf-preview>
    <p class="nm-atf-preview__heading">Above the fold — preview. Click a zone to edit it.</p>
    <p class="nm-atf-preview__banner" data-nm-banner hidden>Couldn&#8217;t load post data — preview may be stale</p>
    <p class="nm-atf-preview__collisions" data-nm-collisions hidden></p>
    <div class="nm-atf-preview__grid">
      <div class="nm-atf-preview__block" data-block="1">
        <?php $render_zone( 'b1-primary' ); ?>
        <div class="nm-atf-preview__badges" data-nm-badges data-block="1"></div>
        <?php $render_zone( 'b1-second' ); ?>
        <?php $render_zone( 'b1-third' ); ?>
        <?php $render_zone( 'b1-fourth' ); ?>
      </div>
      <div class="nm-atf-preview__latest">
        <span class="nm-atf-preview__latest-title">Latest</span>
        <span class="nm-atf-preview__latest-note">automatic — latest News posts</span>
      </div>
      <div class="nm-atf-preview__block" data-block="2">
        <?php $render_zone( 'b2-primary' ); ?>
        <div class="nm-atf-preview__badges" data-nm-badges data-block="2"></div>
        <?php $render_zone( 'b2-second' ); ?>
        <?php $render_zone( 'b2-third' ); ?>
        <?php $render_zone( 'b2-fourth' ); ?>
      </div>
    </div>
  </div>
  <?php
}
add_action( 'cmb2_before_form', 'nm_atf_preview_render', 10, 4 );
