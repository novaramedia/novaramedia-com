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
            <h1 class="font-size-8 margin-top-basic">The Pick.</h1>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="flex-grid-row margin-top-basic margin-bottom-basic">
        <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">
          <div class="u-video-embed-container">
            <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($youtube_id); ?>" allow="autoplay" allowfullscreen></iframe>
          </div>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">
          <div class="font-size-3 font-semibold margin-bottom-mid">
            <?php the_content(); ?>
          </div>
          <?php
            //======================================================================
            // SIGNUP FORM
            //======================================================================

            $netlify = 'https://novara-media-mailchimp-signup.netlify.app/.netlify/functions/mailchimp-signup';

            if ($_SERVER['HTTP_HOST'] === 'localhost:8888') { // for local dev
              $netlify = 'http://localhost:54008/.netlify/functions/mailchimp-signup';
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

            <div class="email-signup__feedback-processing font-size-3">
              <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
              </div>
            </div>

            <div class="email-signup__feedback-failed">
              <p>Sign up error: <span class="newsletter-page-email-signup__feedback-message"></span>. Maybe try again</p>
            </div>
            <div class="email-signup__feedback-completed">
              <p>Thanks for signing up. &#10003;</p>
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
