<?php
/* Template Name: Text Copy */
// TODO: Refactor this to use the new grid system and type scale
get_header();
?>
<main id="main-content">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
?>
  <article id="page" class="container mt-4 mb-8">
    <?php
      if (!empty($meta['_nm_subtitle'][0])) {
    ?>
    <div class="grid-row mb-5">
      <div class="grid-item offset-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold"><?php echo $meta['_nm_subtitle'][0]; ?></h4>
      </div>
    </div>
    <?php } ?>

    <div class="grid-row mb-5">
      <div class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-18 offset-l-2 is-xl-16 offset-xl-4 is-xxl-16 offset-xxl-4">
        <h1 id="single-articles-title" class="text-wrap-pretty"><?php the_title(); ?></h1>
      </div>
    </div>

    <div class="grid-row mb-5">
      <div class="grid-item is-s-24 offset-s-0 is-m-24 offset-m-0 is-l-20 offset-l-2 is-xl-20 offset-xl-2 is-xxl-20 offset-xxl-2">
        <?php the_post_thumbnail('col20'); ?>
        <div class="font-size-8">
          <?php the_post_thumbnail_caption(); ?>
        </div>
      </div>
    </div>

    <div class="grid-row mb-5">
      <div class="grid-item is-s-24 offset-s-0 is-m-16 offset-m-4 is-l-20 offset-l-2 is-xl-4 is-xxl-4">
      </div>

      <div class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-20 offset-l-2 is-xl-16 offset-xl-0 is-xxl-12 offset-xxl-2">
        <div class="text-copy">
          <?php the_content(); ?>
        </div>
        <?php
          if (!empty($meta['_nm_cta-link'][0]) && !empty($meta['_nm_cta-copy'][0])) {
        ?>
          <p><a class="ui-button ui-button--black font-size-10 font-weight-bold" href="<?php echo $meta['_nm_cta-link'][0]; ?>"><?php echo $meta['_nm_cta-copy'][0]; ?></a></p>
        <?php } ?>
      </div>

      <?php if (!empty($meta['_cmb_page_extra'])) { ?>
        <div class="grid-item is-s-24 offset-s-0 is-m-16 offset-m-4 is-l-20 offset-l-2 is-xl-4 offset-xl-0 is-xxl-4 offset-xxl-2">
          <?php echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0]); ?>
        </div>
      <?php } ?>
    </div>
  </article>
<?php
  }
} else {
?>
  <div class="container">
    <div class="grid-row mt-6 mb-5">
      <article class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-20 offset-l-2 is-xl-16 offset-xl-0 is-xxl-12 offset-xxl-2">
        <?php _e('Sorry, no posts matched your criteria :{'); ?>
      </article>
    </div>
  </div>
<?php
} ?>
</main>
<?php
get_footer();
?>
