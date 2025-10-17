<?php

  // TODO
  // - [ ] Generate the form input elements via a function and use in the email-signup.php partial also
  // - [ ] Migrate font sizes to new system

get_header();
?>
<main id="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );

    $mailchimp_key = ! empty( $meta['_nm_mailchimp_key'] ) ? $meta['_nm_mailchimp_key'][0] : false;
    $title_size = ! empty( $meta['_nm_title_size'] ) ? $meta['_nm_title_size'][0] : false;
    $youtube_id = ! empty( $meta['_nm_youtube_id'] ) ? $meta['_nm_youtube_id'][0] : false;
    $support_override = ! empty( $meta['_nm_support_text'] ) ? $meta['_nm_support_text'][0] : false;

    $settings_title_classes = array(
      'huge'    => 'font-size-s-7 font-size-m-6 font-size-l-7 font-size-8',
      'big'     => 'font-size-s-7 font-size-m-5 font-size-xl-6 font-size-7',
      'medium'  => 'font-size-s-7 font-size-6',
      'smaller' => 'font-size-s-6 font-size-5',
    );
    ?>
  <article id="page">
    <div class="background-black font-color-white">
      <div class="container pt-4 pb-6">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <a href="<?php echo get_post_type_archive_link( 'newsletter' ); ?>">
              <h4 class="fs-3-sans font-uppercase font-bold">Newsletter</h4>
            </a>
            <h1 class="
            <?php
              echo $title_size ? $settings_title_classes[ $title_size ] : $settings_title_classes['medium'];
            ?>
            mt-4"><?php the_title(); ?></h1>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="grid-row mt-4 mb-4">
        <div class="grid-item is-m-24 is-xxl-12">
          <?php
          if ( $youtube_id ) {
            ?>
          <div class="u-video-embed-container mb-4">
            <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url( $youtube_id, true ); ?>" allow="autoplay" allowfullscreen></iframe>
          </div>
            <?php
          } else {
            the_post_thumbnail( 'col8' );
          }
          ?>
        </div>
        <div class="grid-item offset-s-0 is-s-24 offset-m-1 is-m-20 is-xxl-12">
          <div class="font-size-3 font-semibold mb-4">
            <?php the_content(); ?>
          </div>
          <?php
          if ( $mailchimp_key ) {
            render_mailchimp_signup_form( $mailchimp_key, 'white', 'black' );
          }
          ?>
        </div>
      </div>
    </div>
  </article>
    <?php
  }
}

if ( $support_override ) {
  get_template_part(
        'partials/support-section',
        null,
        array(
    'override_text' => $support_override,
  )
        );
} else {
  get_template_part( 'partials/support-section' );
}
?>
</main>
<?php
get_footer();
?>
