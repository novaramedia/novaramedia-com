<?php
get_header();
?>
<!-- main content -->
<main id="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );
    ?>
  <article id="page-jobs" class="container margin-top-small margin-bottom-large">
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-s-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold"><?php the_title(); ?></h4>
      </div>
    </div>

    <div class="grid-row grid-row-m--reverse margin-bottom-small">
      <div class="grid-item is-m-24 is-l-16 is-xl-14 is-xxl-12 page-copy">
        <?php the_content(); ?>
      </div>
      <div class="grid-item is-m-24 is-l-8 is-xl-10 is-xxl-12">
        <?php
        $start_of_day = strtotime( 'today midnight' );
        $jobs = get_posts(
            array(
                'numberposts'  => -1,
                'post_type'    => 'job',
                'orderby'      => 'meta_value',
                'order'        => 'ASC',
                'meta_key'     => '_nm_deadline',
                'meta_value'   => $start_of_day,
                'meta_type'    => 'NUMERIC',
                'meta_compare' => '>=', // anything closing today or later
            )
        );

        if ( ! empty( $jobs ) ) {
          ?>
        <h5 class="font-size-10">We are currently hiring:</h5>
        <ul>
          <?php
          foreach ( $jobs as $job ) {
            $deadline = get_post_meta( $job->ID, '_nm_deadline', true );
            ?>
          <li><a href="<?php echo get_permalink( $job ); ?>" class="ui-action-link"><?php echo $job->post_title; ?> (deadline <?php echo gmdate( 'j F', $deadline ); ?>)</a></li>
            <?php
          }
        } else {
          ?>
        <h5 class="mobile-padding-bottom-small">There are currently no available positions</h5>
          <?php
        }
        ?>
      </div>
    </div>
  </article>
    <?php
  }
}
?>
<!-- end main-content -->
</main>
<?php
get_footer();
?>
