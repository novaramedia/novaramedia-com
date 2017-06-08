<?php
  $special = IGV_get_option('_igv_front_special');

  if (!empty($special)) {
?>
<section id="home-special" class="container margin-bottom-basic mobile-margin-bottom-basic">
  <div class="row">
     <div class="col col24 margin-bottom-small">
      <h4>Special</h4>
    </div>
  </div>
  <div class="row">
    <div class="col col24">
      <div class="u-video-embed-container">
        <?php echo $special; ?>
      </div>
    </div>
  </div>
</section>
<?php
  }
?>