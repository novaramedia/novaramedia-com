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
?>
  <!-- main posts loop -->
  <article id="page">
    <div class="container margin-bottom-large">
      <div class="row margin-bottom-basic">
        <div class="col col24">
          <h4><?php the_title(); ?></h4>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col10 page-copy">
          <?php the_content(); ?>
        </div>
        <div class="col col2"></div>
        <div class="col col10 page-copy">
          <?php if (!empty($meta['_cmb_page_extra'])) {
            echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0]);
          } ?>
        </div>
      </div>
    </div>
    
    <?php
      get_template_part('partials/email-signup', null, array(
        'netlify' => 'http://localhost:53751/.netlify/functions/mailchimp-signup/',
        'newsletter' => 'The Cortado',
        'copy' => 'Sign up to The Cortado—your weekly shot of political analysis from Ash Sarkar, plus a round up of the week’s content. It’s brewed every Friday morning.'
      ));

      get_template_part('partials/email-signup', null, array(
        'netlify' => 'http://localhost:53751/.netlify/functions/mailchimp-signup/',
        'newsletter' => 'The Pick',
        'copy' => 'Sign up to The Pick–our weekly newsletter selecting the pick of the weeks articles.'
      ));
    ?>
  
    
  <!-- end post -->
  </article>
<?php
  }
} ?>
<!-- end main-content -->

</main>

<?php
get_footer();
?>