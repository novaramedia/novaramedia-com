<?php
get_header();

$override_title = IGV_get_option('_igv_support_page_title_override');

// $override_title = isset($args['override_text']) ? $args['override_text'] : false;

/*
$fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
$is_fundraiser = $fundraiser_expiration > time();
*/
?>

<!-- main content -->

<main id="main-content">

<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);

    $youtube_id = !empty($meta['_cmb_support_youtube']) ? $meta['_cmb_support_youtube'][0] : false;
?>
  <!-- main posts loop -->
  <article id="page" class="support-page">
    <div class="background-cover-image background-light-blue" style="background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/nm10-splash.svg'; ?>);">
      <div class="container">
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-s-12">
            <h4 class="margin-top-small margin-bottom-tiny"><?php
              if (!empty($override_title)) {
                echo $override_title;
              } else {
                echo 'Support Us';
              }
            ?></h4>
          </div>
        </div>
        <div class="flex-grid-row support-page__hero-wrapper">
          <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-xxl-10 flex-offset-xxl-0">
            <h1 class="font-size-5">We're in it for the long&nbsp;haul.</h1>
            <h2 class="font-size-5 font-color-white">Are you with&nbsp;us?</h2>
          </div>
        </div>
      </div>
    </div>

    <?php
      get_template_part('partials/support-section', null, array(
        'heading_copy' => 'Become a supporter',
        'override_text' => 'It’s been ten years since Novara Media first started. Now, more than ever, we need your support.'
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
          
          <ul class="inline-action-list margin-top-small">
            <?php
              $share_url = 'https://novaramedia.com/support/';
            ?>
            <li><?php render_tweet_link($share_url, $post->post_title, 'Tweet your support'); ?></li>
            <li><?php render_facebook_share_link($share_url, 'Share this page on Facebook'); ?></li>
            <li><?php render_email_share_link($share_url, $post->post_title, 'Email to a friend');?></li>
            <li><?php render_reddit_share_link($share_url, $post->post_title, 'Post to Reddit');?></li>
          </ul>
        </div>
        <?php } ?>
        
        <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6 support-page__content-copy">
          <?php the_content(); ?>
        </div>
      </div>    
    </div>
    
    <div class="background-cover-image font-color-white" style="background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/nm10-splash.svg'; ?>);">
      <div class="background-cover-overlay" style="background: linear-gradient(90deg, #B97EFF 0%, rgba(220, 0, 5, 0) 96.11%);"></div>
      <div class="container padding-top-mid padding-bottom-mid">      
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4">
            <h3 class="font-size-2 margin-bottom-small">Already a supporter?</h3>
            <?php if (!empty($meta['_cmb_page_extra'])) {
              echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0]);
            } ?>
            <p><a href="https://donate.novaramedia.com/login" class="nm-button nm-button--red">Log in to your account</a></p>
          </div>   
        </div>
      </div>
    </div>

    <?php
      get_template_part('partials/support-section', null, array(
        'heading_copy' => 'Become a supporter',
        'override_text' => 'Not yet a supporter? We\'re asking that you donate one hour of your wage each month.'
      ));
    ?>
        
    <div id="other-donation-methods" class="container">      
      <div class="flex-grid-row padding-top-mid padding-bottom-mid">
        <div class="flex-grid-item flex-item-xxl-12 margin-bottom-basic">
          <h4>Other Donation Methods</h4>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-l-12 flex-item-xxl-3 margin-bottom-small">
          <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have options for PayPal, UK Direct Debit or Bitcoin if you prefer.</p>
        </div>
        
        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-paypal.svg'); ?>
          </p>
          <p><strong>PayPal</strong></p>
          <p>You can donate to us via PayPal. You can set a recurring donation or just give a one-off for any amount.</p>
          <p><a class="nm-button nm-button--red" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Donate to us via PayPal</a></p>
        </div>
        
        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-directdebit.svg'); ?>
          </p>
          <p><strong>GoCardless</strong></p>
          <p>You can donate to us via a UK Direct Debit regular bank transfer using the GoCardless platform</p>
          
          <div class="flex-grid-row flex-grid--nested-tight margin-bottom-tiny">
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
              <a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5 per month</a>
            </div>
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
            <a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10 per month</a>
            </div>
          </div>
          <div class="flex-grid-row flex-grid--nested-tight margin-bottom-tiny">
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
              <a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20 per month</a>
            </div>
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
              <a class="nm-button nm-button--red" href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50 per month</a>
            </div>
          </div>
        </div>
          
        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-bitcoin.svg'); ?>
          </p>
          <p><strong>Crypto</strong></p>
          <p>We accept BTC at this address: <code class="font-size-smaller">1EtbqDDij5uT3jnAR5ihFqF3kJA5YZN1i</code> If you want to send to a one time address please email us at: <a href="mailto:donations@novaramedia.com?subject=BTC donation">donations@novaramedia.com</a> </p>
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