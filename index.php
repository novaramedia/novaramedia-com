<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

get_header();
?>
<main id="main-content" data-testid="main-content">
  <section id="posts" class="container mt-3" data-testid="post-list">
<?php
// Suggestions on page 1 only. Computed here so an empty post list can defer
// to them instead of saying nothing matched.
$search_destinations = is_search() && ! is_paged() ? nm_get_search_destination_matches( get_search_query( false ) ) : array();

if ( is_search() ) {
  get_template_part( 'partials/search-results-form' );
  get_template_part( 'partials/search-destinations', null, array( 'destinations' => $search_destinations ) );

  if ( have_posts() ) {
    ?>
    <div class="grid-row mb-3">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-10 font-weight-bold" data-testid="search-results-heading">Results</h4>
      </div>
    </div>
    <?php
  }
} elseif ( is_tag() ) {
  ?>
    <div class="grid-row mb-5">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-10 font-weight-bold">Tag: <?php single_tag_title(); ?></h4>
      </div>
    </div>
  <?php
}
?>
    <div class="grid-row mb-5">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();

    get_template_part(
      'partials/post-layouts/archive-post',
      null,
      array(
        'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
        'image-size'        => 'col12-16to9',
        'show-tags'         => true,
      )
    );
  }
} elseif ( empty( $search_destinations ) ) {
  ?>
    <article class="grid-item is-s-24" data-testid="no-results">Sorry, nothing matched your criteria :/</article>
  <?php
}
?>
    </div>
    <div class="grid-row mb-5">
      <div class="grid-item is-xxl-24">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
