<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

get_header();
?>
<main id="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();

    // Get all event meta in a single database call
    $event_meta = get_post_meta( $post->ID );

    // Extract individual meta values with defaults
    $timestamp = isset( $event_meta['_cmb_time'][0] ) ? $event_meta['_cmb_time'][0] : '';
    $venue_name = isset( $event_meta['_cmb_venue_name'][0] ) ? $event_meta['_cmb_venue_name'][0] : '';
    $venue_google_maps_link = isset( $event_meta['_cmb_venue_google_maps_link'][0] ) ? $event_meta['_cmb_venue_google_maps_link'][0] : '';
    $venue_postcode = isset( $event_meta['_cmb_venue_postcode'][0] ) ? $event_meta['_cmb_venue_postcode'][0] : '';
    $speakers = isset( $event_meta['_cmb_speakers'][0] ) ? maybe_unserialize( $event_meta['_cmb_speakers'][0] ) : array();
    $host = isset( $event_meta['_cmb_host'][0] ) ? $event_meta['_cmb_host'][0] : '';
    $is_sold_out = isset( $event_meta['_cmb_tickets_sold_out'][0] ) ? $event_meta['_cmb_tickets_sold_out'][0] : '';
    $tickets_url = isset( $event_meta['_cmb_tickets'][0] ) ? $event_meta['_cmb_tickets'][0] : '';
    $youtube_id = isset( $event_meta['_cmb_youtube'][0] ) ? $event_meta['_cmb_youtube'][0] : '';
    $gallery = isset( $event_meta['_cmb_gallery'][0] ) ? $event_meta['_cmb_gallery'][0] : '';

    if ( $timestamp ) {
      $time = new \Moment\Moment( '@' . $timestamp );
    } else {
      $time = new \Moment\Moment( '@' . get_the_time( 'U' ) );
    }

    $time_from_event = $time->fromNow();
    ?>
  <article id="event">
    <div class="container">
      <div class="grid-row mb-4">
        <div class="grid-item is-xxl-24">
          <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>" class="ui-hover">
              Events
            </a>
          </h4>
        </div>
      </div>
      <div class="grid-row mb-4">
        <div class="grid-item is-xxl-24 text-align-center">
          <h3 class="font-size-10 font-weight-bold mb-2"><?php echo $time->format( 'j F Y' ); ?></h3>
          <h1 class="font-size-15 font-weight-bold text-wrap-balance"><?php the_title(); ?></h1>
        </div>
      </div>
      <div class="grid-row mb-5">
        <?php
        if ( $youtube_id ) {
          ?>
        <div class="grid-item offset-s-0 is-s-24 offset-xxl-4 is-xxl-16">
          <div class="u-video-embed-container">
            <?php echo render_youtube_embed_iframe( $youtube_id, false, true ); ?>
          </div>
        </div>
          <?php
        } else {
          ?>
        <div class="grid-item is-xxl-24 text-align-center">
          <?php the_post_thumbnail( array( 600, 500 ) ); ?>
        </div>
          <?php
        }
        ?>
      </div>
      <div class="grid-row mb-4">
        <div class="grid-item offset-s-0 is-s-24 offset-xxl-2 is-xxl-8">
          <div class="mb-4">
            <h5 class="font-size-10 font-weight-bold mb-2">Time:</h5>
            <h3 class="font-size-12 font-weight-bold"><?php echo $time->format( 'j F Y' ); ?></h3>
            <h3 class="font-size-12 font-weight-bold"><?php echo $time->format( 'H:i' ); ?></h3>
          </div>
        <?php
        if ( $venue_name ) {
          ?>
        <div class="mb-4">
          <h5 class="font-size-10 font-weight-bold mb-2">Venue:</h5>
          <?php
          if ( $venue_google_maps_link ) {
            $venue_link = $venue_google_maps_link;
            ?>
          <a href="<?php echo esc_url( $venue_link ); ?>" target="_blank" rel="nofollow">
            <h3 class="font-size-12 font-weight-bold"><?php echo $venue_name; ?></h3>
            <h3 class="font-size-11 font-weight-bold mt-1"><?php echo $venue_postcode; ?></h3>
          </a>
            <?php
          } elseif ( $venue_postcode ) {
            $venue_link = 'https://www.google.com/maps/search/' . rawurlencode( $venue_postcode );
            ?>
          <a href="<?php echo esc_url( $venue_link ); ?>" target="_blank" rel="nofollow">
            <h3 class="font-size-12 font-weight-bold"><?php echo $venue_name; ?></h3>
            <h3 class="font-size-11 font-weight-bold mt-1"><?php echo $venue_postcode; ?></h3>
          </a>
            <?php
          } else {
            ?>
          <h3 class="font-size-12 font-weight-bold"><?php echo $venue_name; ?></h3>
            <?php
          }
          ?>
        </div>
          <?php
        }

        if ( $speakers && is_array( $speakers ) && count( $speakers ) > 0 ) {
          ?>
        <div class="mb-4">
          <h5 class="font-size-10 font-weight-bold mb-2">Speakers:</h5>
          <?php
          foreach ( $speakers as $speaker ) {
            ?>
          <h3 class="font-size-12 font-weight-bold"><?php echo $speaker; ?></h3>
            <?php
          }
          ?>
        </div>
          <?php
        }

        if ( $host ) {
          ?>
        <div class="mb-4">
          <h5 class="font-size-10 font-weight-bold mb-2">Host:</h5>
          <h3 class="font-size-12 font-weight-bold"><?php echo $host; ?></h3>
        </div>
          <?php
        }

        if ( $is_sold_out ) {
          ?>
          <h4 class="font-size-12 font-weight-bold">Sold Out!</h4>
          <?php
        }

        if ( ! $is_sold_out && $tickets_url && $time_from_event->getDirection() !== 'past' ) {
          ?>
          <a href="<?php echo $tickets_url; ?>" target="_blank" rel="nofollow" class="ui-button ui-button--red"><h4>Buy Tickets</h4></a>
          <?php
        }
        ?>
        </div>
        <div class="grid-item is-s-24 is-xxl-12 text-copy font-size-10 font-serif">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <?php
    if ( $gallery ) {
      ?>
    <div class="container">
      <div class="grid-row mt-4 mb-4">
        <div class="grid-item is-xxl-24">
          <div class="grid-row background-black font-color-white ui-rounded-box ui-backgrounded-box-padding">
            <div class="grid-item is-xxl-24">
              <h4 class="font-weight-bold">Gallery</h4>
              <div class="grid-item is-xxl-24">
                <?php echo do_shortcode( $gallery ); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <?php
    }
    ?>
  </article>
    <?php
  }
} else {
  ?>
  <article id="event" class="container mb-4">
    <div class="grid-row">
      <article class="grid-item is-xxl-24">Sorry, no posts matched your criteria :{</article>
    </div>
  </article>
    <?php
}
?>
  <?php
    get_template_part( 'partials/support-section', null, array( 'container_classes' => 'mb-4' ) );
  ?>
</main>
<?php
get_footer();
?>
