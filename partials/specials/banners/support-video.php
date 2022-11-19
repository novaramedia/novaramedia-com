<?php
  $youtube_id = NM_get_option('nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options');
  $copy = NM_get_option('nm_fundraising_settings_video_banner_cta_text', 'nm_fundraising_options');

  if (!empty($youtube_id) && !empty($copy)) {
?>
<div class="background-cover-image background-red" style="background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/support-2022-texture.svg'; ?>);">
  <div class="container padding-top-basic padding-bottom-basic">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12 font-color-white">
        <a href="<?php echo home_url('support/'); ?>"><h4>Support Us</h4></a>
      </div>
    </div>
    <div class="flex-grid-row">
      <div class="flex-grid-item flex-item-s-12 flex-offset-m-1 flex-item-m-10 flex-item-xl-6 flex-item-xxl-7 mobile-margin-bottom-small">
        <div class="js-lazy-loaded-youtube-embed background-red" data-src="<?php echo generate_youtube_embed_url($youtube_id); ?>"></div>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-offset-m-1 flex-item-m-10 flex-item-xl-6 flex-item-xxl-5">
        <a href="<?php echo site_url('support/'); ?>">
          <?php
            foreach($copy as $paragraph) {
          ?><h3 class="font-size-2 font-color-white margin-top-tiny margin-bottom-small"><?php echo $paragraph; ?></h3>
          <?php
            }
          ?>
        </a>
        <a href="<?php echo site_url('support/'); ?>" class="nm-button nm-button--white nm-button--inline">Support Us</a>
      </div>
    </div>
  </div>
</div>
<?php } ?>
