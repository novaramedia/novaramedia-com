<?php
// Get options and values
$default_support_section_text = NM_get_option('nm_fundraising_settings_support_section_text', 'nm_fundraising_options');
$default_support_section_heading = !empty($args['heading_copy']) ? $args['heading_copy'] : NM_get_option('nm_fundraising_settings_support_section_title', 'nm_fundraising_options');
$customYoutube_id = NM_get_option('nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options');
$custom_text = NM_get_option('nm_fundraising_settings_video_banner_cta_custom_text', 'nm_fundraising_options');
$custom_headline = NM_get_option('nm_fundraising_settings_video_banner_cta_headline', 'nm_fundraising_options');
$youtube_id = get_post_meta($post_id, '_nm_support_youtube', true);

?>
<div class="background-red font-color-white pt-6 pb-6 pt-s-5 pb-s-5">
  <div class="container">
    <div class="grid-row font-size-11 font-size-s-10">
      <?php //Video embed section with default and hard coded fall backs
      ?>
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
      <div class="support-form__inputs  grid-item is-l-24 offset-xl-0 is-xxl-12 offset-xxl-2">
        <?php
        // custom copy and heading with default and hard coded fall backs
        ?>
        <a href="<?php echo site_url('support/'); ?>">
          <div class="margin-top-micro margin-bottom-small">
            <h3 class="font-size-13 font-weight-bold mb-4 mt-4 js-fix-widows">
              <?php
              if ($custom_headline) {
                echo $custom_headline;
              } else if ($default_support_section_heading) {
                echo $default_support_section_heading;
              } else {
                echo "Support Us";
              }
              ?>
            </h3>
          </div>
          <div class="margin-top-micro margin-bottom-small">
            <p class="font-size-11 mb-4">
              <?php
              if ($custom_text) {
                echo $custom_text;
              } else if ($default_support_section_text) {
                echo $default_support_section_text;
              } else {
                echo "Fund truthful, independent journalism. Donate one hour’s wage per month or whatever you can afford today.";
              }
              ?>
            </p>
          </div>
        </a>
        <?php // Reneders the support form from renderers.php
        render_support_form(); ?>
      </div>
    </div>
  </div>
</div>
</div>