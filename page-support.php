<?php
get_header();
?>
<main id="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );

    $youtube_id = ! empty( $meta['_nm_support_youtube'] ) ? $meta['_nm_support_youtube'][0] : false;
    $header_first_line = ! empty( $meta['_nm_support_header_first_line'] ) ? $meta['_nm_support_header_first_line'][0] : '';
    $header_second_line = ! empty( $meta['_nm_support_header_second_line'] ) ? $meta['_nm_support_header_second_line'][0] : '';
    $how_we_are_funded_heading = ! empty( $meta['_nm_support_how_we_are_funded_heading'] ) ? $meta['_nm_support_how_we_are_funded_heading'][0] : '';
    $how_we_are_funded_text = ! empty( $meta['_nm_support_how_we_are_funded_text'] ) ? $meta['_nm_support_how_we_are_funded_text'][0] : '';
    $how_we_spend_our_funds_heading = ! empty( $meta['_nm_support_how_we_spend_our_funds_heading'] ) ? $meta['_nm_support_how_we_spend_our_funds_heading'][0] : '';

    // Get repeatable funds lines
    $how_we_spend_our_funds_lines = ! empty( $meta['_nm_support_funds_lines'] ) ? $meta['_nm_support_funds_lines'][0] : array();
    // Filter out empty lines
    $how_we_spend_our_funds_lines = array_filter( $how_we_spend_our_funds_lines );

    $our_story_heading = ! empty( $meta['_nm_support_our_story_heading'] ) ? $meta['_nm_support_our_story_heading'][0] : '';
    $our_story_bold_text = ! empty( $meta['_nm_support_our_story_bold_text'] ) ? $meta['_nm_support_our_story_bold_text'][0] : '';
    $our_story_regular_text = ! empty( $meta['_nm_support_our_story_regular_text'] ) ? $meta['_nm_support_our_story_regular_text'][0] : '';

    // Get repeatable carousel quotes
    $support_carousel_quotes = ! empty( $meta['_nm_support_carousel_quotes'] ) ? $meta['_nm_support_carousel_quotes'][0] : array();
    // Filter out empty quotes
    $support_carousel_quotes = array_filter( $support_carousel_quotes );
  }
  ?>
  <article id="page" class="support-page">
    <div class="background-support-texture-alt background-support-texture-alt--fade-to-gray">
      <div class="container">
        <div class="grid-item">
          <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
            Support Us
          </h4>
        </div>
        <div class="grid-row mt-4 mb-6 font-weight-bold">
          <div class="grid-item support-page__headline font-size-17">
            <div class="font-color-black">
              <?php
              if ( ! empty( $header_first_line ) ) {
                echo $header_first_line;
              } else {
                echo 'No owners.';
              }
              ?>
            </div>
            <div class="font-color-white">
              <?php
              if ( ! empty( $header_second_line ) ) {
                echo $header_second_line;
              } else {
                echo 'Only supporters.';
              }
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="grid-row u-relative">
          <div class="grid-item is-xxl-24">
            <div class="grid-row grid-row--nested u-relative background-white ui-rounded-box--top">
              <div class="grid-item is-xxl-12 is-m-24">
                <div class="p-5 p-l-4 font-weight-bold">
                  <div class="font-size-16 font-size-xl-15 mb-4">
                    Truthful, independent journalism <span class="font-color-red">funded by people like you.</span>
                  </div>
                  <div class="font-size-13 font-size-l-12 mb-4">
                    Outside the manufactured narratives that serve the rich and powerful, we are building <span class="font-color-red">a new media for different politics.</span>
                  </div>
                  <div class="font-size-13 font-size-l-12 mb-5">
                    Be part of the change.
                  </div>
                </div>
              </div>
              <div class="grid-item is-xxl-12 is-m-24">
                <div class="support-page__donation-form-sticky p-5 p-l-4">
                  <?php render_support_form( 'condensed' ); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="grid-item is-xxl-24">
            <div class="background-white ui-rounded-box--bottom pl-5 pl-l-4 pr-5 pr-l-4 pb-5 pb-l-4">
            <?php
            if ( $youtube_id ) {
              ?>
              <div class="u-video-embed-container">
                <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url( $youtube_id ); ?>"></iframe>
              </div>
                <?php
            }
            ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="support-page__below-the-fold background-gray-base">
      <!-- how we are funded -->
      <div class="container mt-5 mt-l-4 mb-5 mb-l-4">
        <div class="grid-row support-page__text-container">
          <div class="grid-item is-xxl-24 ">
            <div class="background-red ui-rounded-box--top text-align-center p-5 p-l-4">
              <h3 class="ui-boxed-title ui-boxed-title--grey">How we are funded</h3>

              <div class="support-page__big-stats mt-4 mb-5">
                <div class="grid-row grid-row--nested">
                  <div class="grid-item is-xxl-8">
                    <div class="support-page__big-stat">
                      <div class="font-weight-bold font-color-white">
                        <span class="font-size-17">84</span><span class="font-size-15 font-size-s-14">%</span>
                      </div>
                      <div class="font-size-13 font-size-s-11 font-weight-bold">Supporter donations</div>
                    </div>
                  </div>
                  <div class="grid-item is-xxl-8">
                    <div class="support-page__big-stat">
                      <div class="font-weight-bold font-color-white">
                        <span class="font-size-17">16</span><span class="font-size-15 font-size-s-14">%</span>
                      </div>
                      <div class="font-size-13 font-size-s-11 font-weight-bold">YouTube + Merch</div>
                    </div>
                  </div>
                  <div class="grid-item is-xxl-8">
                    <div class="support-page__big-stat">
                      <div class="font-weight-bold">
                        <span class="font-size-17">0</span><span class="font-size-15 font-size-s-14">%</span>
                      </div>
                      <div class="font-size-13 font-size-s-11 font-weight-bold">Murdoch</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="background-white ui-rounded-box--bottom p-5 p-l-4">
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-12">
                  <h3 class="font-size-16 font-size-l-15 font-weight-bold mb-s-4 mr-5 mr-s-0">
                    No Paywalls.<br/>
                    <span class="font-color-red">No Paymasters.</span>
                  </h3>
                </div>
                <div class="grid-item is-s-24 is-xxl-12 font-size-12 font-weight-bold">
                  <p>Because the vast majority of our income is raised directly from supporters, we can be editorially independent without ever having to toe someone else’s editorial line.</p>
                  <p class="mt-3">It’s a key principle that has <em>always</em> underpinned our funding model.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- how we spend our funds -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="background-white ui-rounded-box p-5 p-l-4">
              <div class="text-align-center mb-6">
                <h3 class="ui-boxed-title">How we spend our funds</h3>
              </div>
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-10">
                  <h3 class="font-size-16 font-size-l-15 font-weight-bold mb-s-4">
                    <span class="font-color-red">Every penny</span> Novara Media makes <span class="font-color-red">goes back into our journalism.</span>
                  </h3>
                </div>
                <div class="grid-item offset-s-0 is-s-24 offset-xxl-2 is-xxl-12 font-size-12 font-weight-bold font-color-gray">
                  <div class="support-page__highlighter ux-highlighter">
                  <?php
                  if ( ! empty( $how_we_spend_our_funds_lines ) ) {
                    foreach ( $how_we_spend_our_funds_lines as $index => $line_text ) {
                      echo '<div class="ux-highlighter__line">'
                        . esc_html( $line_text )
                        . '</div>';
                    }
                  } else {
                    ?>
                    <div class="ux-highlighter__line">Your support pays for the hours it takes to research and meticulously check the claims in our articles.</div>
                    <div class="ux-highlighter__line">It pays for the studio space where we film our live show.</div>
                    <div class="ux-highlighter__line">It allows us to hire key roles, like a labour movement correspondent.</div>
                    <div class="ux-highlighter__line">It helps us fight (and win) against the smears of the rightwing press.</div>
                    <div class="ux-highlighter__line">Above all, it lets us break stories and challenge the establishment in ways mainstream media just won’t.</div>
                    <?php
                  }
                  ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- our story -->
      <div class="container mb-6">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="grid-row grid-row--nested background-black">
              <div class="grid-item is-xxl-24 support-page__our-story-background ui-rounded-box--top"></div>
            </div>
            <div class="grid-row grid-row--nested p-6 p-l-5 background-black font-color-white ui-rounded-box--bottom">
              <div class="grid-item is-xxl-24 text-align-center mb-6">
                <h3 class="ui-boxed-title ui-boxed-title--white">
                  Our Story
                </h3>
              </div>
              <div class="grid-item is-s-24 is-xxl-11">
                <h3 class="font-size-14 font-size-l-13 font-weight-bold mb-s-4">
                  Novara Media has grown from a humble radio show in 2011 to one of Britain’s most influential independent media organizations.
                </h3>
              </div>
              <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-11 font-weight-bold">
                <p class="mb-4">
                  Born amid anti-austerity movements with nothing but passion, we've consistently punched above our weight in the national conversation.
                </p>
                <p class="mb-4">
                  From our breakthrough coverage during the 2017 General Election to our vital reporting during the COVID-19 pandemic and on Israel's actions in Gaza, we've remained committed to principled journalism that centers overlooked voices and stories.
                </p>
                <p class="mb-4">
                  With your support, we can continue expanding our investigative capacity and building media that truly addresses the challenges of our time.
                </p>
                <p>
                  Every contribution helps us maintain our independence in a landscape dominated by powerful interests.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

    <?php
    $max_quotes = 8;
    // Set fallback quotes
    $fallback_quotes = array(
      'Novara tells the stories others won’t.',
      'Independent journalism is vital — and Novara leads the way.',
      'Supporting Novara feels like action, not charity.',
      'They speak truth to power. That’s why I give.',
      'Novara’s journalism is fearless and uncompromising.',
      'I trust Novara to tell it like it is.',
      'I support Novara because they challenge the status quo.',
    );
    // Merge stored quotes with fallback (max 4)
    $all_quotes = array_merge( $support_carousel_quotes, array_slice( $fallback_quotes, 0, max( 0, $max_quotes - count( $support_carousel_quotes ) ) ) );
    ?>
    <!-- carousel -->
    <section class="container support-page__quote-carousel ux-gallery-carousel mb-6" data-autoplay="true">
      <div class="support-page__quote-carousel-fade-left"></div>
      <div class="support-page__quote-carousel-fade-right"></div>
      <div class="swiper">
        <div class="swiper-wrapper">
          <?php foreach ( $all_quotes as $quote ) { ?>
            <div class="swiper-slide text-align-center ui-rounded-box-large">
              <h5 class="ui-boxed-title ui-boxed-title--grey">Supporters Say</h5>
              <div class="support-page__quote-container">
                <div class="font-serif quote support-page__quote-mark text-align-center">“</div>
                <p class="font-serif font-size-13 font-size-s-12"><?php echo esc_html( $quote ); ?></p>
              </div>
            </div>
          <?php } ?>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </section>

    <!-- donation form -->
    <div class="container">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
        <?php render_support_form( 'banner', true ); ?>
      </div>
    </div>

    <div id="other-donation-methods" class="container">
      <!-- already a supporter -->
      <div class="grid-row mt-5">
        <div class="grid-item is-s-24 is-xxl-12 mb-4">
          <h4 class="font-size-13 font-weight-bold mb-3">Already a supporter?</h4>
          <p>Are you able to increase your monthly donation? Increasing by just £2 or £3 helps strengthen funding base and makes us even more secure for the future.</p>
          <p class="mt-4"><a href="https://donate.novaramedia.com/login" class="ui-button ui-button--red ui-button--small mb-s-5">Log in to your account</a></p>
        </div>

        <!-- Other donation methods -->
        <div class="grid-item is-s-24 is-xxl-12">
          <div class="mb-5 mb-s-4">
            <h4 class="font-size-13 font-weight-bold mb-3">Other Donation Methods</h4>
            <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our website, however we also have options for PayPal or UK Direct Debit.</p>
          </div>

          <div class="grid-row grid--nested">
            <!-- paypal -->
            <div class="grid-item is-xxl-12 is-s-24">
              <img class="support-page__paypal-logo mb-3" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/support-logo-paypal.svg" alt="PayPal logo" />
              <p>You can donate to us via PayPal. You can set a recurring donation or just give a one-off for any amount.</p>
              <p><a class="mt-3 ui-button ui-button--red ui-button--small mb-m-5 mb-s-4" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3R58SXSEWNAKE&source=url" target="_blank" rel="noopener">Donate to us via PayPal</a></p>
            </div>

            <!-- GoCardless -->
            <div class="grid-item is-xxl-12 is-s-24">
              <img class="support-page__direct-debit-logo mb-3" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/support-logo-directdebit.svg" alt="Direct Debit logo" />
              <p>You can donate to us via a UK Direct Debit regular bank transfer using the GoCardless platform.</p>
              <div class="mt-3 mb-3">
                <a class="ui-button ui-button--red ui-button--small mr-2 mb-3" href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5/mo</a>
                <a class="ui-button ui-button--red ui-button--small mr-2 mb-3" href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10/mo</a>
                <br/>
                <a class="ui-button ui-button--red ui-button--small mr-2 mb-3" href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20/mo</a>
                <a class="ui-button ui-button--red ui-button--small mb-3" href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50/mo</a>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  </article>
    <?php
}
?>
</main>

<?php
get_footer();
?>
