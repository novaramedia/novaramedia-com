<?php
get_header();

$fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
$fundraiser_youtube_id = IGV_get_option('_igv_fundraiser_youtube_id');
$is_fundraiser = $fundraiser_expiration > time();
?>

<!-- main content -->

<main id="main-content">

<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);

    $youtube_id = !empty($meta['_cmb_support_youtube']) ? $meta['_cmb_support_youtube'][0] : false;
    
    if ($fundraiser_youtube_id && $is_fundraiser) {
      $youtube_id = $fundraiser_youtube_id;
    }
?>
  <!-- main posts loop -->
  <article id="page" class="support-page">
    <div class="background-cover-image" style="background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/nm10-splash.svg'; ?>);">
      <div class="container">
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-s-12">
            <h4 class="margin-top-small margin-bottom-tiny">Anniversary Fundraiser</h4>
          </div>
        </div>
        <div class="flex-grid-row padding-top-large padding-bottom-large">
          <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-xxl-10 flex-offset-xxl-1">
            <h2 class="font-size-massive">We're in it for the long&nbsp;haul</h2>
            <h2 class="font-size-massive font-color-white">Are you with&nbsp;us?</h2>
          </div>
        </div>
      </div>
    </div>

    <?php
      get_template_part('partials/support-section', null, array(
        'show_text' => false,
        'large_text' => true,
        'heading_copy' => 'Donate to Novara Media',
      ));
    ?>

    <div class="container">      
      <div class="flex-grid-row margin-top-basic margin-bottom-basic">
        <?php
          if ($youtube_id) {
        ?>
        <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6 margin-bottom-small">
          <div class="u-video-embed-container">
            <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($youtube_id); ?>"></iframe>
          </div>
        </div>
        <?php } ?>
        
        <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6 support-page__content-copy">
          <?php the_content(); ?>
        </div>
      </div>    
    </div>
    
    <div class="background-red font-color-white">
      <div class="container">      
        <div class="flex-grid-row padding-top-mid padding-bottom-mid">
          <div class="flex-grid-item flex-item-s-12 flex-item-xxl-4">
            <h4 class="margin-bottom-small">Already a supporter?</h4>
            <p>If you can, we’re asking that you increase your donation by a few pounds each month. Just log in to your supporter account, type in the new total amount you want to donate each month, and click ‘edit donation’. Any problems, drop us an email at donations@novaramedia.com.</p>
          </div>
          <div class="flex-grid-item flex-item-s-12 flex-item-xxl-2 margin-top-basic">
            <p><a href="https://payment.novaramedia.com/login" class="nm-button nm-button--red-dark">Log in to your account</a></p>
          </div>

          <div class="flex-grid-item flex-item-s-12 flex-item-xxl-4">
            <h4 class="margin-bottom-small">Not yet a supporter?</h4>
            <p>Head to the red bar above, select the amount you’d like to donate each month, and hit ‘Go’.</p>
          </div>
          <div class="flex-grid-item flex-item-s-12 flex-item-xxl-2 margin-top-basic">

          </div>          
        </div>
      </div>
    </div>
    
    <div id="other-donation-methods" class="container">      
      <div class="flex-grid-row padding-top-mid padding-bottom-mid">
        <div class="flex-grid-item flex-item-xxl-12 margin-bottom-basic">
          <h4>Other Donation Methods</h4>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-3">
          <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have options for PayPal, UK Direct Debit or Bitcoin if you prefer.</p>
        </div>
        
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-paypal.svg'); ?>
          </p>
          <p><strong>PayPal</strong></p>
          <p>You can donate to us via PayPal. You can set a recurring donation or just give a one-off for any amount.</p>
          <p><a class="nm-button nm-button--red" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Donate to us via PayPal</a></p>
        </div>
        
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-directdebit.svg'); ?>
          </p>
          <p><strong>GoCardless</strong></p>
          <p>You can donate to us via a UK Direct Debit regular bank transfer using the GoCardless platform</p>
          <p class="margin-bottom-tiny">
            <a class="nm-button nm-button--red nm-button--inline nm-button--half" href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5 per month</a>
            <a class="nm-button nm-button--red nm-button--inline nm-button--half" href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10 per month</a>
          </p>
          <p>
            <a class="nm-button nm-button--red nm-button--inline nm-button--half" href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20 per month</a>
            <a class="nm-button nm-button--red nm-button--inline nm-button--half" href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50 per month</a>
          </p>
        </div>
          
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-bitcoin.svg'); ?>
          </p>
          <p><strong>Crypto</strong></p>
          <p>We accept BTC at this address: 1EtbqDDij5uT3jnAR5ihFqF3kJA5YZN1i If you want to send to a one time address please email us at: <a href="mailto:donations@novaramedia.com?subject=BTC donation">donations@novaramedia.com</a> </p>
        </div>
      </div>
    </div>
  <!-- end post -->
  </article>
<?php
  }
} ?>
<!-- end main-content -->
</main>

<?php
get_footer();
?>