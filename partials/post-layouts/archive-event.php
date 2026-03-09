<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// Get all event meta in a single database call
$event_meta = get_post_meta( $post->ID );

// Extract individual meta values with defaults
$timestamp = isset( $event_meta['_cmb_time'][0] ) ? $event_meta['_cmb_time'][0] : '';
$venue_name = isset( $event_meta['_cmb_venue_name'][0] ) ? $event_meta['_cmb_venue_name'][0] : '';
$venue_google_maps_link = isset( $event_meta['_cmb_venue_google_maps_link'][0] ) ? $event_meta['_cmb_venue_google_maps_link'][0] : '';
$venue_postcode = isset( $event_meta['_cmb_venue_postcode'][0] ) ? $event_meta['_cmb_venue_postcode'][0] : '';
$speakers = isset( $event_meta['_cmb_speakers'][0] ) ? maybe_unserialize( $event_meta['_cmb_speakers'][0] ) : array();
$host = isset( $event_meta['_cmb_host'][0] ) ? $event_meta['_cmb_host'][0] : '';

if ( $timestamp ) {
  $time = new \Moment\Moment( '@' . $timestamp );
} else {
  $time = new \Moment\Moment( '@' . get_the_time( 'U' ) );
}
?>
<div class="grid-row mb-5">
  <div class="grid-item is-xxl-10 is-s-24 mb-s-2 text-align-center">
    <a href="<?php the_permalink(); ?>" class="ui-hover">
      <?php
      the_post_thumbnail(
        array( 600, 400 ),
        array(
          'alt'   => get_the_title(),
          'class' => 'ui-rounded-image',
        )
      );
      ?>
    </a>
  </div>
  <div class="grid-item is-xxl-14 is-s-24">
    <a href="<?php the_permalink(); ?>" class="ui-hover">
      <h3 class="font-weight-bold mb-2"><?php echo $time->format( 'j F Y' ); ?>:</h3>
      <h2 class="font-size-13 font-weight-bold text-wrap-balance mb-3"><?php the_title(); ?></h2>
    </a>
    <?php
    if ( $venue_name ) {
      if ( ! empty( $venue_google_maps_link ) ) {
        $venue_link = $venue_google_maps_link;
        ?>
          <a href="<?php echo esc_url( $venue_link ); ?>" target="_blank" rel="nofollow">
            <h3 class="font-size-11 font-weight-bold mb-3">At <?php echo $venue_name; ?></h3>
          </a>
            <?php
      } elseif ( ! empty( $venue_postcode ) ) {
        $venue_link = 'https://www.google.com/maps/search/' . rawurlencode( $venue_postcode );
        ?>
          <a href="<?php echo esc_url( $venue_link ); ?>" target="_blank" rel="nofollow">
            <h3 class="font-size-11 font-weight-bold mb-3">At <?php echo $venue_name; ?></h3>
          </a>
            <?php
      } else {
        ?>
          <h3 class="font-size-11 font-weight-bold mb-3">At <?php echo $venue_name; ?></h3>
            <?php
      }
      ?>
      <?php
    }
    ?>
    <?php
    if ( $speakers ) {
      ?>
      <h3 class="font-size-12 font-weight-bold mb-1">With
      <?php
      $i = 0;
      $number_of_speakers = count( $speakers );
      foreach ( $speakers as $speaker ) {
        ++$i;
        if ( $i === $number_of_speakers && $number_of_speakers > 1 ) {
          echo ' & ';
        } elseif ( $i > 1 ) {
          echo ', ';
        }
        echo $speaker;
      }
      ?>
      </h3>
      <?php
    }
    ?>
    <?php
    if ( $host ) {
      ?>
      <h3 class="font-size-12 font-weight-bold">Hosted by <?php echo $host; ?></h3>
      <?php
    }
    ?>
  </div>
</div>
