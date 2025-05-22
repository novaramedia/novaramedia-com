<?php
get_header();
?>
<main id="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );

    $page_tag_override = ! empty( $meta['_nm_support_tag_override'] ) ? $meta['_nm_support_tag_override'][0] : false;
    $youtube_id = ! empty( $meta['_nm_support_youtube'] ) ? $meta['_nm_support_youtube'][0] : false;
    $title = ! empty( $meta['_nm_support_header_title'] ) ? $meta['_nm_support_header_title'][0] : '';
    $subtitle = ! empty( $meta['_nm_support_header_subtitle'] ) ? $meta['_nm_support_header_subtitle'][0] : '';
    $form_tag_override = ! empty( $meta['_nm_support_form_tag_override'] ) ? $meta['_nm_support_form_tag_override'][0] : false;
    $form_copy_override = ! empty( $meta['_nm_support_form_copy_override'] ) ? $meta['_nm_support_form_copy_override'][0] : false;
    ?>
  <article id="page" class="support-page">
    <div class="background-white background-support-texture-alt--fade-to-white pb-6">
      <div class="support-page__container">
        <div class="flex-grid-row">
          <div class="support-page__tag-wrapper">
            <h4 class="margin-top-small margin-bottom-tiny font-size-9 font-weight-bold font-color-black ui-border-bottom ui-border--black pb-3">
            <?php
            if ( ! empty( $page_tag_override ) ) {
              echo $page_tag_override;
            } else {
              echo 'Support Us';
            }
            ?>
            </h4>
          </div>
        </div>
        <div class="flex-grid-row flex-grid-row--align-center pt-2 pb-2">
            <h1 class="font-weight-bold support-page__heading">
              <span class="font-color-white">Beat the billionaires.</span></br>
              <span class="font-color-black">Unfuck the media.</span>
            </h1>
        </div>
      </div>
      <div class="support-page__container">
        <div class="flex-grid-row" style="position: relative;">
          <div class="flex-grid-item flex-item-l-6 flex-item-xxl-6 background-white text-copy font-serif support-page__left-radius">
            <div class="m-5 font-size-13 font-serif">
              <?php the_content(); ?>
              <?php
              if ( $youtube_id ) {
                ?>
              <div class="u-video-embed-container">
                <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url( $youtube_id ); ?>"></iframe>
              </div>
              <?php } ?>
            </div>
          </div>

          <div class="flex-grid-item flex-item-l-6 flex-item-xxl-6 support-page__background-grey support-page__right-radius">

              <div class="support-page__donation-form-sticky mb-4">
                <div class="background-red m-5" style="height: 200px;">
                  This is to mimic support box for now
                </div>
              </div>
          </div>
        </div>

      </div>
    </div>
    <div class="support-page__background-grey">
      <!-- how we are funded -->
      <style>
          .support-page__infographic {
            background-image: url('<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/desktop-how-we-are-funded-graphic.svg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            height: 200px;
            width: 100%;
          }
          @media (max-width: 768px) {
            .support-page__infographic {
              background-image: url('<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/mobile-how-we-are-funded-graphic.svg');
              height: 400px;
            }
          }
      </style>
      <div class="support-page__container">
        <div class="flex-grid-row pt-6 pb-6 support-page__text-container ui-border-top ui-border--black">
          <div class="text-uppercase p-2 background-black font-color-white text-align-center font-size-10">How we are funded</div>
          <br/>
          <div class="support-page__infographic"></div>
          <br/>
          <div class="font-weight-bold support-page__text-box-width text-align-center font-size-12">Because the vast majority of our income is raised directly from supporters, we can be editorially independent without ever having to toe someone else’s editorial line. It’s a key principle that has always underpinned our funding model.</div>
        </div>
      </div>
      <!-- what we spend our funds on -->
      <div class="support-page__container mb-6 pb-6">
        <div class="flex-grid-row pt-6 pb-6 support-page__text-container background-white ui-rounded-box ">
          <div class="text-uppercase p-2 background-black font-color-white text-align-center font-size-10 mb-5">How we spend our funds</div>
          <div class="support-page__text-box-width text-align-center font-weight-bold font-size-13
          ">
            <div class="font-color-black mb-5">Every penny Novara Media makes goes back into our journalism.</div>
            <div class="font-color-gray-light mb-5">Your support pays for the hours it takes to research and meticulously check the claims in our articles.</div>
            <div class="font-color-gray-light mb-5">It pays for the studio space where we film our live show.</div>
            <div class="font-color-gray-light mb-5">It allows us to hire key roles, like a labour movement correspondent.</div>
            <div class="font-color-gray-light mb-5">It helps us fight (and win) against the smears of the rightwing press.</div>
            <div class="font-color-gray-light">Above all, it lets us break stories and challenge the establishment in ways mainstream media just won’t.</div>
          </div>
        </div>
      </div>
      <!-- Our story -->
      <style>
        .avif .support-page__our-story-background
        {
          background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/pages/support-page/our-story-background.avif'; ?>);

        }
        .webp .support-page__our-story-background {
          background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/pages/support-page/our-story-background.webp'; ?>);
        }

        .fallback .support-page__our-story-background {
          background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/pages/support-page/our-story-background.avif'; ?>);
        }
      </style>
      <div class="support-page__container pb-6 pt-6 mb-6 ">
        <div class="flex-grid-row support-page__text-container support-page__our-story-background ui-rounded-box pt-6 pb-6">
          <div class="text-uppercase font-weight-bold p-2 background-white font-color-black text-align-center font-size-10 mb-7"> Our story </div>
          <div class="font-color-white flex-grid-item flex-item-s-10 text-align-left pl-s-3 pr-s-3">
            <div class="font-weight-bold mb-4 font-size-12 ">
              Novara Media has grown from a humble radio show in 2011 to one of Britain’s most influential independent media organizations. Born amid anti-austerity movements with nothing but passion, we've consistently punched above our weight in the national conversation.
            </div>
            <div class="font-size-11 pb-5">
              From our breakthrough coverage during the 2017 General Election to our vital reporting during the COVID-19 pandemic and on Israel's actions in Gaza, we've remained committed to principled journalism that centers overlooked voices and stories. With your support, we can continue expanding our investigative capacity and building media that truly addresses the challenges of our time. Every contribution helps us maintain our independence in a landscape dominated by powerful interests.
            </div>
          </div>
      </div>
    </div>
    </div>

    <div class="background-red">
      <div class="support-page__container padding-top-mid padding-bottom-mid font-color-white">
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4">
            <h4 class="font-size-9 text-uppercase font-weight-bold margin-bottom-small">Already a supporter?</h4>
            <?php
            if ( ! empty( $meta['_cmb_page_extra'] ) ) {
              echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0] );
            }
            ?>
            <p class="mt-4"><a href="https://donate.novaramedia.com/login" class="ui-button ui-button--white ui-button--small">Log in to your account</a></p>
          </div>
        </div>
      </div>
    </div>

    <?php
    get_template_part(
        'partials/support-section',
        null,
        array(
            'heading_copy'  => $form_tag_override,
            'override_text' => $form_copy_override,
        )
    );
    ?>

    <div id="other-donation-methods" class="support-page__container">
      <div class="flex-grid-row padding-top-mid padding-bottom-mid">
        <div class="flex-grid-item flex-item-xxl-12 margin-bottom-basic">
          <h4 class="font-size-9 text-uppercase font-weight-bold">Other Donation Methods</h4>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-l-12 flex-item-xxl-3 margin-bottom-small">
          <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have options for PayPal, UK Direct Debit or Bitcoin if you prefer.</p>
        </div>

        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents( get_bloginfo( 'stylesheet_directory' ) . '/dist/img/support-logo-paypal.svg' ); ?>
          </p>
          <p class="font-weight-bold mb-3">PayPal</p>
          <p>You can donate to us via PayPal. You can set a recurring donation or just give a one-off for any amount.</p>
          <p><a class="mt-3 ui-button ui-button--red ui-button--small" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Donate to us via PayPal</a></p>
        </div>

        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
          <p>
            <?php echo url_get_contents( get_bloginfo( 'stylesheet_directory' ) . '/dist/img/support-logo-directdebit.svg' ); ?>
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
            <?php echo url_get_contents( get_bloginfo( 'stylesheet_directory' ) . '/dist/img/support-logo-bitcoin.svg' ); ?>
          </p>
          <p class="font-weight-bold mb-3"><strong>Crypto</strong></p>
          <p>We accept BTC at this address: <code class="font-size-smaller">1EtbqDDij5uT3jnAR5ihFqF3kJA5YZN1i</code> If you want to send to a one time address please email us at: <a href="mailto:donations@novaramedia.com?subject=BTC donation">donations@novaramedia.com</a> </p>
        </div>
      </div>
    </div>
  <!-- end post -->
  </article>
    <?php
  }
}
?>
<!-- end main-content -->
</main>

<?php
get_footer();
?>
