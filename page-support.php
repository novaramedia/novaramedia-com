<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_header();
?>
<main id="main-content">
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);

    $page_tag_override = !empty($meta['_nm_support_tag_override']) ? $meta['_nm_support_tag_override'][0] : false;
    $youtube_id = !empty($meta['_nm_support_youtube']) ? $meta['_nm_support_youtube'][0] : false;
    $title = !empty($meta['_nm_support_header_title']) ? $meta['_nm_support_header_title'][0] : '';
    $subtitle = !empty($meta['_nm_support_header_subtitle']) ? $meta['_nm_support_header_subtitle'][0] : '';
    $form_tag_override = !empty($meta['_nm_support_form_tag_override']) ? $meta['_nm_support_form_tag_override'][0] : false;
    $form_copy_override = !empty($meta['_nm_support_form_copy_override']) ? $meta['_nm_support_form_copy_override'][0] : false;
?>
  <article id="page" class="support-page">
    <div class="background-red background-support-texture">
      <div class="container">
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-s-12">
            <h4 class="margin-top-small margin-bottom-tiny font-size-9 text-uppercase font-weight-bold font-color-white"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
              if (!empty($page_tag_override)) {
                echo $page_tag_override;
              } else {
                echo 'Support Us';
              }
            ?></h4>
          </div>
        </div>
        <div class="flex-grid-row flex-grid-row--align-center support-page__hero-wrapper font-color-white">
          <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-xxl-6 flex-offset-xxl-0">
            <h1 class="font-size-17 font-weight-bold mobile-margin-bottom-basic" style="line-height: .85;">
              <span class="only-desktop">Build<br/>&nbsp;people-<br/>&nbsp;&nbsp;powered<br/>&nbsp;&nbsp;&nbsp;media.</span>
              <span class="only-mobile">Build people-powered media.</span>
            </h1>
          </div>
          <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-xxl-6 flex-offset-xxl-0">
            <h2 class="font-size-13 font-weight-bold margin-bottom-tiny js-fix-widows">Back truthful, independent journalism today.</h2>
            <h2 class="font-size-13 font-weight-bold js-fix-widows">Donate one hour’s wage per month or whatever you can afford.</h2>
          </div>
        </div>
      </div>
    </div>

    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
      get_template_part('partials/support-section', null, array(
        'heading_copy' => $form_tag_override,
        'override_text' => $form_copy_override
      ));
    ?>

    <div class="container">
      <div class="flex-grid-row margin-top-basic margin-bottom-basic">
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          if ($youtube_id) {
        ?>
        <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6 margin-bottom-small">
          <div class="u-video-embed-container">
            <iframe class="youtube-player" type="text/html" src="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo generate_youtube_embed_url($youtube_id); ?>"></iframe>
          </div>

          <ul class="inline-action-list margin-top-small">
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
              $share_url = 'https://novaramedia.com/support/';
            ?>
            <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_tweet_link($share_url, $post->post_title, 'Tweet your support'); ?></li>
            <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_facebook_share_link($share_url, 'Share this page on Facebook'); ?></li>
            <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_email_share_link($share_url, $post->post_title, 'Email to a friend');?></li>
            <li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} render_reddit_share_link($share_url, $post->post_title, 'Post to Reddit');?></li>
          </ul>
        </div>
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} } ?>

        <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6 text-copy font-serif">
          <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} the_content(); ?>
        </div>
      </div>
    </div>

    <div class="background-lilac background-support-texture-alt">
      <div class="container padding-top-mid padding-bottom-mid font-color-white">
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4">
            <h4 class="font-size-9 text-uppercase font-weight-bold margin-bottom-small">Already a supporter?</h4>
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} if (!empty($meta['_cmb_page_extra'])) {
              echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0]);
            } ?>
            <p class="mt-4"><a href="https://donate.novaramedia.com/login" class="ui-button ui-button--white ui-button--small">Log in to your account</a></p>
          </div>
        </div>
      </div>
    </div>

    <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
      get_template_part('partials/support-section', null, array(
        'heading_copy' => $form_tag_override,
        'override_text' => $form_copy_override
      ));
    ?>

    <div id="other-donation-methods" class="container">
      <div class="flex-grid-row padding-top-mid padding-bottom-mid">
        <div class="flex-grid-item flex-item-xxl-12 margin-bottom-basic">
          <h4 class="font-size-9 text-uppercase font-weight-bold">Other Donation Methods</h4>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-l-12 flex-item-xxl-3 margin-bottom-small">
          <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have options for PayPal, UK Direct Debit or Bitcoin if you prefer.</p>
        </div>

        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-paypal.svg'); ?>
          </p>
          <p class="font-weight-bold mb-3">PayPal</p>
          <p>You can donate to us via PayPal. You can set a recurring donation or just give a one-off for any amount.</p>
          <p><a class="mt-3 ui-button ui-button--red ui-button--small" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Donate to us via PayPal</a></p>
        </div>

        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-directdebit.svg'); ?>
          </p>
          <p class="font-weight-bold mb-3"><strong>GoCardless</strong></p>
          <p>You can donate to us via a UK Direct Debit regular bank transfer using the GoCardless platform</p>

          <div class="mt-3 flex-grid-row flex-grid--nested-tight margin-bottom-tiny">
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
              <a class="ui-button ui-button--red ui-button--small" href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5 per month</a>
            </div>
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
            <a class="ui-button ui-button--red ui-button--small" href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10 per month</a>
            </div>
          </div>
          <div class="flex-grid-row flex-grid--nested-tight margin-bottom-tiny">
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
              <a class="ui-button ui-button--red ui-button--small" href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20 per month</a>
            </div>
            <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6">
              <a class="ui-button ui-button--red ui-button--small" href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50 per month</a>
            </div>
          </div>
        </div>

        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/support-logo-bitcoin.svg'); ?>
          </p>
          <p class="font-weight-bold mb-3"><strong>Crypto</strong></p>
          <p>We accept BTC at this address: <code class="font-size-smaller">1EtbqDDij5uT3jnAR5ihFqF3kJA5YZN1i</code> If you want to send to a one time address please email us at: <a href="mailto:donations@novaramedia.com?subject=BTC donation">donations@novaramedia.com</a> </p>
        </div>
      </div>
    </div>
  <!-- end post -->
  </article>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
  }
} ?>
<!-- end main-content -->
</main>

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_footer();
?>
