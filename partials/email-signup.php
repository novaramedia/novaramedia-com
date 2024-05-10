<?php
  // Each newsletter has metadata set to make choices about how the signup block renders,
  // however there is also the ability to override some of those options by passing them in as arguments to the partial.
  // There are also defaults set for some of these options incase they are not set in the metadata.

  $newsletter_page = get_post($args['newsletter_page_id']);

  if (!$newsletter_page) {
    return;
  }

  $meta = get_post_meta($newsletter_page->ID);
  $mailchimp_key = !empty($meta['_nm_mailchimp_key']) ? $meta['_nm_mailchimp_key'][0] : false;

  if (!$mailchimp_key) {
    return; // if there isn't a mailchimp key then don't render the signup block
  }

  $netlify = 'https://novara-media-mailchimp-signup.netlify.app/.netlify/functions/mailchimp-signup';

  if ($_SERVER['HTTP_HOST'] === 'localhost:8888' || $_SERVER['HTTP_HOST'] === 'novaramediacom.local') { // for local dev
    $netlify = 'http://localhost:60573/.netlify/functions/mailchimp-signup';
  }

  // get metadata with fallback defaults
  $background_color = !empty($meta['_nm_banner_background']) ? $meta['_nm_banner_background'][0] : 'black';
  $text_color = !empty($meta['_nm_banner_text_color']) ? $meta['_nm_banner_text_color'][0] : 'white';
  $button_color = !empty($meta['_nm_banner_button_color']) ? $meta['_nm_banner_button_color'][0] : 'red';

  $headline = !empty($meta['_nm_banner_headline']) ? $meta['_nm_banner_headline'][0] : 'Sign up to our newsletter ' . $mailchimp_key;
  $copy = !empty($meta['_nm_banner_text']) ? $meta['_nm_banner_text'][0] : false;
  $image_id = !empty($meta['_nm_banner_image_id']) ? $meta['_nm_banner_image_id'][0] : false;

  // override colours if set
  if (!empty($args['background-color'])) {
    $background_color = $args['background-color'];
  }

  if (!empty($args['text-color'])) {
    $text_color = $args['text-color'];
  }

  if (!empty($args['button-color'])) {
    $button_color = $args['button-color'];
  }
?>
<div class="email-signup <?php if ($background_color == 'white') { echo 'mt-6 mb-5'; } else { echo 'pt-6 pb-6'; } ?> background-<?php echo $background_color; ?> font-color-<?php echo $text_color; ?>">
  <div class="container">
    <div class="grid-row">
      <div class="grid-item is-s-24 is-xxl-12 mb-s-4">
        <h3 class="fs-8 fs-s-6 mb-4 js-fix-widows"><?php echo $headline; ?></h3>
        <p class="fs-6 fs-s-4-sans">
          <?php echo $copy; ?>
        </p>
        <?php if (!is_page('newsletters')) { ?>
          <div class="mt-3 fs-2">
            <a href="<?php echo site_url('newsletters/'); ?>" class="ui-hover"><span class="ui-dot ui-dot--red"></span>Discover all our newsletters</a>
          </div>
        <?php } ?>
      </div>
      <div class="grid-item <?php echo $image_id === false ? 'is-s-24 is-xxl-12' : 'is-s-16 is-xxl-8'; ?>">
        <form class="email-signup__form" action="<?php echo $netlify; ?>" method="post" target="_blank">
          <input type="hidden" name="newsletter" value="<?php echo $mailchimp_key; ?>" />

          <div class="email-signup__inputs">
            <div class="form-group mb-2">
              <label class="u-visuallyhidden" for="firstName">First name:</label>
              <input name="firstName" class="email-signup__name-input ui-input <?php if ($background_color === 'white') {echo 'ui-input--border-gray';} ?>" id="firstName" type="text" autocomplete="given-name" placeholder="First name" />
            </div>

            <div class="form-group mb-2">
              <label class="u-visuallyhidden" for="email">Email:</label>
              <input name="email" class="email-signup__email-input ui-input <?php if ($background_color === 'white') {echo 'ui-input--border-gray';} ?>" id="email" type="email" autocomplete="email" placeholder="Email" required />
            </div>

            <div class="email-signup__email-gdpr-group form-group layout-flex-align-center mb-2">
              <label for="newsletter-gdpr" class="fs-2">I agree to the <a target="_blank" rel="noopener" href="<?php echo site_url('privacy-policy/'); ?>">Privacy Policy</a></label>
              <input name="gdpr" class="email-signup__email-gdpr-input ui-checkbox <?php if ($background_color === 'white') {echo 'ui-checkbox--border-gray';} ?> ml-2" id="newsletter-gdpr" type="checkbox" value="accepted" required/>
            </div>

            <input class="email-signup__submit ui-button ui-button--<?php echo $button_color; ?> fs-6" type="submit" value="Sign up">
          </div>

          <div class="email-signup__feedback-processing">
            <div class="spinner">
              <div class="double-bounce1"></div>
              <div class="double-bounce2"></div>
            </div>
          </div>

          <div class="email-signup__feedback-failed mt-2">
            <p>Sign up error: <span class="email-signup__feedback-message"></span>. Maybe try again</p>
          </div>

          <div class="email-signup__feedback-completed fs-6 fs-s-4-sans">
            <p>Thanks for signing up. &#10003;</p>
          </div>
        </form>
      </div>
      <?php if ($image_id) { ?>
        <div class="grid-item is-s-8 is-xxl-4">
          <?php echo wp_get_attachment_image($image_id, 'col4-square', false, ['class' => 'email-signup__image']); ?>
        </div>
      <?php } ?>
      <?php if ($background_color == 'white') { ?>
        <div class="grid-item is-xxl-24 mt-5">
          <hr />
        </div>
      <?php } ?>
    </div>
  </div>
</div>
