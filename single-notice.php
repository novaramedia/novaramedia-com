<?php
get_header();
?>
<main id="main-content">
  <section id="notice" class="container mt-4 mb-6">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <h4 class="fs-3-sans text-uppercase font-weight-bold mb-4">Notices</h4>
      </div>
    </div>
    <div class="grid-row">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
?>
      <article class="grid-item is-s-24 is-xl-14 is-xxl-10">
        <header class="mb-4">
          <h5><?php the_title(); ?></h5>
          <h5><?php the_time('j F Y'); ?></h5>
        </header>
        <div class="page-copy">
          <?php the_content(); ?>
        </div>
      </article>
<?php
  }
} else {
?>
      <article class="grid-item is-xxl-24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
  </section>
</main>
<?php
get_footer();
?>
