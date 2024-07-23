<?php
get_header();
?>
<!-- main content -->
<main id="main-content">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
?>
  <article id="page-jobs" class="container margin-top-small margin-bottom-large">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <h4 class="fs-3-sans text-uppercase font-weight-bold"><?php the_title(); ?></h4>
      </div>
    </div>

    <div class="flex-grid-row flex-grid-row-m--reverse margin-bottom-small">
      <div class="flex-grid-item flex-item-m-12 flex-item-l-8 flex-item-xl-7 flex-item-xxl-6 page-copy">
        <?php the_content(); ?>
      </div>
      <div class="flex-grid-item flex-item-m-12 flex-item-l-4 flex-item-xl-5 flex-item-xxl-6">
        <?php
          $jobs = get_posts(array(
            	'numberposts'	=> -1,
            	'post_type' => 'job',
            	'orderby' => 'meta_value',
            	'order' => 'ASC',
            	'meta_key' => '_nm_deadline',
            	'meta_value' => (date('U') - 86400), // today minus one day of seconds to make display inclusive of deadline day
            	'meta_type' => 'NUMERIC',
            	'meta_compare' => '>=',
          ));

          if (!empty($jobs)) {
        ?>
        <h5 class="font-size-2">We are currently hiring:</h5>
        <ul class="list--links font-size-2">
        <?php
            foreach ($jobs as $job) {
              $deadline = get_post_meta($job->ID, '_nm_deadline', true);
        ?>
          <li><a href="<?php echo get_permalink($job); ?>"><?php echo $job->post_title; ?> (deadline <?php echo date('j F', $deadline); ?>)</a></li>
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
