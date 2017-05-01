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
    $fundraiser_youtube_id = IGV_get_option('_igv_fundraiser_youtube_id');
?>
  <!-- main posts loop -->
  <article id="page">
    <div class="container">
      <div class="row margin-bottom-small">
        <div class="col col24">
          <h4><?php the_title(); ?></h4>
        </div>
      </div>
    </div>
  <?php
    if (!empty($meta['_cmb_support_youtube']) || $fundraiser_youtube_id) {
      $youtube_id = $meta['_cmb_support_youtube'];
      if ($fundraiser_youtube_id) {
        $youtube_id= $fundraiser_youtube_id;
      }
  ?>
    <div class="container">
      <div class="row margin-bottom-basic">
        <div class="col col24">
          <div class="u-video-embed-container">
            <iframe class="youtube-player" type="text/html" src="http://www.youtube.com/embed/<?php echo $youtube_id; ?>?autohide=2&amp;modestbranding=1&amp;origin=http://novaramedia.com&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
          </div>
        </div>
      </div>
    </div>
  <?php
      get_template_part('partials/support-section');
    }
  ?>
    <div class="container">
      <div class="row margin-top-basic margin-bottom-basic">
        <div class="col col24">
          <div class="text-copy text-copy-max-width">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    </div>

    <?php
      get_template_part('partials/support-section');

      if (!empty($meta['_cmb_page_extra_section'])) {
    ?>
    <div class="container">
      <div class="row margin-top-basic margin-bottom-basic">
        <div class="col col24">
          <div class="text-copy text-copy-max-width">
            <?php echo apply_filters( 'the_content', $meta['_cmb_page_extra_section'][0]); ?>
          </div>
        </div>
      </div>
    </div>
<?php
        get_template_part('partials/support-section');

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