<?php
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

<a href="<?php the_permalink(); ?>">
  <div class="row margin-bottom-mid">
    <div class="col col1"></div>
    <div class="col col9">
      <?php the_post_thumbnail('col12'); ?>
    </div>
    <div class="col col1"></div>
    <div class="col col13">
      <h3 class="margin-bottom-tiny"><?php echo $time->format('j'); ?><sup><?php echo $time->format('S'); ?></sup><?php echo $time->format(' F Y'); ?>:</h3>
      <h2 class="margin-bottom-tiny"><?php the_title(); ?></h2>
      <?php
        if ($venue_name) {
      ?>
        <h3>At <?php echo $venue_name; ?></h3>
      <?php
        }
      ?>
      <?php
        if ($speakers) {
      ?>
        <h3>With <?php
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
        }
      ?>
      <?php
        if ($host) {
      ?>
        <h3>Hosted by <?php echo $host; ?></h3>
      <?php
        }
      ?>
    </div>
  </div>
</a>