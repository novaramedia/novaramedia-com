<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  $meta = get_post_meta($post->ID);
?>
<div class="grid-row mb-4">
  <div class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-18 offset-l-2 is-xl-16 offset-xl-4 is-xxl-16 offset-xxl-4">
    <h1 id="single-articles-title" class="font-size-15 font-weight-bold mb-3"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?></h1>
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
      if (!empty($meta['_cmb_standfirst'])) {
    ?><h2 class="font-size-12 font-weight-bold mb-3 js-fix-widows"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $meta['_cmb_standfirst'][0]; ?></h2>
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
      }
    ?>
    <h3 class="font-size-11 font-weight-bold">by <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_bylines($post->ID, true); ?></h3>
    <h3 class="font-size-11 font-weight-bold"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_time('j F Y'); ?></h3>
  </div>
</div>
