<?php
// Get options and values
$support_section_text = NM_get_option('nm_fundraising_settings_support_section_text', 'nm_fundraising_options');
$heading_copy = !empty($args['heading_copy']) ? $args['heading_copy'] : NM_get_option('nm_fundraising_settings_support_section_title', 'nm_fundraising_options');
$customYoutube_id = NM_get_option('nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options');
$cutomText = NM_get_option('nm_fundraising_settings_video_banner_cta_text', 'nm_fundraising_options');
$customHeadline = NM_get_option('nm_fundraising_settings_video_banner_cta_headline', 'nm_fundraising_options');
$youtube_id = get_post_meta($post_id, '_nm_support_youtube', true);

?>
<div id="<?php echo $instance; ?>" class="support-section background-red font-color-white pt-2 pb-2 pt-s-2 pb-s-2 pl-0 pt-s-5 pb-s-5 ">
  <div class="container">


    <!-- Grid row for video and form -->
    <div class="grid-row font-size-11 font-size-s-10">

      <!-- Video section -->
      <div class="grid-item is-m-24 is-l-16 is-xl-12 is-xxl-10">
        <div class="u-video-embed-container background-red">
          <iframe class="youtube-player lazyload" data-src="<?php
                                                            if ($customYoutube_id) {
                                                              echo generate_youtube_embed_url($customYoutube_id);
                                                            } else if ($youtube_id) {
                                                              echo generate_youtube_embed_url($youtube_id);
                                                            } else {
                                                              echo generate_youtube_embed_url("c6hfjBmzt5c");
                                                            }
                                                            ?>" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>

      <!-- Title and form section -->
      <div class="support-form__inputs grid-item is-l-24 offset-xl-0 is-xxl-12">

        <!-- Copy and heading -->
        <a href="<?php echo site_url('support/'); ?>">
          <div class="margin-top-micro margin-bottom-small">
            <h3 class="font-size-13 font-weight-bold mb-4 mt-4 js-fix-widows">
              <?php
              if ($customHeadline) {
                echo $customHeadline;
              } else if ($heading_copy) {
                echo $heading_copy;
              } else {
                echo "Support Us";
              }
              ?>
            </h3>
          </div>

          <!-- Support section text or custom message -->
          <div class="margin-top-micro margin-bottom-small">
            <p class="font-size-11 mb-4">
              <?php
              if ($cutomText) {
                echo $cutomText;
              } else if ($support_section_text) {
                echo $support_section_text;
              } else {
                echo "Fund truthful, independent journalism. Donate one hour’s wage per month or whatever you can afford today.";
              }
              ?>
            </p>
          </div>
        </a>
        <!-- Reneders the support form -->
        <?php render_support_form(); ?>
      </div>
    </div>
  </div>
</div>
</div>