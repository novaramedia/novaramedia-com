<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/* Template Name: Newsletter */
get_header();
?>
<main id="main-content">
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);

    $mailchimp_key = !empty($meta['_nm_mailchimp_key']) ? $meta['_nm_mailchimp_key'][0] : false;
    $title_size = !empty($meta['_nm_title_size']) ? $meta['_nm_title_size'][0] : false;
    $youtube_id = !empty($meta['_nm_youtube_id']) ? $meta['_nm_youtube_id'][0] : false;
    $support_override = !empty($meta['_nm_support_text']) ? $meta['_nm_support_text'][0] : false;

    $settings_title_classes = array(
      'huge' => 'font-size-s-15 font-size-m-16 font-size-17',
      'big' => 'font-size-s-14 font-size-m-15 font-size-16',
      'medium' => 'font-size-s-13 font-size-15',
      'smaller' => 'font-size-s-12 font-size-13'
    );
?>
  <article id="page">
    <div class="background-black font-color-white">
      <div class="container pt-4 pb-6 pb-m-4">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <h4 class="font-size-9 text-uppercase font-weight-bold">Newsletter</h4>
            <h1 class="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
              echo $title_size ? $settings_title_classes[$title_size] : $settings_title_classes['medium'];
            ?> font-weight-bold mt-4"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_title(); ?></h1>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="grid-row mt-4 mb-4">
        <div class="grid-item is-m-24 is-xxl-12">
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            if ($youtube_id) {
          ?>
          <div class="u-video-embed-container mb-4">
            <iframe class="youtube-player" type="text/html" src="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo generate_youtube_embed_url($youtube_id, true); ?>" allow="autoplay" allowfullscreen></iframe>
          </div>
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            } else {
              the_post_thumbnail('col8');
            }
          ?>
        </div>
        <div class="grid-item offset-s-0 is-s-24 offset-m-1 is-m-20 is-xxl-12">
          <div class="font-size-12 font-weight-semibold mb-4">
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_content(); ?>
          </div>
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            //======================================================================
            // SIGNUP FORM
            //======================================================================

            if ($mailchimp_key) {

            $netlify = 'https://novara-media-mailchimp-signup.netlify.app/.netlify/functions/mailchimp-signup';

            if ($_SERVER['HTTP_HOST'] === 'localhost:8888') { // for local dev
              $netlify = 'http://localhost:65208/.netlify/functions/mailchimp-signup';
            }
          ?>
          <form class="email-signup__form newsletter-page-email-signup__form" action="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $netlify; ?>" method="post" target="_blank">
            <input type="hidden" name="newsletter" value="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $mailchimp_key; ?>" />

            <div class="newsletter-page-email-signup__inputs">
              <div class="form-group mb-2">
                <label class="u-visuallyhidden" for="firstName">First name:</label>
                <input name="firstName" class="newsletter-page-email-signup__name-input ui-input ui-input--border-gray" id="firstName" type="text" autocomplete="given-name" placeholder="First name" />
              </div>
              <div class="form-group mb-2">
                <label class="u-visuallyhidden" for="email">Email:</label>
                <input name="email" class="newsletter-page-email-signup__email-input ui-input ui-input--border-gray" id="email" type="email" autocomplete="email" placeholder="Email" required />
              </div>
              <div class="newsletter-page-email-signup__email-gdpr-group form-group layout-flex-align-center mb-2">
                <label for="newsletter-gdpr" class="font-size-8 font-weight-bold">I agree to the <a target="_blank" rel="noopener" href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo site_url('privacy-policy/'); ?>">Privacy Policy</a></label>
                <input name="gdpr" class="newsletter-page-email-signup__email-gdpr-input ui-checkbox ui-checkbox--border-gray ml-2" id="newsletter-gdpr" type="checkbox" value="accepted" required/>
              </div>

              <input class="newsletter-page-email-signup__submit ui-button ui-button--black" type="submit" value="Sign up">
            </div>

            <div class="email-signup__feedback-processing">
              <div class="u-flex-center">
                <div class="spinner spinner--white">
                  <div class="double-bounce1"></div>
                  <div class="double-bounce2"></div>
                </div>
              </div>
            </div>

            <div class="email-signup__feedback-failed font-weight-bold text-align-center">
              <div class="u-flex-center">
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" x="0" y="0" version="1.1" viewBox="0 0 51 51" class="email-signup__icon mb-2 u-pointer" >
                    <path d="M25.5 51C11.4 51 0 39.6 0 25.5S11.4 0 25.5 0 51 11.4 51 25.5 39.6 51 25.5 51zm0-50C12 1 1 12 1 25.5S12 50 25.5 50 50 39 50 25.5 39 1 25.5 1z"/>
                    <path d="M36.9 14.4c-.2-.2-.5-.2-.7 0L25.5 25 14.9 14.4c-.2-.2-.5-.2-.7 0s-.2.5 0 .7l10.6 10.6-9.9 9.9c-.2.2-.2.5 0 .7.1.1.2.1.4.1s.3 0 .4-.1l9.9-9.9 9.9 9.9c.1.1.2.1.4.1s.3 0 .4-.1c.2-.2.2-.5 0-.7l-9.9-9.9L37 15.1c0-.2 0-.6-.1-.7z"/>
                  </svg>
                  <br />
                  Sign up error: <span class="email-signup__feedback-message"></span>. Try again later.
                </div>
              </div>
            </div>
            <div class="email-signup__feedback-completed font-weight-bold text-align-center">
              <div class="u-flex-center">
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" x="0" y="0" version="1.1" viewBox="0 0 52 52" class="email-signup__icon mb-2">
                    <path d="M38.4 16.2c-.3 0-.6.1-.8.3L20.9 33.2 14 26.3c-.1-.1-.2-.2-.4-.2-.1-.1-.2-.1-.3-.1-.1 0-.3 0-.4.1-.1.1-.3.1-.4.2-.1.1-.2.2-.2.4-.1.1-.1.3-.1.4 0 .1 0 .3.1.4.1.1.1.3.2.4l7.6 7.7c.2.2.5.3.8.3s.6-.1.8-.3L39.2 18c.2-.2.3-.4.3-.6 0-.2 0-.4-.1-.6-.1-.2-.2-.4-.4-.5-.2-.1-.4-.1-.6-.1z"/>
                    <path d="M26 51.5C11.9 51.5.5 40.1.5 26S11.9.5 26 .5 51.5 11.9 51.5 26 40.1 51.5 26 51.5zm0-50C12.5 1.5 1.5 12.5 1.5 26s11 24.5 24.5 24.5 24.5-11 24.5-24.5S39.5 1.5 26 1.5z" />
                  </svg>
                  <br />
                  Thanks for signing up.
                </div>
              </div>
            </div>
          </form>
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            } // end conditional for set Mailchimp newsletter key
          ?>
        </div>
      </div>
    </div>
  </article>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  }
}

if ( $support_override ) {
  get_template_part(
    'partials/support-section',
    null,
    array(
      'container_classes' => 'mb-4',
      'override_text' => $support_override,
    )
  );
} else {
  get_template_part( 'partials/support-section', null, array( 'container_classes' => 'mb-4' ) );
}
?>
</main>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_footer();
?>
