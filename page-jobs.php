<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_header();
?>
<!-- main content -->
<main id="main-content">
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );
    ?>
  <article id="page-jobs" class="container margin-top-small margin-bottom-large">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <h4 class="font-size-9 text-uppercase font-weight-bold"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?></h4>
      </div>
    </div>

    <div class="flex-grid-row flex-grid-row-m--reverse margin-bottom-small">
      <div class="flex-grid-item flex-item-m-12 flex-item-l-8 flex-item-xl-7 flex-item-xxl-6 page-copy">
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_content(); ?>
      </div>
      <div class="flex-grid-item flex-item-m-12 flex-item-l-4 flex-item-xl-5 flex-item-xxl-6">
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
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
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          foreach ( $jobs as $job ) {
            $deadline = get_post_meta( $job->ID, '_nm_deadline', true );
            ?>
          <li><a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo get_permalink( $job ); ?>" class="ui-action-link"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $job->post_title; ?> (deadline <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo gmdate( 'j F', $deadline ); ?>)</a></li>
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          }
        } else {
          ?>
        <h5 class="mobile-padding-bottom-small">There are currently no available positions</h5>
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
        }
        ?>
      </div>
    </div>
  </article>
    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  }
}
?>
<!-- end main-content -->
</main>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_footer();
?>
