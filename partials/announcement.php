<?php
  $announcement_expiration = IGV_get_option('_igv_announcement_time');

  if ($announcement_expiration > time()) {

    $announcement_link = IGV_get_option('_igv_announcement_link');

    if (!$announcement_link) {
      $announcement_link_ext = IGV_get_option('_igv_announcement_link_ext');
      $link = '<a href="' . $announcement_link_ext . '" target="_blank" rel="noopener">';
    } else {
      $link = '<a href="' . get_the_permalink($announcement_link) . '">';
    }

    $announcement_text = IGV_get_option('_igv_announcement_text');
    $announcement_image = IGV_get_option('_igv_announcement_image_id');

?>
<div class="annoucement-section background-black font-color-white margin-top-basic padding-top-mid padding-bottom-mid">
  <div class="container">
    <div class="row margin-bottom-small">
      <div class="col col24">
        <?php echo $link; ?><h4>Announcement</h4></a>
      </div>
    </div>
    <?php
      if ($announcement_image) {
    ?>
    <div class="row margin-top-mid margin-bottom-mid">
      <div class="col col2 only-desktop"></div>
      <div class="col col4 only-desktop">
        <?php echo $link; ?>
          <?php echo wp_get_attachment_image($announcement_image, 'col4-square'); ?>
        </a>
      </div>
      <div class="col col1 only-desktop"></div>
      <div class="col col15 font-size-h2">
        <?php echo $link; ?>
          <?php if ($announcement_text) {
            echo $announcement_text;
          } ?>
        </a>
      </div>
    </div>
    <?php
      } else {
    ?>
    <div class="row margin-top-mid margin-bottom-mid">
      <div class="col col4 only-desktop"></div>
      <div class="col col16 font-size-h2">
        <?php echo $link; ?>
          <?php if ($announcement_text) {
            echo $announcement_text;
          } ?>
        </a>
      </div>
    </div>
    <?php
      }
    ?>
    <div class="row margin-bottom-small">
      <div class="col col24 text-align-right">
        <?php echo $link; ?><h4>Find out more</h4></a>
      </div>
    </div>
  </div>
</div>
<?php
  }