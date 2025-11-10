<?php
/**
$default_support_section_text = NM_get_option( 'nm_fundraising_settings_support_section_text', 'nm_fundraising_options' );
$default_support_section_heading = ! empty( $args['heading_copy'] ) ? $args['heading_copy'] : NM_get_option( 'nm_fundraising_settings_support_section_title', 'nm_fundraising_options' );
$custom_text = NM_get_option( 'nm_fundraising_settings_video_banner_cta_custom_text', 'nm_fundraising_options' );
$custom_headline = NM_get_option( 'nm_fundraising_settings_video_banner_cta_headline', 'nm_fundraising_options' );
 */

$custom_youtube_id = NM_get_option( 'nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options' );

$support_page = get_page_by_path( 'support' );
$support_page_youtube_id = false;

if ( $support_page !== null ) {
  $support_page_youtube_id = get_post_meta( $support_page->ID, '_nm_support_youtube', true );
}
?>
<div class="container pt-6 pb-6 pt-s-5 pb-s-5">
  <div class="grid-row font-size-11 font-size-s-10">
    <div class="grid-item is-m-24 is-xxl-12 mb-4">
      <div class="u-video-embed-container background-black">
        <?php
        if ( ! empty( $custom_youtube_id ) ) {
          $video_id = $custom_youtube_id;
        } elseif ( ! empty( $support_page_youtube_id ) ) {
          $video_id = $support_page_youtube_id;
        } else {
          $video_id = 'c6hfjBmzt5c';
        }
        echo render_youtube_embed_iframe( $video_id, false, true );
        ?>
      </div>
    </div>
    <?php
      render_support_form( 'condensed', false, 'grid-item is-m-24 is-xxl-12' );
    ?>
  </div>
</div>
