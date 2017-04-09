<?php
get_header();
?>

<!-- main content -->

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

    $venue_name = get_post_meta( $post->ID, '_cmb_venue_name', true );
    $venue_postcode = get_post_meta( $post->ID, '_cmb_venue_postcode', true );

    $speakers = get_post_meta( $post->ID, '_cmb_speakers', true );
    $host = get_post_meta( $post->ID, '_cmb_host', true );

    $is_sold_out = get_post_meta( $post->ID, '_cmb_tickets_sold_out', true );
    $tickets_url = get_post_meta( $post->ID, '_cmb_tickets', true );

    $gallery = get_post_meta( $post->ID, '_cmb_gallery', true );

?>
  <article id="event">
    <div class="container margin-bottom-large">
      <div class="row margin-bottom-basic">
        <div class="col col24">
          <h4>IRL</h4>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col24 text-align-center">
          <h1><?php echo $time->format('j'); ?><sup><?php echo $time->format('S'); ?></sup><?php echo $time->format(' F Y'); ?>: <?php the_title(); ?></h1>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col6"></div>
        <div class="col col12">
          <?php the_post_thumbnail('col12'); ?>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col2"></div>
        <div class="col col8">
          <div class="margin-bottom-small">
            <h5 class="margin-bottom-small">Time:</h5>
            <h3><?php echo $time->format('j'); ?><sup><?php echo $time->format('S'); ?></sup><?php echo $time->format(' F Y'); ?></h3>
            <h3><?php echo $time->format('H:i'); ?></h3>
          </div>
        <?php
          if ($venue_name) {
        ?>
          <div class="margin-bottom-small">
            <h5 class="margin-bottom-small">Venue:</h5>

            <h3><?php echo $venue_name; ?></h3>
        <?php
            if ($venue_postcode) {
        ?>
            <h3><a href="https://www.google.com/maps/search/<?php echo urlencode($venue_postcode); ?>" target="_blank" rel="nofollow"><?php echo $venue_postcode; ?></a></h3>
        <?php
            }
        ?>
          </div>
        <?php
          }
        ?>

        <?php
          if ($speakers) {
        ?>
          <div class="margin-bottom-small">
            <h5 class="margin-bottom-small">Speakers:</h5>

        <?php
          foreach ($speakers as $speaker) {
        ?>
            <h3><?php echo $speaker; ?></h3>
        <?php
          }
        ?>
          </div>
        <?php
          }
        ?>

        <?php
          if ($host) {
        ?>
          <div class="margin-bottom-small">
            <h5 class="margin-bottom-small">Host:</h5>

            <h3><?php echo $host; ?></h3>
          </div>
        <?php
          }
        ?>

        <?php
          if ($is_sold_out) {
        ?>
            <h4>Sold Out!</h4>
        <?php
          }
        ?>
        </div>
        <div class="col col12 text-copy font-italic">
          <?php the_content(); ?>
        </div>
      </div>
      <?php
        if (!$is_sold_out && $tickets_url) {
      ?>
      <div class="row margin-bottom-basic">
        <div class="col col2"></div>
        <div class="col col20">
          <a href="<?php echo $tickets_url; ?>" target="_blank" rel="nofollow" class="button"><h4>Buy Tickets</h4></a>
        </div>
      </div>
      <?php
        }
      ?>
    </div>

    <div class="background-black font-color-white padding-top-mid padding-bottom-mid">
      <div class="container">
        <div class="row margin-bottom-basic">
          <div class="col col24">
            <h4>Gallery: {x/x}</h4>
          </div>
        </div>
        <div class="row margin-bottom-basic">
          <div class="col col24">
            {gallery}
          </div>
        </div>
      </div>
    </div>
  </article>
<?php
  }
} else {
?>
  <article id="event" class="container margin-bottom-basic">
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </article>
<?php
} ?>

  <?php
    get_template_part('partials/announcement');

    get_template_part('partials/support-section');
  ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>