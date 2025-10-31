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

    $timestamp = get_post_meta( $post->ID, '_cmb_time', true );

    if ( $timestamp ) {
      $time = new \Moment\Moment( '@' . $timestamp );
    } else {
      $time = new \Moment\Moment( '@' . get_the_time( 'U' ) );
    }

    $time_from_event = $time->fromNow();

    $venue_name = get_post_meta( $post->ID, '_cmb_venue_name', true );
    $venue_google_maps_link = get_post_meta( $post->ID, '_cmb_venue_google_maps_link', true );
    $venue_postcode = get_post_meta( $post->ID, '_cmb_venue_postcode', true );

    $speakers = get_post_meta( $post->ID, '_cmb_speakers', true );
    $host = get_post_meta( $post->ID, '_cmb_host', true );

    $is_sold_out = get_post_meta( $post->ID, '_cmb_tickets_sold_out', true );
    $tickets_url = get_post_meta( $post->ID, '_cmb_tickets', true );

    $youtube_id = get_post_meta( $post->ID, '_cmb_youtube', true );

    $gallery = get_post_meta( $post->ID, '_cmb_gallery', true );
    ?>
  <article id="event">
    <div class="container">
      <div class="grid-row mb-4">
        <div class="grid-item is-xxl-24">
          <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>" class="ui-hover">
              Event
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
            <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url( $youtube_id ); ?>" frameborder="0" allowfullscreen></iframe>
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
        <div class="grid-item offset-s-0 is-s-8 offset-xxl-2 is-xxl-8">
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
          if ( $venue_google_maps_link || $venue_postcode ) {
            $venue_link = $venue_google_maps_link ? $venue_google_maps_link : 'https://www.google.com/maps/search/' . $venue_postcode;
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
        <div class="grid-item is-s-16 is-xxl-12 text-copy font-size-10 font-serif">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <?php
    if ( $gallery ) {
      ?>
    <div class="background-black font-grid-itemor-white padding-top-mid padding-bottom-mid">
      <div class="container">
        <div class="grid-row mb-4">
          <div class="grid-item is-xxl-24">
            <h4>Gallery: <span id="gallery-pagination"></span></h4>
          </div>
        </div>
        <div class="grid-row mb-4">
          <div class="grid-item is-xxl-24">
            <?php echo do_shortcode( $gallery ); ?>
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
