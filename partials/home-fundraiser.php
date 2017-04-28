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
        <iframe class="youtube-player" type="text/html" src="http://www.youtube.com/embed/<?php echo $fundraiser_youtube_id; ?>?autohide=2&amp;modestbranding=1&amp;origin=http://novaramedia.com&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
      </div>
    </div>
  </div>
</section>
<?php

  get_template_part('partials/support-section');

  }
?>