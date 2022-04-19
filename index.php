<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <section id="posts" class="container margin-top-small">

<?php
if (is_search()) {
?>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <h4>Search Results for: <?php echo get_search_query(); ?></h4>
      </div>
    </div>
<?php
} else if (is_tag()) {
?>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <h4><?php single_tag_title('Tag: '); ?></h4>
      </div>
    </div>
<?php
}
?>
    <div class="flex-grid-row margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4 margin-bottom-basic',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="flex-grid-item flex-item-s-12"><?php _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
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
