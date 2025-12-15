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
  <article id="page-jobs" class="container">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-5">
        <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
          Jobs
        </h4>
      </div>
    </div>

    <div class="flex-grid-row flex-grid-row-m--reverse margin-bottom-small">
      <div class="flex-grid-item flex-item-m-12 flex-item-l-8 flex-item-xl-7 flex-item-xxl-6 page-copy">
        <?php the_content(); ?>
      </div>
      <div class="flex-grid-item flex-item-m-12 flex-item-l-4 flex-item-xl-5 flex-item-xxl-6">
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
        <ul class="mt-4 mb-4">
          <?php
          foreach ( $jobs as $job ) {
            $deadline = get_post_meta( $job->ID, '_nm_deadline', true );
            ?>
          <li><a href="<?php echo get_permalink( $job ); ?>" class="ui-action-link"><?php echo $job->post_title; ?> (deadline <?php echo gmdate( 'j F', $deadline ); ?>)</a></li>
            <?php
          }
        } else {
          ?>
        <h5 class="mb-s-4">There are currently no available positions</h5>
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
