<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <section id="posts" class="container">

<?php
if (is_search()) {
?>
    <div class="row margin-bottom-basic">
      <div class="col col24">
        <h4>Search Results for: <?php echo get_search_query(); ?></h4>
      </div>
    </div>
<?php
} else if (is_tag()) {
?>
    <div class="row margin-bottom-basic">
      <div class="col col24">
        <h4><?php single_tag_title('Tag: '); ?></h4>
      </div>
    </div>
<?php
}
?>
    <div class="row margin-bottom-basic">
<?php
if( have_posts() ) {
  $i = 0;
  while( have_posts() ) {
    the_post();

    if ($i % 3 === 0 && $i !== 0) {
      echo "</div>\n<div class=\"row margin-bottom-basic\">";
    }

    get_template_part('partials/post-layouts/post-col8');

    $i++;
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="row margin-bottom-basic">
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