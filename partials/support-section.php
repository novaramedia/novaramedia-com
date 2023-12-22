<?php
  $override_text = isset($args['override_text']) ? $args['override_text'] : false;
  $heading_copy = !empty($args['heading_copy']) ? $args['heading_copy'] : NM_get_option('nm_fundraising_settings_support_section_title', 'nm_fundraising_options');
  $support_section_text = NM_get_option('nm_fundraising_settings_support_section_text', 'nm_fundraising_options');
  $support_section_autovalues = nm_get_support_autovalues();

  $instance = uniqid('support-form-');
?>

<div id="<?php echo $instance; ?>" class="support-section background-red font-color-white pt-6 pb-6">
  <div class="container">

    <form
      class="support-form"
      action="https://donate.novaramedia.com/regular"
    >
      <input class="support-form__value-input" type="hidden" value="<?php echo $support_section_autovalues['default']->regular_low; ?>" name="amount" />

      <div class="grid-row margin-bottom-small">
        <div class="grid-item is-m-24">
          <a href="<?php echo home_url('support/'); ?>">
            <h4 class="fs-6"><?php echo $heading_copy; ?></h4>
          </a>
        </div>
      </div>

      <div class="grid-row fs-5-sans fs-s-4-sans">
        <div class="grid-item is-m-24 is-l-16 is-xl-12 is-xxl-10">
          <?php
            if ($support_section_text || $override_text) {
            ?>
          <div class="margin-top-micro margin-bottom-small">
            <a href="<?php echo home_url('support/'); ?>" class="js-fix-widows"><?php
              if ($override_text) {
                echo $override_text;
              } else {
                echo $support_section_text;
              }
            ?></a>
          </div>
            <?php
              }
            ?>
        </div>

        <div class="support-form__inputs grid-item is-l-24 offset-xl-0 is-xxl-12 offset-xxl-2 fs-s-3-sans">
          <div class="grid-row grid--nested-tight margin-bottom-tiny">
            <div class="grid-item grid-item--tight is-xxl-4">
              <button class="support-form__button support-form__value-option" data-action="set-value" data-value="<?php echo $support_section_autovalues['default']->regular_low ?>" data-name="low"
              >£<?php echo $support_section_autovalues['default']->regular_low ?></button>
            </div>
            <div class="grid-item grid-item--tight is-xxl-4">
              <button class="support-form__button support-form__value-option" data-action="set-value" data-value="<?php echo $support_section_autovalues['default']->regular_medium ?>" data-name="medium"
              >£<?php echo $support_section_autovalues['default']->regular_medium ?></button>
            </div>
            <div class="grid-item grid-item--tight is-xxl-4">
              <button class="support-form__button support-form__value-option" data-action="set-value" data-value="<?php echo $support_section_autovalues['default']->regular_high ?>" data-name="high"
              >£<?php echo $support_section_autovalues['default']->regular_high ?></button>
            </div>
            <div class="grid-item grid-item--tight is-xxl-12">
              <label for="<?php echo $instance; ?>__custom-input" class="u-visuallyhidden">Custom donation amount in pounds</label>
              <input id="<?php echo $instance; ?>__custom-input" class="support-form__custom-input" type="number" placeholder="£ Custom amount" />
            </div>
          </div>
          <div class="grid-row grid--nested-tight">
            <div class="grid-item grid-item--tight is-xxl-6">
              <button class="support-form__button support-form__schedule-option" data-action="set-type" data-value="oneoff">One-off</button>
            </div>
            <div class="grid-item grid-item--tight is-xxl-6">
              <button class="support-form__button support-form__button--active support-form__schedule-option" data-action="set-type" data-value="regular">Monthly</button>
            </div>
            <div class="grid-item grid-item--tight is-xxl-12">
              <input class="support-form__submit nm-button nm-button--red-dark" type="submit" value="Go" />
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
