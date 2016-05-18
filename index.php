<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <section id="posts" class="container">
    <div class="row margin-bottom-basic">
<?php
if( have_posts() ) {
  $i = 0;
  while( have_posts() ) {
    the_post();
    $description = get_post_meta($post->ID, '_cmb_short_desc');

    if ($i % 3 === 0 && $i !== 0) {
      echo "</div>\n<div class=\"row margin-bottom-basic\">";
    }
?>

    <a href="<?php the_permalink() ?>">
      <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">

        <?php the_post_thumbnail('col8-16to9', array('class' => 'index-post-thumbnail')); ?>

        <h5 class="index-post-title margin-top-tiny margin-bottom-tiny js-fix-widows"><?php the_title(); ?></h5>

        <div class="index-post-description">
          <?php
            if (!empty($description)) {
              echo $description[0];
            } else {
              the_excerpt();
            }
          ?>
        </div>

      </article>
    </a>

<?php
    $i++;
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