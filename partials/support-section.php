<?php
if ( ! isset( $args['container_classes'] ) || ! is_string( $args['container_classes'] ) ) {
  $container_classes = '';
} else {
  $container_classes = $args['container_classes'];
}

if ( ! isset( $args['on_colored_background'] ) || ! is_bool( $args['on_colored_background'] ) ) {
  $on_colored_background = false;
} else {
  $on_colored_background = $args['on_colored_background'];
}

// Which form a donation came from, passed to the donation app as `nm_form`. Worked out per page (so safe
// with the page cache); the page path is sent separately, so other templates share one slug.
if ( ! empty( $args['placement'] ) && is_string( $args['placement'] ) ) {
  $placement = $args['placement'];
} elseif ( is_front_page() ) {
  $placement = 'front-page-support-section';
} elseif ( is_singular( 'post' ) ) {
  $placement = 'article-support-section';
} else {
  $placement = 'support-section';
}
?>
<div class="container <?php echo esc_attr( $container_classes ); ?>">
  <div class="grid-row">
      <?php render_support_form( 'banner', $on_colored_background, 'grid-item is-xxl-24', $placement ); ?>
  </div>
</div>
