<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
    $fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
    $fundraiser_youtube_id = IGV_get_option('_igv_fundraiser_youtube_id');
    $is_fundraiser = $fundraiser_expiration > time();
?>
  <!-- main posts loop -->
  <article id="page">
    <div class="container">
  <?php if ($is_fundraiser) { ?>
      <div class="row margin-bottom-small">
        <div class="col col24">
          <h4>Fundraiser<span id="progress-text"></span></h4>
        </div>
      </div>
      <div id="progress-bar-row" class="row margin-bottom-small">
        <div id="progress-bar-holder" class="col col24">
          <div id="progress-bar"></div>
        </div>
      </div>
  <?php
        } else {
  ?>
      <div class="row margin-bottom-small">
        <div class="col col24">
          <h4><?php the_title(); ?></h4>
        </div>
      </div>
  <?php
        }
  ?>
    </div>
  <?php
    if (!empty($meta['_cmb_support_youtube']) || $fundraiser_youtube_id) {
      $youtube_id = $meta['_cmb_support_youtube'][0];
      if ($fundraiser_youtube_id && $is_fundraiser) {
        $youtube_id = $fundraiser_youtube_id;
      }
  ?>
    <div class="container">
      <div class="row margin-bottom-basic">
        <div class="col col24">
          <div class="u-video-embed-container">
            <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($youtube_id); ?>"></iframe>
          </div>
        </div>
      </div>
    </div>
  <?php
      get_template_part('partials/support-section');
    }
  ?>
    <div class="container">
      <div class="row margin-top-basic margin-bottom-basic">
        <div class="col col24">
          <div class="text-copy text-copy-max-width">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    </div>

    <?php
      get_template_part('partials/support-section');

      if (!empty($meta['_cmb_page_extra_section'])) {
    ?>
    <div class="container">
      <div class="row margin-top-basic margin-bottom-basic">
        <div class="col col24">
          <div class="text-copy text-copy-max-width">
            <?php echo apply_filters( 'the_content', $meta['_cmb_page_extra_section'][0]); ?>
          </div>
        </div>
      </div>
    </div>
<?php
        get_template_part('partials/support-section');
      }
?>
    <div id="other-donation-methods" class="container">
      <div class="row margin-top-basic margin-bottom-basic">
        <div class="col col24">
          <h4>Other Donation Methods</h4>
        </div>
      </div>
      <div class="row margin-top-mid margin-bottom-basic">
        <div class="col col24">
          <div class="text-copy text-copy-max-width">
            <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have PayPal or UK Direct Debit via GoCardless if you prefer.</p>
            <h3>PayPal</h3>
            <p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Click here to donate to us via PayPal.</a> You can set a recurring donation or just give a one-off for any amount.</p>
            <h3>GoCardless (UK Direct Debit)</h3>
            <p>With GoCardless you can setup and manage a monthly Direct Debit. If you would like to donate a specific amount per month please email us at: <a href="mailto:donations@novaramedia.com?subject=GoCardless custom amount">donations@novaramedia.com</a> and we can set that up for you</p>
            <ul>
              <li><a href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5 per month</a></li>
              <li><a href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10 per month</a></li>
              <li><a href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20 per month</a></li>
              <li><a href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50 per month</a></li>
            </ul>
            <h3>Crypto</h3>
            <p>We accept BTC at this address: 1EtbqDDij5uT3jnAR5ihFqF3kJA5YZN1i If you want to send to a one time address please email us at: <a href="mailto:donations@novaramedia.com?subject=BTC donation">donations@novaramedia.com</a> </p>
          </div>
        </div>
      </div>
    </div>
  <!-- end post -->
  </article>
<?php
  }
} else {
?>
  <div class="container">
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </div>
<?php
} ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>