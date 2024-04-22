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
    <div class="support-bar__open-view flex-grid-row pb-2">
      <div class="flex-grid-item flex-item-xxl-3 only-desktop">
        <a href="<?php echo site_url('support/'); ?>">
          <h3 class="font-color-white font-size-l-3 font-size-4" style="line-height: .95;">Build<br/>&nbsp;people-<br/>&nbsp;&nbsp;powered<br/>&nbsp;&nbsp;&nbsp;media.</h3>
        </a>
      </div>
      <div class="flex-grid-item flex-item-xxl-10 only-mobile">
        <a href="<?php echo site_url('support/'); ?>">
          <h3 class="font-color-white font-size-2 mb-2">Build people-powered media.</h3>
        </a>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6 font-color-white font-size-l-1 font-size-2 only-desktop">
        <p><?php echo ($open_copy ? $open_copy : 'We’re up against obscene wealth and influence in the media space. Our supporters keep us entirely free to access. We don’t have any ad partnerships or sponsored content.'); ?></p>
        <strong><?php echo ($open_cta ? $open_cta : 'If you can, donate one hour’s wage per month or whatever you can afford today.'); ?></strong>
      </div>
      <div class="support-bar__open-actions-column flex-grid-item flex-item-s-2 flex-item-xxl-3">
        <div class="support-bar__open-actions-wrapper">
          <nav class="support-bar__close-trigger ui-hit-area ui-hit-area--top-right">
            <span class="ui-chevron ui-chevron--down font-color-white"></span>
          </nav>
          <a href="<?php echo site_url('support/'); ?>" class="ui-button ui-button--white ui-button--small only-desktop">
            <?php echo ($open_button ? $open_button : 'Join our supporters'); ?>
          </a>
        </div>
      </div>
      <div class="flex-grid-item flex-item-s-12 font-color-white only-mobile">
        <p class="margin-bottom-micro"><?php echo ($open_copy ? $open_copy : 'We’re up against obscene wealth and influence in the media space. Our supporters keep us entirely free to access. We don’t have any ad partnerships or sponsored content.'); ?></p>
        <strong><?php echo ($open_cta ? $open_cta : 'If you can, donate one hour’s wage per month or whatever you can afford today.'); ?></strong>
        <div class="margin-top-tiny">
          <a href="<?php echo site_url('support/'); ?>" class="ui-button ui-button--white ui-button--small">
            <?php echo ($open_button ? $open_button : 'Join our supporters'); ?>
          </a>
        </div>
      </div>
    </div>
    <div class="support-bar__closed-view flex-grid-row is-one-even-line-grid font-color-white">
      <div class="flex-grid-item">
        <span class="only-desktop">
          <a href="<?php echo site_url('support/'); ?>">
            <strong>Build people-powered media.</strong> <?php echo ($desktop_closed_copy ? $desktop_closed_copy : 'We’re up against obscene wealth and influence in the media space.'); ?>
          </a>
        </span>
        <a href="<?php echo site_url('support/'); ?>" class="font-size-2 only-mobile">
          <?php echo ($mobile_closed_cta ? $mobile_closed_cta : 'Fund independent, truthful journalism'); ?>
        </a>
      </div>
      <div class="flex-grid-item">
        <a href="<?php echo site_url('support/'); ?>" class="ui-action-link only-desktop">
          <?php echo ($desktop_closed_cta ? $desktop_closed_cta : 'Fund something better'); ?>
        </a>
        <span class="support-bar__open-trigger ux-pointer pl-3">
          <span class="ui-chevron ui-chevron--up"></span>
        </span>
      </div>
    </div>
  </div>
</div>
