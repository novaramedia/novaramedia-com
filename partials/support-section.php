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

// Optional context copy per donation mode; see render_support_form().
$copy = isset( $args['copy'] ) ? $args['copy'] : array();
?>
<div class="container <?php echo esc_attr( $container_classes ); ?>">
  <div class="grid-row">
      <?php render_support_form( 'banner', $on_colored_background, 'grid-item is-xxl-24', $copy ); ?>
  </div>
</div>
