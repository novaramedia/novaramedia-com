<?php
  $copy = IGV_get_option('_igv_privacy_notice');

  if (empty($copy)) {
    $copy = 'We are always working to improve this website for our users. To do this we use data provided by the cookies and external scripts.';
  }
?>
<div id="obligation-bar">
  <div class="container padding-top-tiny padding-bottom-tiny">
    <div class="flex-grid-row">
      <div class="flex-grid-item font-color-white text-links-underlined">
        <?php echo apply_filters('the_content', $copy); ?>
      </div>
      <div class="flex-grid-item">
        <a id="obligation-accept" class="obligation-button ui-button ui-button--white">Accept</a>
      </div>
    </div>
  </div>
</div>
