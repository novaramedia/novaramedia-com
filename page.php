<?php
get_header();
?>
<main id="main-content">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
?>
  <article id="page" class="container mt-4 mb-6">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold"><?php the_title(); ?></h4>
      </div>
    </div>
    <div class="grid-row mb-4">
      <div class="grid-item is-m-24 is-xxl-10 page-copy">
        <?php the_content(); ?>
      </div>
      <div class="grid-item offset-m-0 is-m-24 offset-xxl-2 is-xxl-10 page-copy">
        <?php if (!empty($meta['_cmb_page_extra'])) {
          echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0]);
        } ?>
      </div>
    </div>
<?php
    if (!empty($meta['_cmb_page_extra_section'])) {
      if (!empty($meta['_cmb_page_extra_section_title'])) {
?>
    <div class="grid-row margin-top-large mb-4">
      <div class="grid-item is-xxl-24">
        <h5 class="font-size-9 text-uppercase font-weight-bold"><?php echo $meta['_cmb_page_extra_section_title'][0]; ?></h5>
      </div>
    </div>
<?php
      }
?>
    <div class="grid-row mb-5">
      <div class="grid-item is-m-24 is-xxl-10 page-copy">
        <?php echo apply_filters('the_content', $meta['_cmb_page_extra_section'][0]); ?>
      </div>
    </div>
<?php
    }
?>
  </article>
<?php
  }
} else {
?>
  <div class="container">
    <div class="grid-row">
      <article class="grid-item is-xxl-24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </div>
<?php
} ?>
</main>
<?php
get_footer();
?>
