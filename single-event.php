<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header();
?>
<main id="main-content">
<?php

if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    $timestamp = get_post_meta( $post->ID, '_cmb_time', true );

    if ($timestamp) {
      $time = new \Moment\Moment('@' . $timestamp);
    } else {
      $time = new \Moment\Moment('@' . get_the_time('U'));
    }

    $fromEvent = $time->fromNow();

    $venue_name = get_post_meta( $post->ID, '_cmb_venue_name', true );
    $venue_postcode = get_post_meta( $post->ID, '_cmb_venue_postcode', true );

    $speakers = get_post_meta( $post->ID, '_cmb_speakers', true );
    $host = get_post_meta( $post->ID, '_cmb_host', true );

    $is_sold_out = get_post_meta( $post->ID, '_cmb_tickets_sold_out', true );
    $tickets_url = get_post_meta( $post->ID, '_cmb_tickets', true );

    $youtube_id = get_post_meta( $post->ID, '_cmb_youtube', true );

    $gallery = get_post_meta( $post->ID, '_cmb_gallery', true );
?>
  <article id="event">
    <div class="container mt-4 mb-6">
      <div class="grid-row mb-4">
        <div class="grid-item is-xxl-24">
          <h4 class="font-size-9 text-uppercase font-weight-bold">Events</h4>
        </div>
      </div>
      <div class="grid-row mb-5">
        <div class="grid-item is-xxl-24 text-align-center">
          <h1 class="font-size-15 font-weight-bold"><?php
 echo $time->format('j F Y'); ?>: <?php
 the_title(); ?></h1>
        </div>
      </div>
      <div class="grid-row mb-4">
        <?php

          if ($youtube_id) {
        ?>
        <div class="grid-item offset-s-0 is-s-24 offset-xxl-4 is-xxl-16">
          <div class="u-video-embed-container">
            <iframe class="youtube-player lazyload" data-src="<?php
 echo generate_youtube_embed_url($youtube_id); ?>" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
        <?php

          } else {
        ?>
        <div class="grid-item offset-s-2 is-s-20 offset-l-3 is-l-18 offset-xxl-6 is-xxl-12">
          <?php
 the_post_thumbnail('is-xxl-12'); ?>
        </div>
        <?php

          }
        ?>
      </div>
      <div class="grid-row mb-4">
        <div class="grid-item offset-s-0 is-s-8 offset-xxl-2 is-xxl-8">
          <div class="mb-4">
            <h5 class="font-size-10 font-weight-bold mb-2">Time:</h5>
            <h3 class="font-size-11 font-weight-bold"><?php
 echo $time->format('j'); ?><sup><?php
 echo $time->format('S'); ?></sup><?php
 echo $time->format(' F Y'); ?></h3>
            <h3 class="font-size-11 font-weight-bold"><?php
 echo $time->format('H:i'); ?></h3>
          </div>
        <?php

          if ($venue_name) {
        ?>
          <div class="mb-4">
            <h5 class="font-size-10 font-weight-bold mb-2">Venue:</h5>
            <h3><?php
 echo $venue_name; ?></h3>
        <?php

            if ($venue_postcode) {
        ?>
            <h3 class="font-size-11 font-weight-bold"><a href="https://www.google.com/maps/search/<?php
 echo urlencode($venue_postcode); ?>" target="_blank" rel="nofollow"><?php
 echo $venue_postcode; ?></a></h3>
        <?php

            }
        ?>
          </div>
        <?php

          }

          if ($speakers) {
        ?>
          <div class="mb-4">
            <h5 class="font-size-10 font-weight-bold mb-2">Speakers:</h5>
        <?php

          foreach ($speakers as $speaker) {
        ?>
            <h3 class="font-size-11 font-weight-bold"><?php
 echo $speaker; ?></h3>
        <?php

          }
        ?>
          </div>
        <?php

          }

          if ($host) {
        ?>
          <div class="mb-4">
            <h5 class="font-size-10 font-weight-bold mb-2">Host:</h5>
            <h3 class="font-size-11 font-weight-bold"><?php
 echo $host; ?></h3>
          </div>
        <?php

          }

          if ($is_sold_out) {
        ?>
            <h4 class="font-size-10 font-weight-bold">Sold Out!</h4>
        <?php

          }

          if (!$is_sold_out && $tickets_url && $fromEvent->getDirection() !== 'past') {
        ?>
            <a href="<?php
 echo $tickets_url; ?>" target="_blank" rel="nofollow" class="ui-button ui-button--black"><h4>Buy Tickets</h4></a>
        <?php

          }
        ?>
        </div>
        <div class="grid-item is-s-16 is-xxl-12 text-copy font-size-10 font-serif">
          <?php
 the_content(); ?>
        </div>
      </div>
    </div>
    <?php

      if ($gallery) {
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
            <?php
 echo do_shortcode($gallery); ?>
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
      <article class="grid-item is-xxl-24"><?php
 _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </article>
<?php

} ?>
  <?php

    get_template_part( 'partials/support-section', null, array( 'container_classes' => 'mb-4' ) );
  ?>
</main>
<?php

get_footer();
?>
