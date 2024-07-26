<?php
// TODO: Refactor this to use the new grid system and type scale

  $open_copy = NM_get_option('nm_fundraising_settings_support_bar_open_paragraph', 'nm_fundraising_options');
  $open_cta = NM_get_option('nm_fundraising_settings_support_bar_open_cta', 'nm_fundraising_options');
  $open_button = NM_get_option('nm_fundraising_settings_support_bar_open_button', 'nm_fundraising_options');

  $desktop_closed_copy = NM_get_option('nm_fundraising_settings_support_bar_desktop_closed_copy', 'nm_fundraising_options');
  $desktop_closed_cta = NM_get_option('nm_fundraising_settings_support_bar_desktop_closed_cta', 'nm_fundraising_options');
  $mobile_closed_cta = NM_get_option('nm_fundraising_settings_support_bar_mobile_closed_cta', 'nm_fundraising_options');
?>
<div class="support-bar support-bar--closed background-red background-support-texture">
  <div class="container pt-4 pb-4">
    <div class="support-bar__open-view grid-row pb-2">
      <div class="grid-item is-xxl-6 only-desktop">
        <a href="<?php echo site_url('support/'); ?>">
          <h3 class="font-color-white fs-l-6 font-size-15 font-weight-bold" style="line-height: .95;">Build<br/>&nbsp;people-<br/>&nbsp;&nbsp;powered<br/>&nbsp;&nbsp;&nbsp;media.</h3>
        </a>
      </div>
      <div class="grid-item is-xxl-20 only-mobile">
        <a href="<?php echo site_url('support/'); ?>">
          <h3 class="font-color-white font-size-10 font-weight-bold mb-2">Build people-powered media.</h3>
        </a>
      </div>
      <div class="grid-item is-s-24 flex-item-xxl-6 font-color-white font-size-11 only-desktop">
        <div class="ml-m-4">
          <p class="mb-2 font-weight-regular"><?php echo ($open_copy ? $open_copy : 'We’re up against obscene wealth and influence in the media. Our supporters keep us entirely free to access. We don’t have any ad partnerships or sponsored content.'); ?></p>
          <strong><?php echo ($open_cta ? $open_cta : 'If you can, donate one hour’s wage per month or whatever you can afford today.'); ?></strong>
        </div>
      </div>
      <div class="support-bar__open-actions-column grid-item is-s-4 is-xxl-6">
        <div class="support-bar__open-actions-wrapper">
          <nav class="support-bar__close-trigger ui-hit-area ui-hit-area--top-right">
            <span class="ui-chevron ui-chevron--down ui-chevron--white font-color-white"></span>
          </nav>
          <a href="<?php echo site_url('support/'); ?>" class="ui-button ui-button--white ui-button--small only-desktop">
            <?php echo ($open_button ? $open_button : 'Join our supporters'); ?>
          </a>
        </div>
      </div>
      <div class="grid-item is-s-24 font-color-white only-mobile">
        <p class="mb-2"><?php echo ($open_copy ? $open_copy : 'We’re up against obscene wealth and influence in the media. Our supporters keep us entirely free to access. We don’t have any ad partnerships or sponsored content.'); ?></p>
        <strong><?php echo ($open_cta ? $open_cta : 'If you can, donate one hour’s wage per month or whatever you can afford today.'); ?></strong>
        <div class="mt-3">
          <a href="<?php echo site_url('support/'); ?>" class="ui-button ui-button--white ui-button--small">
            <?php echo ($open_button ? $open_button : 'Join our supporters'); ?>
          </a>
        </div>
      </div>
    </div>
    <div class="support-bar__closed-view grid-row is-one-even-line-grid font-color-white">
      <div class="grid-item">
        <span class="only-desktop">
          <a href="<?php echo site_url('support/'); ?>">
            <strong>Build people-powered media.</strong> <?php echo ($desktop_closed_copy ? $desktop_closed_copy : 'We’re up against obscene wealth and influence in the media.'); ?>
          </a>
        </span>
        <a href="<?php echo site_url('support/'); ?>" class="font-size-10 font-weight-bold only-mobile">
          <?php echo ($mobile_closed_cta ? $mobile_closed_cta : 'Fund independent, truthful journalism'); ?>
        </a>
      </div>
      <div class="grid-item">
        <a href="<?php echo site_url('support/'); ?>" class="ui-action-link only-desktop">
          <?php echo ($desktop_closed_cta ? $desktop_closed_cta : 'Fund something better'); ?>
        </a>
        <span class="support-bar__open-trigger ux-pointer pl-3">
          <span class="ui-chevron ui-chevron--up ui-chevron--white"></span>
        </span>
      </div>
    </div>
  </div>
</div>
