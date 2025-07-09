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
    $header_first_line = ! empty( $meta['_nm_support_header_first_line'] ) ? $meta['_nm_support_header_first_line'][0] : '';
    $header_second_line = ! empty( $meta['_nm_support_header_second_line'] ) ? $meta['_nm_support_header_second_line'][0] : '';
    $how_we_are_funded_heading = ! empty( $meta['_nm_support_how_we_are_funded_heading'] ) ? $meta['_nm_support_how_we_are_funded_heading'][0] : '';
    $how_we_are_funded_text = ! empty( $meta['_nm_support_how_we_are_funded_text'] ) ? $meta['_nm_support_how_we_are_funded_text'][0] : '';
    $how_we_spend_our_funds_heading = ! empty( $meta['_nm_support_how_we_spend_our_funds_heading'] ) ? $meta['_nm_support_how_we_spend_our_funds_heading'][0] : '';
    $how_we_spend_our_funds_lines = ! empty( $meta['_nm_support_how_we_spend_our_funds_lines'] ) ? maybe_unserialize( $meta['_nm_support_how_we_spend_our_funds_lines'][0] ) : array();
    ?>
  <article id="page" class="support-page">
    <div class="background-white background-support-texture-alt--fade-to-white pb-6">
      <div class="container">
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
        <!-- heading -->
        <div class="flex-grid-row pt-2 pb-2 font-weight-bold font-size-s-14 font-size-17">
            <div class="font-color-white">
              <?php
              if ( ! empty( $header_first_line ) ) {
                echo $header_first_line;
              } else {
                echo 'Beat the billionaires.';
              }
              ?>
              </div>
            <div class="font-color-black">
              <?php
              if ( ! empty( $header_second_line ) ) {
                echo $header_second_line;
              } else {
                echo 'Unfuck the media.';
              }
              ?>
              </div>
        </div>
      </div>
      <!-- left column -->
      <div class="container">
        <div class="flex-grid-row" style="position: relative;">
          <div class="flex-grid-item support-page__grid-item flex-item-l-6 flex-item-xxl-6 flex-item-s-12 background-white text-copy font-serif support-page__left-radius">
            <div class="m-5 m-s-0 font-size-13 font-serif">
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
          <!-- right column -->
          <div class="flex-grid-item support-page__grid-item flex-item-l-6 flex-item-xxl-6 flex-item-s-12 background-gray-base support-page__right-radius">
              <div class="support-page__donation-form-sticky mb-4">
                   <?php render_support_form_dispatcher( 'condensed' ); ?>
              </div>
          </div>
        </div>
      </div>
    </div>

    <div class="background-gray-base">
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
      <div class="container">
        <div class="grid-row pt-5 pb-6 support-page__text-container ui-border-top ui-border--black">
          <div class="text-uppercase p-2 background-black font-color-white text-align-center font-size-9">
            <?php
            if ( ! empty( $how_we_are_funded_heading ) ) {
              echo $how_we_are_funded_heading;
            } else {
              echo 'How we are funded';
            }
            ?>
            </div>
          <br/>
          <div class="support-page__infographic"></div>
          <br/>
          <div class="font-weight-bold is-xl-13 text-align-center font-size-12">
            <?php
            if ( ! empty( $how_we_are_funded_text ) ) {
              echo $how_we_are_funded_text;
            } else {
              echo 'Because the vast majority of our income is raised directly from supporters, we can be editorially independent without ever having to toe someone else’s editorial line. It’s a key principle that has always underpinned our funding model.';
            }
            ?>
        </div>
      </div>

      <!-- how we spend our funds -->
      <div class="container mb-6 pb-6">
        <div class="grid-row pt-6 pb-5 support-page__text-container background-white ui-rounded-box-large">
          <div class="text-uppercase p-2 background-black font-color-white text-align-center font-size-9 mb-5">
             <?php
              if ( ! empty( $how_we_spend_our_funds_heading ) ) {
                echo $how_we_spend_our_funds_heading;
              } else {
                echo 'How we spend our funds';
              }
              ?>
            </div>
            <div class="ux-highlighter is-xl-13 text-align-center font-size-13 font-size-s-12 font-weight-bold">
            <?php
            if ( ! empty( $how_we_spend_our_funds_lines ) && is_array( $how_we_spend_our_funds_lines ) ) {
              foreach ( $how_we_spend_our_funds_lines as $line ) {
                if ( ! empty( $line['text'] ) ) {
                  $color_class = 'font-color-gray-light';
                  // Make the first line black as in your original markup
                  if ( $line === reset( $how_we_spend_our_funds_lines ) ) {
                    $color_class = 'font-color-black';
                  }
                  echo '<div class="ux-highlighter__line mb-5 ' . esc_attr( $color_class ) . '">'
                      . esc_html( $line['text'] )
                      . '</div>';
                }
              }
            } else {
              // fallback hardcoded lines
              ?>
               <div class="ux-highlighter__line mb-5 font-color-black">Every penny Novara Media makes goes back into our journalism.</div>
              <div class="ux-highlighter__line mb-5 font-color-gray-light">Your support pays for the hours it takes to research and meticulously check the claims in our articles.</div>
              <div class="ux-highlighter__line mb-5 font-color-gray-light">It pays for the studio space where we film our live show.</div>
              <div class="ux-highlighter__line mb-5 font-color-gray-light">It allows us to hire key roles, like a labour movement correspondent.</div>
              <div class="ux-highlighter__line mb-5 font-color-gray-light">It helps us fight (and win) against the smears of the rightwing press.</div>
              <div class="ux-highlighter__line font-color-gray-light">Above all, it lets us break stories and challenge the establishment in ways mainstream media just won’t.</div>
              <?php
            }
            ?>
        </div>
        </div>
      </div>

      <!-- our story -->
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
      <div class="container pb-6 pb-s-0 pt-5 pt-s-0 mb-6 ">
        <div class="grid-row support-page__text-container support-page__our-story-background ui-rounded-box-large pt-6 pb-6">
          <div class="text-uppercase font-weight-bold p-2 background-white font-color-black text-align-center font-size-9 mb-7"> Our story </div>
          <div class="is-xl-13 font-color-white flex-grid-item flex-item-s-10 text-align-left pl-s-3 pr-s-3">
            <div class="font-weight-bold mb-4 font-size-12 ">
              Novara Media has grown from a humble radio show in 2011 to one of Britain’s most influential independent media organizations. Born amid anti-austerity movements with nothing but passion, we've consistently punched above our weight in the national conversation.
            </div>
            <div class="font-size-11 pb-5">
              From our breakthrough coverage during the 2017 General Election to our vital reporting during the COVID-19 pandemic and on Israel's actions in Gaza, we've remained committed to principled journalism that centers overlooked voices and stories. With your support, we can continue expanding our investigative capacity and building media that truly addresses the challenges of our time. Every contribution helps us maintain our independence in a landscape dominated by powerful interests.
            </div>
          </div>
      </div>
    </div>

    <!-- carousel -->
    <section class="ux-carousel pb-6 pt-6 mb-6 mb-s-0 alt">
      <div class="swiper">
        <div class="swiper-wrapper">
          <!-- Slide 1 -->
          <div class="swiper-slide alt-ux-carousel__item text-align-center p-5 ui-rounded-box-large">
            <h5 class="font-weight-bold mb-5 text-uppercase font-weight-bold font-color-black font-size-9">Supporters Say</h5>
            <img class="support-page__quote-mark mb-2" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/quote-mark.png" alt="big quote mark" />
            <p class="font-serif font-size-12">Short description here</p>
          </div>
          <!-- Slide 2 -->
          <div class="swiper-slide alt-ux-carousel__item text-align-center p-5 ui-rounded-box-large">
              <h5 class="font-weight-bold mb-5 text-uppercase font-weight-bold font-color-black font-size-9">Supporters Say</h5>
              <img class="support-page__quote-mark mb-2" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/quote-mark.png" alt="big quote mark" />
              <p class="font-serif font-size-12">Short description here more words to see whhat it looks like. j jncsiu njwepfjnc wejf c wejf</p>
          </div>
          <!-- Slide 3 -->
          <div class="swiper-slide alt-ux-carousel__item text-align-center p-5 ui-rounded-box-large">
            <h5 class="font-weight-bold mb-5 text-uppercase font-weight-bold font-color-black font-size-9">Supporters Say</h5>
            <img class="support-page__quote-mark mb-2" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/quote-mark.png" alt="big quote mark" />
            <p class="font-serif font-size-12">Short description here</p>
          </div>
          <!-- Slide 4 -->
           <div class="swiper-slide alt-ux-carousel__item text-align-center p-5 ui-rounded-box-large">
            <h5 class="font-weight-bold mb-5 text-uppercase font-weight-bold font-color-black font-size-9">Supporters Say</h5>
            <img class="support-page__quote-mark mb-2" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/quote-mark.png" alt="big quote mark" />
            <p class="font-serif font-size-12">Short description here</p>
          </div>
        </div>
      <div class="swiper-pagination"></div>
      </div>
    </section>

    <!-- donation form -->
    <div class="container">

    </div>

    <div id="other-donation-methods" class="container">
      <!-- already a supporter -->
      <div class="flex-grid-row pt-6 pb-6">
        <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-6 mb-4 support-page__grid-item">
          <h4 class="font-size-11 font-weight-bold mb-3">Already a supporter?</h4>
          <?php
          if ( ! empty( $meta['_cmb_page_extra'] ) ) {
            echo apply_filters( 'the_content', $meta['_cmb_page_extra'][0] );
          }
          ?>
          <p class="mt-4"><a href="https://donate.novaramedia.com/login" class="ui-button ui-button--red ui-button--small mb-s-5">Log in to your account</a></p>
        </div>

        <!-- Other donation methods -->
        <div class="flex-grid-item flex-item-xxl-6 flex-item-s-12 support-page__grid-item">
          <div class="flex-grid-item flex-item-s-12 flex-item-l-12 flex-item-s-12 mb-5 mb-s-4 support-page__grid-item">
            <h4 class="font-size-11 font-weight-bold mb-3">Other Donation Methods</h4>
            <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have options for PayPal or UK Direct Debit.</p>
          </div>

          <div class="flex-grid-row">
            <!-- paypal -->
            <div class="flex-grid-item flex-item-l-6 flex-item-xxl-6 flex-item-s-12 support-page__grid-item">
              <img class="support-page__paypal-logo mb-3" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/support-logo-paypal.svg" alt="PayPal logo" />
              <p>You can donate to us via PayPal. You can set a recurring donation or just give a one-off for any amount.</p>
              <p><a class="mt-3 ui-button ui-button--red ui-button--small mb-m-5 mb-s-4" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Donate to us via PayPal</a></p>
            </div>

            <!-- GoCardless -->
            <div class="flex-grid-item flex-item-l-6 flex-item-xxl-6 flex-item-s-12 support-page__grid-item">
              <img class="support-page__direct-debit-logo mb-3" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/support-logo-directdebit.svg" alt="Direct Debit logo" />
              <p>You can donate to us via a UK Direct Debit regular bank transfer using the GoCardless platform.</p>
              <div class="mt-3 flex-grid-row flex-grid--nested-tight mb-3">
                <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6 flex-item-s-3 mb-3">
                  <a class="ui-button ui-button--red ui-button--small" href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5/mo</a>
                </div>
                <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6 flex-item-s-3">
                <a class="ui-button ui-button--red ui-button--small" href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10/mo</a>
                </div>
                <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6 flex-item-s-3">
                  <a class="ui-button ui-button--red ui-button--small" href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20/mo</a>
                </div>
                <div class="flex-grid-item flex-grid-item--tight flex-item-xxl-6 flex-item-s-3">
                  <a class="ui-button ui-button--red ui-button--small mb-s-3" href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50/mo</a>
                </div>
              </div>
            </div>
          </div>
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
