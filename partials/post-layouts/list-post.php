<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
  This post layout is for list views of posts when they need to be displayed for archival type reasons.
  It is used on the single contributor page when the full archive setting is true.
*/

  if (empty($args['grid-item-classes'])) { // if no classes set for grid item don't render
    return;
  }
?>
<div <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} post_class($args['grid-item-classes']); ?>>
  <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink() ?>">
    <span class="font-size-9"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_time('j F Y'); ?></span>
    <h3 class="font-size-9 font-weight-semibold"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?></h3>
  </a>
</div>
