<?php
  $youtube_id = NM_get_option('nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options');
  $copy = NM_get_option('nm_fundraising_settings_video_banner_cta_text', 'nm_fundraising_options');

  if (!empty($youtube_id) && !empty($copy)) {
?>
<div class="background-red background-support-texture">
  <div class="container padding-top-basic padding-bottom-basic font-color-white">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <a href="<?php echo home_url('support/'); ?>"><h4>Support Us</h4></a>
      </div>
    </div>
    <div class="flex-grid-row">
      <div class="flex-grid-item flex-item-s-12 flex-offset-m-1 flex-item-m-10 flex-item-xl-6 flex-item-xxl-7 mobile-margin-bottom-small">
        <div class="js-lazy-loaded-youtube-embed background-red" data-src="<?php echo generate_youtube_embed_url($youtube_id); ?>"></div>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-offset-m-1 flex-item-m-10 flex-item-xl-6 flex-item-xxl-4">
        <a href="<?php echo site_url('support/'); ?>">
          <?php
            foreach($copy as $key => $paragraph) {
              if ($key === 0) {
            ?>
            <h3 class="font-size-3 margin-bottom-tiny js-fix-widows"><?php echo $paragraph; ?></h3>
            <?php
              } else {
            ?>
            <p class="font-size-2"><?php echo $paragraph; ?></p>
            <?php
              }
          ?>
          <?php
            }
          ?>
        </a>
        <a href="<?php echo site_url('support/'); ?>" class="nm-button nm-button--white nm-button--inline">Fund something better</a>
      </div>
    </div>
  </div>
</div>
<?php } ?>
