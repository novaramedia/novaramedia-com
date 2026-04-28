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

if ( ! $related_post || 'publish' !== $related_post->post_status ) {
  return;
}

$post_type = $related_post->post_type;
$permalink = get_permalink( $related_id );
$title     = get_the_title( $related_id );

$layout = 'unknown';
if ( 'event' === $post_type ) {
  $layout = 'event';
} elseif ( 'post' === $post_type ) {
  $top_level = get_the_top_level_category( $related_id );
  if ( $top_level && in_array( $top_level->slug, array( 'articles', 'audio', 'video' ), true ) ) {
    $layout = $top_level->slug;
  }
}

if ( 'unknown' === $layout ) {
  return;
}

$wrapper_attributes = get_block_wrapper_attributes(
  array(
    'class' => 'nm-block-related-post nm-block-related-post--' . esc_attr( $layout ) . ' background-black font-color-white p-4 mb-4',
  )
);
?>
<div <?php echo $wrapper_attributes; ?>>
  <?php
  if ( 'video' === $layout ) {
    $youtube_id = get_post_meta( $related_id, '_cmb_utube', true );
    if ( empty( $youtube_id ) ) {
      return;
    }
    ?>
    <div class="mb-2">
      <?php echo render_youtube_embed_iframe( $youtube_id, false, 'lazy', $title ); ?>
    </div>
    <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover">
      <h3 class="font-weight-bold text-wrap-balance"><?php echo esc_html( $title ); ?></h3>
    </a>
    <?php
  } elseif ( 'event' === $layout ) {
    $timestamp = get_post_meta( $related_id, '_cmb_time', true );
    ?>
    <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover">
      <?php if ( has_post_thumbnail( $related_id ) ) { ?>
        <div class="mb-2">
          <?php
          echo get_the_post_thumbnail(
            $related_id,
            array( 600, 400 ),
            array(
              'alt'   => esc_attr( $title ),
              'class' => 'ui-rounded-image',
            )
          );
          ?>
        </div>
      <?php } ?>
      <h3 class="font-weight-bold text-wrap-balance mb-1"><?php echo esc_html( $title ); ?></h3>
      <?php
      if ( $timestamp ) {
        $time = new \Moment\Moment( '@' . $timestamp );
        ?>
        <p class="font-size-10 mb-0"><?php echo esc_html( $time->format( NM_DATE_FORMAT_LONG ) ); ?></p>
        <?php
      }
      ?>
    </a>
    <?php
  } else {
    // articles + audio share the same shell; only bylines differ.
    ?>
    <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover">
      <?php if ( has_post_thumbnail( $related_id ) ) { ?>
        <div class="mb-2">
          <?php
          echo get_the_post_thumbnail(
            $related_id,
            array( 600, 400 ),
            array(
              'alt'   => esc_attr( $title ),
              'class' => 'ui-rounded-image',
            )
          );
          ?>
        </div>
      <?php } ?>
      <h3 class="font-weight-bold text-wrap-balance mb-1"><?php echo esc_html( $title ); ?></h3>
      <?php
      $standfirst = get_post_meta( $related_id, '_cmb_standfirst', true );
      if ( ! empty( $standfirst ) ) {
        ?>
        <p class="font-size-10 mb-1"><?php echo esc_html( $standfirst ); ?></p>
        <?php
      }
      ?>
    </a>
    <?php
    if ( 'articles' === $layout ) {
      ?>
      <p class="font-size-10 mb-0"><?php render_bylines( $related_id ); ?></p>
      <?php
    }
  }
  ?>
</div>
