<?php
  $youtube_id = NM_get_option('nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options');
  $copy = NM_get_option('nm_fundraising_settings_video_banner_cta_text', 'nm_fundraising_options');

  if (!empty($youtube_id) && !empty($copy)) {
?>
<div class="background-red background-support-texture">
  <div class="container pt-5 pb-5 font-color-white">
    <div class="flex-grid-row mb-3">
      <div class="flex-grid-item flex-item-s-12 fs-3-sans font-uppercase font-weight-bold">
        <a href="<?php echo home_url('support/'); ?>"><h4>Support Us</h4></a>
      </div>
    </div>
    <div class="flex-grid-row">
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-m-1 flex-item-m-10 flex-item-xl-6 flex-item-xxl-7 mb-s-3">
        <div class="u-video-embed-container background-red">
          <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url($youtube_id); ?>" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-m-1 flex-item-m-10 flex-item-xl-6 flex-item-xxl-4">
        <a href="<?php echo site_url('support/'); ?>">
          <?php
            foreach($copy as $key => $paragraph) {
              if ($key === 0) {
            ?>
            <h3 class="fs-7 mb-3 js-fix-widows"><?php echo $paragraph; ?></h3>
            <?php
              } else {
            ?>
            <p class="fs-5-sans mb-3"><?php echo $paragraph; ?></p>
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
