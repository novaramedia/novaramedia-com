<?php
  $copy = IGV_get_option('_igv_privacy_notice');

  if (empty($copy)) {
    $copy = 'We are always working to improve this website for our users. To do this we use data provided by the cookies and external scripts.';
  }
?>
<div id="gdpr">
  <div class="container margin-top-basic">
    <div class="row">
      <div class="col col24 margin-bottom-small font-color-white">
        <?php echo apply_filters('the_content', $copy); ?>
      </div>
      <div class="col col24 margin-bottom-basic">
        <a id="gdpr-accept" class="gdpr-button button u-pointer">Accept</a>
      </div>
    </div>
  </div>
</div>