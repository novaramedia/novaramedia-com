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
  <!-- main posts loop -->
  <article id="page" class="container margin-bottom-large">
    <div class="row margin-bottom-basic">
      <div class="col col24">
        <h4><?php the_title(); ?></h4>
      </div>
    </div>
    <div class="row margin-bottom-basic">
      <div class="col col10">
        <?php the_content(); ?>
      </div>
      <div class="col col2"></div>
      <div class="col col10">
        <?php if (!empty($meta['_cmb_page_extra'])) {
          echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0]);
        } ?>
      </div>
    </div>
<?php
    if (!empty($meta['_cmb_page_extra_section'])) {
      if (!empty($meta['_cmb_page_extra_section_title'])) {
?>
    <div class="row margin-top-large margin-bottom-basic">
      <div class="col col24">
        <h5><?php echo $meta['_cmb_page_extra_section_title'][0]; ?></h5>
      </div>
    </div>
<?php
      }
?>
    <div class="row margin-bottom-mid">
      <div class="col col10">
        <?php echo apply_filters('the_content', $meta['_cmb_page_extra_section'][0]); ?>
      </div>
    </div>
<?php
    }
?>
  <!-- end post -->
  </article>
<?php
  }
} else {
?>
  <div class="container">
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </div>
<?php
} ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>