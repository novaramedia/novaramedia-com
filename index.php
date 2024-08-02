<?php
get_header();
?>
<main id="main-content">
  <section id="posts" class="container mt-3">
<?php
if (is_search() || is_tag()) {
?>
    <div class="grid-row mb-5">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-10 font-weight-bold">
<?php
  if (is_search()) {
?>
          Search Results for: <?php echo get_search_query(); ?></h4>
<?php
  } else if (is_tag()) {
?>
          Tag: <?php single_tag_title(); ?></h4>
<?php
  }
?>
      </div>
    </div>
<?php
}
?>
    <div class="grid-row mb-5">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="grid-item is-s-24"><?php _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
} ?>
    </div>
    <div class="grid-row mb-5">
      <div class="grid-item is-xxl-24">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
