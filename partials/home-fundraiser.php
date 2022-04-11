<?php
  $fundraiser_youtube_id = IGV_get_option('_igv_fundraiser_youtube_id');
  if ($fundraiser_youtube_id) {
      ?>

<section id="home-featured" class="container margin-bottom-basic mobile-margin-bottom-basic">
  <div class="row">
     <div class="col col24 margin-bottom-small">
      <h4><a href="http://support.novaramedia.com">Fundraiser</a></h4>
    </div>
  </div>
  <div class="row">
    <div class="col col24">
      <div class="u-video-embed-container">
        <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($fundraiser_youtube_id); ?>"></iframe>
      </div>
    </div>
  </div>
</section>
<?php

  get_template_part('partials/support-section');
  }
?>