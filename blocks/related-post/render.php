<?php
/**
 * Related Post Block - Server-side render
 *
 * Renders a linked preview of the selected post or event using a layout
 * matching the linked content's type:
 *
 * - post (articles): thumb + title + standfirst + bylines (whole tile linked)
 * - post (audio):    thumb + title + standfirst (whole tile linked)
 * - post (video):    YouTube embed + linked title below (only title linked)
 * - event:           thumb + title + datestamp (whole tile linked)
 *
 * Silently renders nothing if the selected post is missing, unpublished, or
 * lacks the data required for its layout.
 *
 * @param array    $attributes Block attributes from the editor.
 * @param string   $content    Block content (empty for dynamic blocks).
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$related    = $attributes['relatedPost'] ?? array();
$related_id = absint( $related['id'] ?? 0 );

if ( ! $related_id ) {
  return;
}

$related_post = get_post( $related_id );

if ( ! $related_post || $related_post->post_status !== 'publish' ) {
  return;
}

$related_post_type = $related_post->post_type;
$permalink = get_permalink( $related_id );
$related_title     = get_the_title( $related_id );

$layout = 'unknown';
if ( $related_post_type === 'event' ) {
  $layout = 'event';
} elseif ( $related_post_type === 'post' ) {
  $top_level = get_the_top_level_category( $related_id );
  if ( $top_level && in_array( $top_level->slug, array( 'articles', 'audio', 'video' ), true ) ) {
    $layout = $top_level->slug;
  }
}

if ( $layout === 'unknown' ) {
  return;
}

// Validate video-specific data before opening any HTML.
if ( $layout === 'video' ) {
  $youtube_id = get_post_meta( $related_id, '_cmb_utube', true );
  if ( empty( $youtube_id ) ) {
    return;
  }
}

$wrapper_attributes = get_block_wrapper_attributes(
  array(
    'class' => 'nm-block-related-post nm-block-related-post--' . esc_attr( $layout ) . ' background-black font-color-white ui-rounded-box p-4 mb-4',
  )
);
?>
<div <?php echo $wrapper_attributes; ?>>
  <?php
  if ( $layout === 'video' ) {
    // $youtube_id validated and set before wrapper div.
    ?>
    <div class="u-video-embed-container">
      <?php echo render_youtube_embed_iframe( $youtube_id, false, 'lazy', $related_title ); ?>
    </div>
    <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover">
      <h3 class="font-size-9 font-weight-bold text-wrap-pretty mt-2"><?php echo esc_html( $related_title ); ?></h3>
    </a>
    <?php
  } elseif ( $layout === 'event' ) {
    $timestamp = get_post_meta( $related_id, '_cmb_time', true );
    $tickets_url = get_post_meta( $related_id, '_cmb_tickets', true );
    $is_future_event = $timestamp && $timestamp > time();
    ?>
    <div class="grid-row grid--nested">
      <div class="grid-item is-xxl-10 is-s-10">
        <div class="layout-thumbnail-frame">
          <div class="layout-thumbnail-frame__inner mt-1 ml-1">
            <?php render_post_ui_tags( $related_id, true, true, 'no-border' ); ?>
          </div>
          <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover u-display-block">
            <?php
              render_thumbnail(
                $related_id,
                array(
                  'width'  => 600,
                  'height' => 600,
                ),
                array(
                  'class' => 'ui-rounded-image u-display-block',
                )
              );
            ?>
          </a>
        </div>
      </div>
      <div class="grid-item is-xxl-14 is-s-14">
        <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover">
          <h3 class="font-size-11 font-weight-bold text-wrap-balance"><?php echo esc_html( $related_title ); ?></h3>
          <?php
          if ( $timestamp ) {
            $time = new \Moment\Moment( '@' . $timestamp );
            ?>
            <p class="font-size-9 mt-1"><?php echo esc_html( $time->format( NM_DATE_FORMAT_LONG ) ); ?></p>
            <?php
          }
          ?>
        </a>
        <?php
        if ( $is_future_event && ! empty( $tickets_url ) ) {
          ?>
          <a href="<?php echo esc_url( $tickets_url ); ?>" target="_blank" rel="nofollow noopener noreferrer" class="ui-button ui-button--red ui-button--small mt-2">Buy Tickets</a>
          <?php
        }
        ?>
      </div>
    </div>
    <?php
  } else {
    // articles + audio share the same shell; only bylines differ.
  ?>
    <div class="grid-row grid--nested">
      <div class="grid-item is-xxl-10 is-s-10">
        <div class="layout-thumbnail-frame">
          <div class="layout-thumbnail-frame__inner mt-1 ml-1">
            <?php render_post_ui_tags( $related_id, true, true, 'no-border' ); ?>
          </div>
          <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover u-display-block">
            <?php
              render_thumbnail(
                $related_id,
                'col24-16to9',
                array(
                  'class' => 'ui-rounded-image u-display-block',
                )
              );
              ?>
          </a>
        </div>
      </div>
      <div class="grid-item is-xxl-14 is-s-14">
        <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover">
          <h3 class="font-size-11 font-weight-bold text-wrap-pretty"><?php echo esc_html( $related_title ); ?></h3>
          <?php
          if ( $layout === 'articles' ) {
            ?>
            <p class="font-size-8 font-weight-bold text-uppercase mt-1"><?php render_bylines( $related_id ); ?></p>
            <?php
          }

          $standfirst = get_post_meta( $related_id, '_cmb_standfirst', true );
          if ( ! empty( $standfirst ) ) {
            ?>
            <p class="font-size-9 mt-1 text-wrap-balance"><?php echo esc_html( $standfirst ); ?></p>
            <?php
          }

          ?>
        </a>
        <?php
        if ( $layout === 'audio' ) {
          ?>
          <div class="font-size-9 mt-1 text-wrap-balance">
            <?php render_short_description( $related_id ); ?>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
            <?php
          }
          ?>
</div>
