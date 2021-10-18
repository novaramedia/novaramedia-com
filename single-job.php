<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
    
    $email_subject = urlencode(strtoupper($post->post_title));
?>
  <!-- main posts loop -->
  <article id="job" class="container margin-top-small margin-bottom-large">
    <div class="row margin-bottom-basic">
      <div class="col col24">
        <h4>Job: <?php the_title(); ?></h4>
      </div>
    </div>
    <div class="row margin-bottom-basic">
      <div class="col col10 page-copy">
        <?php the_content(); ?>
        <p>Send your application in a single email to <a href="mailto:info@novaramedia.com?subject=<?php echo $email_subject; ?>">info@novaramedia.com</a> with <?php echo $email_subject; ?> in the subject line.</p>
        <p>Closing date: <?php echo date('j F Y', $meta['_nm_deadline'][0]); ?></p>
      </div>      
    </div>
  <!-- end post -->
  </article>
<?php
  }
} else {
?>
  <div class="container">
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </div>
<?php
} ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>