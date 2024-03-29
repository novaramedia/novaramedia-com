<?php
  $headline = NM_get_option('nm_fundraising_settings_header_cta_headline', 'nm_fundraising_options');
  $copy = NM_get_option('nm_fundraising_settings_header_cta_text', 'nm_fundraising_options');

  if (!empty($headline) && !empty($copy)) {
?>
<div class="background-cover-image background-red mobile-margin-bottom-small" style="background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/support-2022-texture.svg'; ?>);">
  <div class="container">
    <div class="flex-grid-row padding-top-mid padding-bottom-mid">
      <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6">
        <a href="<?php echo site_url('support/'); ?>">
          <h2 class="font-size-4 margin-bottom-tiny font-color-white js-fix-widows"><?php echo $headline; ?></h2>
        </a>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-m-10 flex-item-xxl-6">
        <a href="<?php echo site_url('support/'); ?>">
          <h3 class="font-size-2 font-color-white margin-top-tiny margin-bottom-small"><?php echo $copy; ?></h3>
        </a>
        <a href="<?php echo site_url('support/'); ?>" class="nm-button nm-button--white nm-button--inline">Support Us</a>
      </div>
    </div>
  </div>
</div>
<?php } ?>
