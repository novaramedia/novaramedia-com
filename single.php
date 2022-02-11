<?php
get_header();
?>

<!-- main content -->

<main id="main-content">
<?php
  $post_status = get_post_status();
  if ($post_status === 'draft' || $post_status === 'pending') {
?>
  <div class="background-yellow">
    <div class="container">
      <div class="flex-grid-row padding-top-small padding-bottom-small">
        <div class="flex-grid-item flex-item-xxl-12">
          <h1 class="font-size-3 text-align-center font-uppercase">
            Caution: <?php echo $post_status; ?> post
          </h1>
        </div>
      </div>
    </div>
  </div>
<?php
   }
?>
  <!-- main posts loop -->
  <article id="post" class="container margin-top-small margin-bottom-basic">
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
    <div class="flex-grid-row">
      <article class="flex-grid-item flex-item-xxl-12"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
<?php
} ?>
  <!-- end post -->
  </article>

  <?php
    get_template_part('partials/support-section');

    get_template_part('partials/singles/single-related');
  ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>
