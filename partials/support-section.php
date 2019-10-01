<?php
  $fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
  $fundraiser_form_text = IGV_get_option('_igv_fundraiser_form_text');

  $minDonation = 3;
  $defaultSubscription = 8;
  $defaultOneoff = 10;
  $maxSubscription = 80;
  $maxOneoff = 800;
?>

<div class="support-section background-red font-color-white padding-top-mid padding-bottom-mid">
  <div class="container">
    <div class="row margin-bottom-small">
      <div class="col col24">
<?php
    if ($fundraiser_expiration > time() && $fundraiser_form_text) {
?>
        <p class="font-size-h2"><?php echo $fundraiser_form_text; ?></p>
<?php
    } else {
?>
        <h4>Support Us</h4>
<?php
    }
?>
      </div>
    </div>

    <div class="row margin-bottom-tiny font-bold">
      <div class="col col12">
        <p>Become a subscriber and support Novara Media monthly:</p>
      </div>
      <div class="col col12">
        <p>Or you can give us a one-off donation:</p>
      </div>
    </div>

    <div class="row">

<!--  desktop monthly form -->
      <form class="support-form only-desktop" action="https://payment.novaramedia.com/subscription">
        <div class="col col3">
          <div class="support-form-holder u-flex-center">
            £<span class="support-form-value"><?php echo $defaultSubscription; ?></span> /month
          </div>
        </div>
        <div class="col col9">
          <div class="support-form-holder u-flex-center">
            <input class="support-form-slider" type="range" value="<?php echo $defaultSubscription; ?>" min="<?php echo $minDonation; ?>" max="<?php echo $maxSubscription; ?>" step="1" name="amount" /> £££ <input class="support-form-submit" type="submit" value="Go" />
          </div>
        </div>
      </form>

<!--  desktop oneoff form -->
      <form class="support-form only-desktop" action="https://payment.novaramedia.com/oneoff">
        <div class="col col3">
          <div class="support-form-holder u-flex-center">
            £<span class="support-form-value"><?php echo $defaultOneoff; ?></span>
          </div>
        </div>
        <div class="col col9">
          <div class="support-form-holder u-flex-center">
            <input class="support-form-slider" type="range" value="<?php echo $defaultOneoff; ?>" min="<?php echo $minDonation; ?>" max="<?php echo $maxOneoff; ?>" step="1" name="amount" /> £££ <input class="support-form-submit" type="submit" value="Go" />
          </div>
        </div>
      </form>

<!--  mobile monthly form -->
      <form class="support-form only-mobile" action="https://payment.novaramedia.com/subscription">
        <div class="col">
          <div class="support-form-holder u-flex-center mobile-margin-bottom-small">
            <input type="number" value="<?php echo $defaultSubscription; ?>" min="<?php echo $minDonation; ?>" max="<?php echo $maxSubscription; ?>" step="1" name="amount" /> £ /month <input class="support-form-submit" type="submit" value="Go" />
          </div>
        </div>
      </form>

<!--  mobile oneoff form -->
      <form class="support-form only-mobile" action="https://payment.novaramedia.com/oneoff">
        <div class="col">
          <div class="support-form-holder u-flex-center">
            <input type="number" value="<?php echo $defaultOneoff; ?>" min="<?php echo $minDonation; ?>" max="<?php echo $maxOneoff; ?>" step="1" name="amount" /> £ one off<input class="support-form-submit" type="submit" value="Go" />
          </div>
        </div>
      </form>

    </div>
  </div>
</div>