<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

get_header();
?>
<main id="main-content">
  <section id="posts" class="container">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
          Events
        </h4>
      </div>
    </div>
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    get_template_part( 'partials/post-layouts/archive-event' );
  }
} else {
  ?>
    <article class="u-alert">Sorry, no posts matched your criteria :{</article>
  <?php
}
?>
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
