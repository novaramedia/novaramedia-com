<?php
  $newsletter = false;
  
  if (!empty($args['newsletter']) && !empty($args['netlify'])) {
    $newsletter = $args['newsletter'];
?>
<div class="email-signup padding-top-mid padding-bottom-mid">
  <div class="container">
    <div class="row margin-bottom-small">
      <div class="col col24">  
        <h4>Sign up to our newsletter <?php echo $newsletter; ?></h4>
      </div>
    </div>
    <?php
      if (!empty($args['copy'])) {
    ?>
    <div class="row margin-bottom-tiny">
      <div class="col col24">
          <p><?php echo $args['copy']; ?></p>
      </div>
    </div>
    <?php
      }
    ?>
    <form action="<?php echo $args['netlify']; ?>" method="post" target="_blank">
      <div class="row margin-bottom-small">
        <div class="col col16">            
            <input type="hidden" name="newsletter" value="<?php echo $newsletter; ?>" />
            
            <div class="form-group">
              <label class="u-visuallyhidden" for="email">Email:</label>
              <input class="email-signup__email-input" id="email" type="email" autocomplete="email" placeholder="Email" required>
            </div>
        </div>
        <div class="col col8 email-signup__completion-section">  
            <div class="email-signup__email-gdpr-group form-group">
              <label for="newsletter-gdpr">I agree to the <a target="_blank" rel="noopener" href="https://novaramedia.com/privacy-policy/">Privacy Policy</a></label>
              <input class="email-signup__email-gdpr-input" id="newsletter-gdpr" type="checkbox" value="gdpr" required/>          
            </div>
                            
            <input class="email-signup__submit nm-button" type="submit" value="Sign up">
        </div>
      </div>
    </form>
  </div>
</div>
<?php
  }
?>