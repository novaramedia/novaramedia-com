<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <section id="post" class="container margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    $categories = get_the_category();

    $topLevelCategory = array_filter($categories, 'only_top_level_category_filter');
    $topLevelCategory = array_values($topLevelCategory);
    $topLevelCategory = $topLevelCategory[0]->slug;

    if ($topLevelCategory === 'articles') {

      get_template_part('partials/singles/single-post-articles');

    } else if ($topLevelCategory === 'audio') {

      get_template_part('partials/singles/single-post-audio');

    } else if ($topLevelCategory === 'video') {

      get_template_part('partials/singles/single-post-video');

    } else {
?>
      <div class="row">
        <article class="col col24">Error with post. Someone did something wrong :/</article>
      </div>
<?php
    }

  }
} else {
?>
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
<?php
} ?>
  <!-- end post -->
  </section>

  <?php get_template_part('partials/support-section'); ?>

  <section id="single-related" class="container margin-top-large margin-bottom-large">
    <?php get_template_part('partials/singles/single-related'); ?>
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>