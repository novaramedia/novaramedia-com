<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  $timestamp = get_post_meta( $post->ID, '_cmb_time', true );

  if ($timestamp) {
    $time = new \Moment\Moment('@' . $timestamp);
  } else {
    $time = new \Moment\Moment('@' . get_the_time('U'));
  }

  $venue_name = get_post_meta( $post->ID, '_cmb_venue_name', true );
  $speakers = get_post_meta( $post->ID, '_cmb_speakers', true );
  $host = get_post_meta( $post->ID, '_cmb_host', true );
?>

<a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_permalink(); ?>">
  <div class="row margin-bottom-mid">
    <div class="col col1"></div>
    <div class="col col9">
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_post_thumbnail('col12'); ?>
    </div>
    <div class="col col1"></div>
    <div class="col col13">
      <h3 class="margin-bottom-tiny"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $time->format('j F Y'); ?>:</h3>
      <h2 class="margin-bottom-tiny"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?></h2>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        if ($venue_name) {
      ?>
        <h3>At <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $venue_name; ?></h3>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        }
      ?>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        if ($speakers) {
      ?>
        <h3>With <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          $i = 0;
          $last = count($speakers) - 1;
          foreach ($speakers as $speaker) {
            if ($i === $last) {
              echo ' & ';
            } else if ($i > 0) {
              echo ', ';
            }
            echo $speaker;
            $i++;
          }
        ?></h3>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        }
      ?>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        if ($host) {
      ?>
        <h3>Hosted by <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $host; ?></h3>
      <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        }
      ?>
    </div>
  </div>
</a>