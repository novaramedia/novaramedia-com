<?php
get_header();
?>
<main id="main-content">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
    $email_subject = strtoupper($post->post_title);
    $has_closed = $meta['_nm_deadline'][0] < time();
?>
  <article id="job" class="container mt-4 mb-6">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold mb-4">Job: <?php the_title(); ?></h4>
      </div>
    </div>
    <div class="grid-row mb-4">
      <article class="grid-item is-s-24 is-xl-14 is-xxl-10 page-copy">
        <?php
          if ($has_closed) {
        ?>
        <p class="fs-6 text-uppercase font-weight-bold">This job listing has now closed.</p>
        <?php
          }

          the_content();
        ?>
        <p>Send your application in a single email to <a href="mailto:info@novaramedia.com?subject=<?php echo urlencode($email_subject); ?>">info@novaramedia.com</a> with <?php echo $email_subject; ?> in the subject line.</p>
        <p>Closing date: <?php echo date('j F Y', $meta['_nm_deadline'][0]); ?></p>
      </div>
    </div>
  </article>
<?php
  }
} else {
?>
  <div class="container">
    <div class="grid-row">
      <article class="grid-item is-xxl-24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </div>
<?php
} ?>
</main>
<?php
get_footer();
?>
