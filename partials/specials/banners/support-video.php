<?php
  // Get options and values
  $support_section_text = NM_get_option('nm_fundraising_settings_support_section_text', 'nm_fundraising_options');
  $heading_copy = !empty($args['heading_copy']) ? $args['heading_copy'] : NM_get_option('nm_fundraising_settings_support_section_title', 'nm_fundraising_options');
  $support_section_autovalues = nm_get_support_autovalues();
  $youtube_id = NM_get_option('nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options');
  $cutomText = NM_get_option('nm_fundraising_settings_video_banner_cta_text', 'nm_fundraising_options');
  $customHeadline = NM_get_option('nm_fundraising_settings_video_banner_cta_headline', 'nm_fundraising_options');
  $instance = uniqid('support-form-');

  // Check if YouTube ID exists
  if (!empty($youtube_id)) {
?>
    <div id="<?php echo $instance; ?>" class="support-section background-red font-color-white pt-2 pb-2 pt-s-2 pb-s-2 pl-0 pt-s-5 pb-s-5 ">
      <div class="container">
        <form class="support-form" action="https://donate.novaramedia.com/regular">
          <input class="support-form__value-input" type="hidden" value="<?php echo $support_section_autovalues['default']->regular_low; ?>" name="amount" />
          
          <!-- Grid row for video and form -->
          <div class="grid-row font-size-11 font-size-s-10">
            
            <!-- Video section -->
            <div class="grid-item is-m-24 is-l-16 is-xl-12 is-xxl-10">
              <div class="u-video-embed-container background-red">
                <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url($youtube_id); ?>" frameborder="0" allowfullscreen></iframe>
              </div>
            </div>
            
            <!-- Title and form section -->
            <div class="support-form__inputs grid-item is-l-24 offset-xl-0 is-xxl-12">
              
              <!-- Copy and heading -->
              <a href="<?php echo site_url('support/'); ?>">
                <?php
                  if ($heading_copy || $customHeadline) {
                ?>
                  <div class="margin-top-micro margin-bottom-small">
                    <h3 class="font-size-13 font-weight-bold mb-4 mt-4 js-fix-widows">
                      <?php 
                        if ($customHeadline) {
                          echo $customHeadline;
                        } else {
                          echo $heading_copy;
                        }
                      ?>
                    </h3>
                  </div>
                </a>
                <?php
                  }
                ?>
                
                <!-- Support section text or custom message -->
                <?php
                  if ($support_section_text || $cutomText) {
                ?>
                  <div class="margin-top-micro margin-bottom-small">
                    <p class="font-size-11 mb-4">
                      <?php 
                        if ($cutomText) {
                          echo $cutomText;
                        } else {
                          echo $support_section_text;
                        }
                      ?>
                    </p>
                  </div>
                <?php
                  }
                ?>
                
                <!-- Donation amount options -->
                <div class="grid-row grid--nested-tight margin-bottom-tiny">
                  <div class="grid-item grid-item--tight is-xxl-4">
                    <button class="support-form__button support-form__value-option ui-input" data-action="set-value" data-value="<?php echo $support_section_autovalues['default']->regular_low ?>" data-name="low">
                      £<?php echo $support_section_autovalues['default']->regular_low ?>
                    </button>
                  </div>
                  <div class="grid-item grid-item--tight is-xxl-4">
                    <button class="support-form__button support-form__value-option ui-input" data-action="set-value" data-value="<?php echo $support_section_autovalues['default']->regular_medium ?>" data-name="medium">
                      £<?php echo $support_section_autovalues['default']->regular_medium ?>
                    </button>
                  </div>
                  <div class="grid-item grid-item--tight is-xxl-4">
                    <button class="support-form__button support-form__value-option ui-input" data-action="set-value" data-value="<?php echo $support_section_autovalues['default']->regular_high ?>" data-name="high">
                      £<?php echo $support_section_autovalues['default']->regular_high ?>
                    </button>
                  </div>
                  <div class="grid-item grid-item--tight is-xxl-12">
                    <label for="<?php echo $instance; ?>__custom-input" class="u-visuallyhidden">Custom donation amount in pounds</label>
                    <input id="<?php echo $instance; ?>__custom-input" class="support-form__custom-input ui-input" type="number" min="1" placeholder="£ Custom amount" />
                  </div>
                </div>

                <!-- Donation type options -->
                <div class="grid-row grid--nested-tight">
                  <div class="grid-item grid-item--tight is-xxl-6">
                    <button class="support-form__button support-form__schedule-option ui-input" data-action="set-type" data-value="oneoff">One-off</button>
                  </div>
                  <div class="grid-item grid-item--tight is-xxl-6">
                    <button class="support-form__button support-form__button--active support-form__schedule-option ui-input" data-action="set-type" data-value="regular">Monthly</button>
                  </div>
                  <div class="grid-item grid-item--tight is-xxl-12">
                    <input class="support-form__submit ui-button ui-button--white ui-button--fill-width" type="submit" value="Go" />
                  </div>
                </div>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
<?php } ?>
