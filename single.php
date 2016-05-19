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
    $meta = get_post_meta($post->ID);

    $categories = get_the_category();

    function onlyTopLevel($var) {
      if ($var->category_parent == 0) {
        return true;
      }
    }

    $topLevelCategory = array_filter($categories, 'onlyTopLevel');
    $topLevelCategory = array_values($topLevelCategory);
    $topLevelCategory = $topLevelCategory[0]->slug;

    if ($topLevelCategory === 'wire') {

      get_template_part('partials/singles/single-post-wire');

    } else if ($topLevelCategory === 'fm') {

      get_template_part('partials/singles/single-post-fm');

    } else if ($topLevelCategory === 'tv') {

      get_template_part('partials/singles/single-post-tv');

    } else {

      // handle error here :/

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

  <section id="single-support">
    // support section here
  </section>

  <section id="single-related" class="container margin-top-large margin-bottom-large">
    <div class="row">
      <div class="col col24">
        <h4>Related</h4>
      </div>
    </div>
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>