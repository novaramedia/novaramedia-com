<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
  <article id="page" class="container mt-4 margin-bottom-large">
    <?php

      if (!empty($meta['_nm_subtitle'][0])) {
    ?>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-offset-xxl-12">
        <h4 class="font-size-9 text-uppercase font-weight-bold"><?php
 echo $meta['_nm_subtitle'][0]; ?></h4>
      </div>
    </div>
    <?php
 } ?>

    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-9 flex-offset-l-1 flex-item-xl-8 flex-offset-xl-2 flex-item-xxl-8 flex-offset-xxl-2">
        <h1 id="single-articles-title" class="js-fix-widows"><?php
 the_title(); ?></h1>
      </div>
    </div>

    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-12 flex-offset-m-0 flex-item-l-10 flex-offset-l-1 flex-item-xl-10 flex-offset-xl-1 flex-item-xxl-10 flex-offset-xxl-1">
        <?php
 the_post_thumbnail('col20'); ?>
        <div class="font-size-8">
          <?php
 the_post_thumbnail_caption(); ?>
        </div>
      </div>
    </div>

    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-8 flex-offset-m-2 flex-item-l-10 flex-offset-l-1 flex-item-xl-2 flex-item-xxl-2">
      </div>

      <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-10 flex-offset-l-1 flex-item-xl-8 flex-offset-xl-0 flex-item-xxl-6 flex-offset-xxl-1">
        <div class="text-copy">
          <?php
 the_content(); ?>
        </div>
        <?php

          if (!empty($meta['_nm_cta-link'][0]) && !empty($meta['_nm_cta-copy'][0])) {
        ?>
          <p><a class="ui-button ui-button--black font-size-10 font-weight-bold" href="<?php
 echo $meta['_nm_cta-link'][0]; ?>"><?php
 echo $meta['_nm_cta-copy'][0]; ?></a></p>
        <?php
 } ?>
      </div>

      <?php
 if (!empty($meta['_cmb_page_extra'])) { ?>
        <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-8 flex-offset-m-2 flex-item-l-10 flex-offset-l-1 flex-item-xl-2 flex-offset-xl-0 flex-item-xxl-2 flex-offset-xxl-1">
          <?php
 echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0]); ?>
        </div>
      <?php
 } ?>
    </div>
  </article>
<?php

  }
} else {
?>
  <div class="container">
    <div class="flex-grid-row margin-top-mid margin-bottom-basic">
      <article class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-10 flex-offset-l-1 flex-item-xl-8 flex-offset-xl-0 flex-item-xxl-6 flex-offset-xxl-1">
        <?php
 _e('Sorry, no posts matched your criteria :{'); ?>
      </article>
    </div>
  </div>
<?php

} ?>
</main>
<?php

get_footer();
?>
