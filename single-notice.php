<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <section id="notice" class="container margin-top-small margin-bottom-large">
    <div class="row">
      <div class="col col24">
        <h4 class="margin-bottom-basic">Notices</h4>
      </div>
    </div>
    <div class="row">
<?php
if (have_posts()) {
    while (have_posts()) {
        the_post(); ?>
      <article class="col col10">
        <header class="margin-bottom-small">
          <h5><?php the_title(); ?></h5>
          <h5><?php the_time('j F Y'); ?></h5>
        </header>
        <?php the_content(); ?>
      </article>
<?php
    }
} else {
    ?>
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
  <!-- end post -->
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>