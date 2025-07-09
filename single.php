<?php
get_header();
?>
<main id="main-content">
<?php
  $post_status = get_post_status();
if ( $post_status === 'draft' || $post_status === 'pending' ) {
  ?>
  <div class="background-yellow">
    <div class="container">
      <div class="grid-row pt-4 pb-4">
        <div class="grid-item is-xxl-24">
          <h1 class="font-size-12 font-weight-bold text-align-center text-uppercase">
            Caution: <?php echo $post_status; ?> post
          </h1>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>
  <article id="post" class="container mt-4 mb-4">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();

    $top_level_category = get_the_top_level_category( $post->ID );

    $focus_terms = wp_get_post_terms( $post->ID, 'focus' ); // check for focus on the post
    $focus_tax = count( $focus_terms ) > 0 ? $focus_terms[0] : false;
    ?>

<div class="grid-row mb-4 mb-s-2">
  <div class="grid-item is-xxl-24">
    <?php render_post_ui_tags( $post->ID ); ?>
  </div>
</div>

    <?php
    if ( $top_level_category->slug === 'articles' ) {
      get_template_part( 'partials/singles/single-post-articles' );
    } elseif ( $top_level_category->slug === 'audio' ) {
      get_template_part( 'partials/singles/single-post-audio' );
    } elseif ( $top_level_category->slug === 'video' ) {
      get_template_part( 'partials/singles/single-post-video' );
    }
  }
} else {
  ?>
    <div class="grid-row">
      <article class="grid-item is-xxl-24"><?php esc_html_e( 'Sorry, no posts matched your criteria :[' ); ?></article>
    </div>
  <?php
}
?>
  </article>
  <?php
    get_template_part( 'partials/support-section' );

    get_template_part( 'partials/singles/single-related' );
  ?>
</main>
<?php
get_footer();
?>
