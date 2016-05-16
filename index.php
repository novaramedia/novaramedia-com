<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <section id="posts" class="container">
    <div class="row">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
?>

    <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">

      <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>

      <?php the_content(); ?>

    </article>

<?php
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="row">
      <div class="col col24">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>

  <!-- end posts -->
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>