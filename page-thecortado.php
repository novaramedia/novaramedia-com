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
        'newsletter' => 'The Cortado'
      ));
    ?>
    
  <!-- end post -->
  </article>
<?php
  }
} ?>
<!-- end main-content -->

<?php
  get_template_part('partials/support-section');
?>

</main>

<?php
get_footer();
?>