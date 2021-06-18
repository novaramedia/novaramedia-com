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
  <article id="page">
    <div class="container">
      <div class="flex-grid-row margin-bottom-basic">
        <div class="flex-grid-item flex-item-s-12">
          <h4 class="margin-bottom-tiny">Support Us</h4>
          <h2 class="font-size-massive">Every single thing we do is funded by you.</h2>
        </div>
      </div>
    </div>

    <div class="container">      
      <div class="flex-grid-row margin-bottom-basic">
        <div class="flex-grid-item flex-item-s-12">
          New form goes here***
        </div>
      </div>
    </div>

    <div class="container">      
      <div class="flex-grid-row margin-bottom-basic">
        <?php
          if ($youtube_id) {
        ?>
        <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">
          <div class="u-video-embed-container">
            <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($youtube_id); ?>"></iframe>
          </div>
        </div>
        <?php } ?>
        
        <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    
    <div id="other-donation-methods" class="container">      
      <div class="flex-grid-row">
        <div class="flex-grid-item flex-item-xxl-12 margin-bottom-basic">
          <h4>Other Donation Methods</h4>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-6">
          <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have options for PayPal, UK Direct Debit or Bitcoin if you prefer.</p>
        </div>
      </div>
      <div class="flex-grid-row">
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-4">
          <p>You can donate to us via a UK Direct Debit regular bank transfer using the GoCardless platform</p>
          <p><a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5 per month</a></p>
          <p><a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10 per month</a></p>
          <p><a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20 per month</a></p>
          <p><a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50 per month</a></p>
        </div>
        
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-4">
          <p>You can donate to us via PayPal. You can set a recurring donation or just give a one-off for any amount.</p>
          <p><a class="nm-button nm-button--red" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Donate to us via PayPal</a></p>
        </div>
  
        <div class="flex-grid-item flex-item-s-12 flex-item-m-8 flex-item-xxl-4">
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