<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <article id="post" class="container margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    $categories = get_the_category();
    $topLevelCategory = array_filter($categories, 'only_top_level_category_filter');

    if (!$topLevelCategory) { // if there is no top level category set to post
      if ($categories[0]->parent) { // then check the first category set
        $topLevelCategory = get_category($categories[0]->parent); // and if there is a parent
        $topLevelCategory = $topLevelCategory->slug; // get the slug
      }
    } else {
      $topLevelCategory = array_values($topLevelCategory); // if there is a top level category
      $topLevelCategory = $topLevelCategory[0]->slug; // get the slug from it
    }

    if ($topLevelCategory === 'articles') {

      get_template_part('partials/singles/single-post-articles');

    } else if ($topLevelCategory === 'audio') {

      get_template_part('partials/singles/single-post-audio');

    } else if ($topLevelCategory === 'video') {

      get_template_part('partials/singles/single-post-video');

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
  </article>

  <?php
    get_template_part('partials/announcement');

    get_template_part('partials/support-section');
  ?>

  <section id="single-related" class="container margin-top-large margin-bottom-large">
    <?php get_template_part('partials/singles/single-related'); ?>
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>