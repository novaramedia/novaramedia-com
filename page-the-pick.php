<?php
get_header();
?>
<main id="main-content">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);

    $youtube_id = !empty($meta['_nm_youtube_id']) ? $meta['_nm_youtube_id'][0] : false;
    $support_override = !empty($meta['_nm_support_text']) ? $meta['_nm_support_text'][0] : false;
?>
  <article id="page">
    <div class="background-black font-color-white">
      <div class="container padding-top-small padding-bottom-large">
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-xxl-12">
            <h4>Newsletter</h4>
            <h1 class="font-size-s-7 font-size-m-6 font-size-l-7 font-size-8 margin-top-basic">The Pick.</h1>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="flex-grid-row margin-top-basic margin-bottom-basic">
        <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6">
          <div class="u-video-embed-container margin-bottom-small">
            <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($youtube_id, true); ?>" allow="autoplay" allowfullscreen></iframe>
          </div>
        </div>
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-m-1 flex-item-m-10 flex-item-xxl-6">
          <div class="font-size-3 font-semibold margin-bottom-small">
            <?php the_content(); ?>
          </div>
          <?php
            //======================================================================
            // SIGNUP FORM
            //======================================================================

            $netlify = 'https://novara-media-mailchimp-signup.netlify.app/.netlify/functions/mailchimp-signup';

            if ($_SERVER['HTTP_HOST'] === 'localhost:8888') { // for local dev
              $netlify = 'http://localhost:60573/.netlify/functions/mailchimp-signup';
            }
          ?>
          <form class="email-signup__form newsletter-page-email-signup__form" action="<?php echo $netlify; ?>" method="post" target="_blank">
            <input type="hidden" name="newsletter" value="The Pick" />

            <div class="newsletter-page-email-signup__inputs flex-grid-row flex-grid--nested">
              <div class="flex-grid-item flex-grid-item--tight flex-item-l-12 flex-item-xxl-4 margin-bottom-tiny">
                <div class="form-group">
                  <label class="u-visuallyhidden" for="firstName">First name:</label>
                  <input name="firstName" class="newsletter-page-email-signup__name-input" id="firstName" type="text" autocomplete="given-name" placeholder="First name" />
                </div>
              </div>
              <div class="flex-grid-item flex-grid-item--tight flex-item-l-12 flex-item-xxl-8 margin-bottom-tiny">
                <div class="form-group">
                  <label class="u-visuallyhidden" for="email">Email:</label>
                  <input name="email" class="newsletter-page-email-signup__email-input" id="email" type="email" autocomplete="email" placeholder="Email" required />
                </div>
              </div>
              <div class="flex-grid-item flex-grid-item--tight flex-item-l-6 flex-item-xxl-4 margin-bottom-small">
                <div class="newsletter-page-email-signup__email-gdpr-group form-group">
                  <label for="newsletter-gdpr">I agree to the<br /><a target="_blank" rel="noopener" href="<?php echo site_url('privacy-policy/'); ?>">Privacy Policy</a></label>
                  <input name="gdpr" class="newsletter-page-email-signup__email-gdpr-input" id="newsletter-gdpr" type="checkbox" value="accepted" required/>
                </div>
              </div>
              <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6 margin-bottom-small">
                <input class="newsletter-page-email-signup__submit nm-button nm-button--black" type="submit" value="Sign up">
              </div>
            </div>

            <div class="email-signup__feedback-processing">
              <div class="u-flex-center">
                <div class="spinner spinner--white">
                  <div class="double-bounce1"></div>
                  <div class="double-bounce2"></div>
                </div>
              </div>
            </div>

            <div class="email-signup__feedback-failed font-bold text-align-center">
              <div class="u-flex-center">
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" x="0" y="0" version="1.1" viewBox="0 0 51 51" class="email-signup__icon margin-bottom-tiny u-pointer" >
                    <path d="M25.5 51C11.4 51 0 39.6 0 25.5S11.4 0 25.5 0 51 11.4 51 25.5 39.6 51 25.5 51zm0-50C12 1 1 12 1 25.5S12 50 25.5 50 50 39 50 25.5 39 1 25.5 1z"/>
                    <path d="M36.9 14.4c-.2-.2-.5-.2-.7 0L25.5 25 14.9 14.4c-.2-.2-.5-.2-.7 0s-.2.5 0 .7l10.6 10.6-9.9 9.9c-.2.2-.2.5 0 .7.1.1.2.1.4.1s.3 0 .4-.1l9.9-9.9 9.9 9.9c.1.1.2.1.4.1s.3 0 .4-.1c.2-.2.2-.5 0-.7l-9.9-9.9L37 15.1c0-.2 0-.6-.1-.7z"/>
                  </svg>
                  <br />
                  Sign up error: <span class="email-signup__feedback-message"></span>. Try again later.
                </div>
              </div>
            </div>
            <div class="email-signup__feedback-completed font-bold text-align-center">
              <div class="u-flex-center">
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" x="0" y="0" version="1.1" viewBox="0 0 52 52" class="email-signup__icon margin-bottom-tiny">
                    <path d="M38.4 16.2c-.3 0-.6.1-.8.3L20.9 33.2 14 26.3c-.1-.1-.2-.2-.4-.2-.1-.1-.2-.1-.3-.1-.1 0-.3 0-.4.1-.1.1-.3.1-.4.2-.1.1-.2.2-.2.4-.1.1-.1.3-.1.4 0 .1 0 .3.1.4.1.1.1.3.2.4l7.6 7.7c.2.2.5.3.8.3s.6-.1.8-.3L39.2 18c.2-.2.3-.4.3-.6 0-.2 0-.4-.1-.6-.1-.2-.2-.4-.4-.5-.2-.1-.4-.1-.6-.1z"/>
                    <path d="M26 51.5C11.9 51.5.5 40.1.5 26S11.9.5 26 .5 51.5 11.9 51.5 26 40.1 51.5 26 51.5zm0-50C12.5 1.5 1.5 12.5 1.5 26s11 24.5 24.5 24.5 24.5-11 24.5-24.5S39.5 1.5 26 1.5z" />
                  </svg>
                  <br />
                  Thanks for signing up.
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </article>
<?php
  }
}
  get_template_part('partials/support-section', null, array(
    'override_text' => $support_override,
  ));
?>
</main>
<?php
get_footer();
?>
