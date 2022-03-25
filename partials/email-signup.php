<?php
  $netlify = 'https://novara-media-mailchimp-signup.netlify.app/.netlify/functions/mailchimp-signup';

  if ($_SERVER['HTTP_HOST'] === 'localhost:8888') { // for local dev
    $netlify = 'http://localhost:60573/.netlify/functions/mailchimp-signup';
  }

  if (!empty($args['newsletter']) && $netlify) {
    $newsletter = $args['newsletter'];
    $background_color = 'light-blue';

    if (!empty($args['background-color'])) {
      $background_color = $args['background-color'];
    }
?>
<div class="email-signup padding-top-mid padding-bottom-mid background-<?php echo $background_color; ?>">
  <div class="container">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <h4>Sign up to our newsletter <?php echo $newsletter; ?></h4>
      </div>
    </div>
    <?php
      if (!empty($args['copy'])) {
    ?>
    <div class="flex-grid-row margin-bottom-tiny">
      <div class="flex-grid-item flex-item-s-12">
        <p><?php echo $args['copy']; ?></p>
      </div>
    </div>
    <?php
      }
    ?>
    <form class="email-signup__form" action="<?php echo $netlify; ?>" method="post" target="_blank">
      <input type="hidden" name="newsletter" value="<?php echo $newsletter; ?>" />

      <div class="email-signup__inputs flex-grid-row">
        <div class="flex-grid-item flex-item-s-12 flex-item-m-4 flex-item-xxl-3 margin-bottom-small">
          <div class="form-group">
            <label class="u-visuallyhidden" for="firstName">First name:</label>
            <input name="firstName" class="email-signup__name-input" id="firstName" type="text" autocomplete="given-name" placeholder="First name" />
          </div>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-5 margin-bottom-small">
          <div class="form-group">
            <label class="u-visuallyhidden" for="email">Email:</label>
            <input name="email" class="email-signup__email-input" id="email" type="email" autocomplete="email" placeholder="Email" required />
          </div>
        </div>
        <div class="flex-grid-item flex-item-m-12 flex-item-xxl-4 margin-bottom-small email-signup__completion-section">
          <div class="email-signup__email-gdpr-group form-group">
            <label for="newsletter-gdpr">I agree to the <a target="_blank" rel="noopener" href="<?php echo site_url('privacy-policy/'); ?>">Privacy Policy</a></label>
            <input name="gdpr" class="email-signup__email-gdpr-input" id="newsletter-gdpr" type="checkbox" value="accepted" required/>
          </div>

          <input class="email-signup__submit nm-button" type="submit" value="Sign up">
        </div>
      </div>

      <div class="email-signup__feedback-processing">
        <div class="spinner">
          <div class="double-bounce1"></div>
          <div class="double-bounce2"></div>
        </div>
      </div>

      <div class="flex-grid-row">
        <div class="flex-grid-item flex-item-s-12 margin-bottom-small email-signup__feedback-failed">
          <p>Sign up error: <span class="email-signup__feedback-message"></span>. Maybe try again</p>
        </div>
        <div class="flex-grid-item flex-item-s-12 margin-bottom-small email-signup__feedback-completed">
          <p>Thanks for signing up. &#10003;</p>
        </div>
      </div>
    </form>
  </div>
</div>
<?php
  }
?>
