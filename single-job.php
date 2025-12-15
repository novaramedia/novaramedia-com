<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

get_header();
?>
<main id="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );
    $email_subject = strtoupper( $post->post_title );
    $start_of_day = strtotime( 'today midnight' );
    $is_open = $meta['_nm_deadline'][0] >= $start_of_day;
    ?>
  <article id="job" class="container">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-4">
        <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
          Job listing
        </h4>
      </div>
      <div class="grid-item is-xxl-24 mb-5">
        <h1 class="font-size-13 font-weight-bold"><?php the_title(); ?></h1>
      </div>
    </div>
    <div class="grid-row mb-4">
      <article class="grid-item is-s-24 is-xl-14 is-xxl-10 page-copy">
        <?php
        if ( ! $is_open ) {
          ?>
        <p class="font-size-12 font-weight-bold text-uppercase">This job listing has now closed.</p>
          <?php
        }
          the_content();
        ?>
        <p>Closing date: <?php echo gmdate( 'j F Y', $meta['_nm_deadline'][0] ); ?></p>
      </div>
    </div>
  </article>
    <?php
  }
} else {
  ?>
  <div class="container">
    <div class="grid-row">
      <article class="grid-item is-xxl-24"><?php esc_html_e( 'Sorry, no jobs matched your criteria :{' ); ?></article>
    </div>
  </div>
  <?php
}
?>
</main>
<?php
get_footer();
?>
