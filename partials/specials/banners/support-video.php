<?php
$default_support_section_text = NM_get_option( 'nm_fundraising_settings_support_section_text', 'nm_fundraising_options' );
$default_support_section_heading = ! empty( $args['heading_copy'] ) ? $args['heading_copy'] : NM_get_option( 'nm_fundraising_settings_support_section_title', 'nm_fundraising_options' );
$custom_youtube_id = NM_get_option( 'nm_fundraising_settings_video_banner_cta_youtube_id', 'nm_fundraising_options' );
$custom_text = NM_get_option( 'nm_fundraising_settings_video_banner_cta_custom_text', 'nm_fundraising_options' );
$custom_headline = NM_get_option( 'nm_fundraising_settings_video_banner_cta_headline', 'nm_fundraising_options' );

$support_page = get_page_by_path( 'support' );
$suport_page_youtube_id = false;

if ( ! $support_page === null ) {
  $suport_page_youtube_id = get_post_meta( $support_page->ID, '_nm_support_youtube', true );
}

?>
<div class="container pt-6 pb-6 pt-s-5 pb-s-5">
  <div class="grid-row font-size-11 font-size-s-10">
      <?php
      // Video embed section with default and hard coded fall backs
      ?>
      <div class="grid-item is-m-24 is-xxl-12 mb-4">
        <div class="u-video-embed-container background-black">
          <iframe class="youtube-player lazyload" data-src="
          <?php
          if ( $custom_youtube_id ) {
            echo generate_youtube_embed_url( $custom_youtube_id );
          } elseif ( $support_page_youtube_id ) {
            echo generate_youtube_embed_url( $support_page_youtube_id );
          } else {
            echo generate_youtube_embed_url( 'c6hfjBmzt5c' );
          }
          ?>
      " allow="<?php echo get_youtube_iframe_allow_attr(); ?>" allowfullscreen></iframe>
        </div>
      </div>
      <?php
        render_support_form( 'condensed', false, 'grid-item is-m-24 is-xxl-12' );
      ?>
    </div>
  </div>
</div>
