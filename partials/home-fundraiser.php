<?php
/**
 * Home fundraiser partial.
 *
 * @deprecated Not referenced anywhere in the theme. Uses legacy col grid.
 *             Safe to delete. Left in place pending cleanup — the single
 *             `render_youtube_embed_iframe()` call here is intentionally
 *             not updated to the new signature since this file is unused.
 */
$fundraiser_youtube_id = IGV_get_option( '_igv_fundraiser_youtube_id' );
if ( $fundraiser_youtube_id ) {
  ?>
<section id="home-featured" class="container mb-5">
  <div class="row">
     <div class="col col24 mb-4">
      <h4><a href="http://support.novaramedia.com">Fundraiser</a></h4>
    </div>
  </div>
  <div class="row">
    <div class="col col24">
      <div class="u-video-embed-container">
        <?php echo render_youtube_embed_iframe( $fundraiser_youtube_id ); ?>
      </div>
    </div>
  </div>
</section>
  <?php
  get_template_part( 'partials/support-section', null, array( 'container_classes' => 'mb-5' ) );
}
?>
