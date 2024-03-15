<?php
  $youtube_id = NM_get_option('nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options');
  $copy = NM_get_option('nm_fundraising_settings_video_banner_cta_text', 'nm_fundraising_options');

  if (!empty($youtube_id) && !empty($copy)) {
?>
<div class="background-red background-support-texture font-color-white">
  <div class="container pt-6 pb-6">
    <div class="grid-row mb-3">
      <div class="grid-item is-xxl-24 fs-3-sans font-uppercase font-weight-bold">
        <a href="<?php echo home_url('support/'); ?>"><h4>Support Us</h4></a>
      </div>
    </div>
    <div class="grid-row">
      <div class="grid-item offset-s-0 is-s-24 offset-m-2 is-m-20 is-xl-12 is-xxl-14 mb-s-3">
        <div class="u-video-embed-container background-red">
          <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url($youtube_id); ?>" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
      <div class="grid-item offset-s-0 is-s-24 offset-m-2 is-m-20 is-xl-12 flex-item-xxl-4">
        <a href="<?php echo site_url('support/'); ?>">
          <?php
            foreach($copy as $key => $paragraph) {
              if ($key === 0) {
            ?>
            <h3 class="fs-7 mb-4 js-fix-widows"><?php echo $paragraph; ?></h3>
            <?php
              } else {
            ?>
            <p class="fs-5-sans mb-4"><?php echo $paragraph; ?></p>
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
