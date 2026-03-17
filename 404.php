<?php
get_header();

// Extract the last path segment from the failed URL and convert slug to search terms
$request_path = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
$parsed_path  = wp_parse_url( trim( $request_path, '/' ), PHP_URL_PATH );
$last_segment = basename( $parsed_path ? $parsed_path : '' );
$search_hint  = str_replace( array( '-', '_' ), ' ', $last_segment );
?>

<main id="main-content" data-testid="main-content">

  <div class="container">
    <div class="grid-row mt-4 mb-5">
      <div class="grid-item is-xxl-16 is-xxl-offset-4 is-s-24">
        <h1 class="font-size-20 font-weight-bold mb-4">404!</h1>
        <h2>Sorry whatever you were looking for isn't here</h2>
      </div>
    </div>
  </div>

  <div class="container mb-5">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <div class="background-gray ui-rounded-box ui-backgrounded-box-padding font-color-white">
          <form role="search" method="get" class="site-header-search__form font-size-11" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">
            <div class="grid-row">
              <div class="grid-item is-xxl-24">
                <h3 class="mb-4">Search for something</h3>
                <label class="u-visuallyhidden" for="404-search-input">Search this site</label>
              </div>
              <div class="grid-item is-s-20 is-xxl-22">
                <input id="404-search-input" class="site-header-search__input ui-input" type="text" placeholder="Search" value="<?php echo esc_attr( $search_hint ); ?>" name="s" required aria-required="true">
              </div>
              <div class="grid-item is-s-4 is-xxl-2">
                <button type="submit" class="site-header-search__submit ui-button ui-button--fill-width" role="button" aria-label="Submit Search"><i class="icon-search"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-16 is-xxl-offset-4 is-s-24">
        <h3>Or view the latest</h3>
      </div>
    </div>
  </div>

  <section class="container mb-5">
    <div class="grid-row">
<?php
$recent_posts = new WP_Query(
  array(
    'posts_per_page' => 9,
    'post_status'    => 'publish',
  )
);
if ( $recent_posts->have_posts() ) {
  while ( $recent_posts->have_posts() ) {
    $recent_posts->the_post();

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
  wp_reset_postdata();
} else {
  ?>
    <p>Sorry, no posts matched your criteria :/</p>
  <?php
}
?>
    </div>
  </section>

</main>

<?php
get_footer();
?>
