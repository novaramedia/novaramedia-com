<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$timestamp = get_post_meta( $post->ID, '_cmb_time', true );

if ( $timestamp ) {
  $time = new \Moment\Moment( '@' . $timestamp );
} else {
  $time = new \Moment\Moment( '@' . get_the_time( 'U' ) );
}

$venue_name = get_post_meta( $post->ID, '_cmb_venue_name', true );
$venue_postcode = get_post_meta( $post->ID, '_cmb_venue_postcode', true );
$speakers = get_post_meta( $post->ID, '_cmb_speakers', true );
$host = get_post_meta( $post->ID, '_cmb_host', true );
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
      ?>
      <a href="https://www.google.com/maps/search/<?php echo $venue_postcode; ?>" target="_blank" rel="nofollow" class="ui-hover">
        <h3 class="font-size-11 font-weight-bold mb-3">At <?php echo $venue_name; ?></h3>
      </a>
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
