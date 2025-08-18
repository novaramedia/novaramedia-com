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
?>
<div class="container <?php echo esc_attr( $container_classes ); ?>">
  <div class="grid-row">
      <?php render_support_form_dispatcher( 'banner', $on_colored_background ); ?>
  </div>
</div>
