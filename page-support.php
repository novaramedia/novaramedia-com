<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * The template for displaying the Support Us page.
 *
 * @package Novara_Media
 */

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

    // Get repeatable funds lines - properly unserialize CMB2 repeatable fields
    $how_we_spend_our_funds_lines = ! empty( $meta['_nm_support_funds_lines'] ) ? maybe_unserialize( $meta['_nm_support_funds_lines'][0] ) : array();
    // Ensure it's an array and filter out empty lines
    $how_we_spend_our_funds_lines = is_array( $how_we_spend_our_funds_lines ) ? array_filter( $how_we_spend_our_funds_lines ) : array();

    // Get repeatable carousel quotes - properly unserialize CMB2 repeatable fields
    $support_carousel_quotes = ! empty( $meta['_nm_support_carousel_quotes'] ) ? maybe_unserialize( $meta['_nm_support_carousel_quotes'][0] ) : array(); // TODO: remove this meta as mutliple blocks means hardcoded for now
    $support_carousel_quotes = is_array( $support_carousel_quotes ) ? array_filter( $support_carousel_quotes ) : array();
    // Filter out empty quotes
    $support_carousel_quotes = array_filter( $support_carousel_quotes );
  }
  ?>
  <article id="page" class="support-page background-gray-base support-page__background-cover-image">
    <div class="container">
      <div class="grid-item">
        <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
          Support Us
        </h4>
      </div>
      <div class="grid-row mt-4 mb-6 mb-s-5 font-weight-bold">
        <div class="grid-item support-page__headline font-size-19 font-size-xl-18 font-size-l-17 font-size-s-16 text-wrap-balance">
          <div class="font-color-black">
            <?php
            if ( ! empty( $header_first_line ) ) {
              echo $header_first_line;
            } else {
              echo 'Help build a new media';
            }
            ?>
          </div>
          <div class="font-color-white">
            <?php
            if ( ! empty( $header_second_line ) ) {
              echo $header_second_line;
            } else {
              echo 'for a different politics.';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="grid-row u-relative">
        <div class="grid-item is-xxl-24 ">
          <div class="mb-4 only-mobile">
            <?php render_support_form( 'condensed' ); ?>
          </div>
          <div class="grid-row grid-row--nested only-mobile background-white ui-rounded-box">
            <div class="grid-item is-xxl-24">
              <div class="pt-4 pb-4 pl-2 pr-2 font-weight-bold text-wrap-balance">
                <div class="font-size-14 mb-4">
                  We’re up against <span class="font-color-red">huge power and influence.</span>
                </div>
                <div class="font-size-12 mb-4">
                  Join our supporters and back <span class="font-color-red">truthful, independent journalism today.</span>
                </div>
                <?php
                if ( $youtube_id ) {
                  ?>
                <div class="u-video-embed-container">
                  <?php echo render_youtube_embed_iframe( $youtube_id ); ?>
                </div>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>

          <div class="grid-row grid-row--nested u-relative only-desktop background-white ui-rounded-box">
            <div class="grid-item is-xxl-12 is-m-24">
              <div class="p-5 p-l-4 pl-s-2 pr-s-2 font-weight-bold text-wrap-balance">
                <div class="font-size-16 font-size-xl-15 font-size-s-14 mb-4">
                  We’re up against <span class="font-color-red">huge power and influence.</span>
                </div>
                <div class="font-size-13 font-size-l-12 mb-4">
                  Join our supporters and back <span class="font-color-red">truthful, independent journalism today.</span>
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
        <?php
        if ( $youtube_id ) {
          ?>
        <div class="grid-item is-xxl-24 only-desktop">
          <div class="background-white ui-rounded-box ui-rounded-box--bottom pl-5 pl-l-4 pr-5 pr-l-4 pb-5 pb-l-4">
            <div class="u-video-embed-container">
            <?php echo render_youtube_embed_iframe( $youtube_id ); ?>
            </div>
          </div>
        </div>
          <?php
        }
        ?>
      </div>
    </div>

    <div class="support-page__below-the-fold">
      <!-- Block 1 -->
      <div class="container mt-5 mt-l-4 mb-5 mb-l-4">
        <div class="grid-row support-page__text-container">
          <div class="grid-item is-xxl-24 ">
            <div class="background-white ui-rounded-box p-5 p-l-4">
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-12">
                  <h3 class="font-size-16 font-size-l-15 font-size-s-14 font-weight-bold mb-s-4 mr-5 mr-s-0">
                    Billionaire-backed?<br/>
                    <span class="font-color-red">Not us.</span>
                  </h3>
                </div>
                <div class="grid-item is-s-24 is-xxl-12 font-size-12 font-weight-bold text-wrap-balance">
                  <p>Outlets funded by billionaires are obliged to push narratives that serve the obscenely wealthy.</p>
                  <p class="mt-3">Because the vast majority of our income is from our supporters, we can be editorially independent without ever having to toe someone else’s editorial line.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quotes 1 -->
          <?php
          $quotes_block_1 = array(
          'I support because I enjoy Ash Sarkar\'s writing, but also to support a media source which doesn\'t simply exist to amplify the concerns of the wealthy.',
          'In a time where misinformation is omnipresent, Novara Media provides much needed clarity and nuance.',
          'I support because you are one of the few media outlets that can claim independence as you are financed by members of the community and not corporations or individuals who will control and direct the narratives.',
          'It takes lots of little people to counter one Murdoch.',
          'I want a strong left-wing view so decided to support you properly with a bit of my own money.',
          'I have never supported anything financially really, but when you had your recent push for new backers it occurred to me that I consume Novara content more than that of other media providers I actually pay for. ',
          );
          ?>
      <section class="container support-page__quote-carousel ux-gallery-carousel mb-5" data-autoplay="true">
        <div class="swiper">
          <div class="swiper-wrapper">
          <?php foreach ( $quotes_block_1 as $quote ) { ?>
              <div class="swiper-slide text-align-center ui-rounded-box ui-rounded-box--large">
                <h5 class="ui-boxed-title ui-boxed-title--black mb-s-5">Supporters Say</h5>
                <div class="support-page__quote-container">
                  <div class="font-serif quote support-page__quote-mark text-align-center">“</div>
                  <p class="font-serif font-size-13 text-wrap-balance"><?php echo esc_html( $quote ); ?></p>
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </section>

      <!-- Block 2 -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="background-white ui-rounded-box p-5 p-l-4">
              <div class="text-align-center mb-6 mb-s-5">
                <h3 class="ui-boxed-title">People-powered Media</h3>
              </div>
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-12">
                  <h3 class="font-size-16 font-size-l-15 font-size-s-14 font-weight-bold mb-s-4 text-wrap-balance">
                    Our <span class="font-color-red">supporter-funded model</span> continues to defy expectations and buck industry trends.
                  </h3>
                </div>
                <div class="grid-item is-s-24 is-xxl-12 font-size-12 font-weight-bold text-wrap-balance">
                  <p>You might think giving a small amount per month won’t have much impact.</p>
                  <p class="mt-3">But many people chipping in builds a sturdy foundation.</p>
                  <p class="mt-3">Having lots of small, regular donations means we don’t waste valuable time pitching for highly competitive and admin-heavy grants. Plus, no large funder can pull the rug from underneath us and threaten the whole organisation.</p>
                  <p class="mt-3">And we never, ever accept funding that threatens our editorial independence.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- donation form -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
          <?php render_support_form( 'banner', true ); ?>
          </div>
        </div>
      </div>

      <!-- Block 3 -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="background-white ui-rounded-box p-5 p-l-4">
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-10">
                  <h3 class="font-size-16 font-size-l-15 font-size-s-14 font-weight-bold mb-s-4 text-wrap-balance">
                    No ads, no distractions.<br/>
                    <span class="font-color-red">Just quality journalism.</span>
                  </h3>
                </div>
                <div class="grid-item offset-s-0 is-s-24 offset-xxl-2 is-xxl-12 font-size-12 font-weight-bold">
                  <p>Paywalls prevent ordinary people from accessing the news. Many outlets have you dodging obnoxious pop ups and whack-a-mole-ing ads left, right and centre.</p>
                  <p class="mt-3">But we don’t have any ad partnerships or sponsored content. We don’t ask for your data to read an article in full. Absolutely none of our content is behind a paywall.</p>
                  <p class="mt-3">Thanks to our supporters, we’re one of a tiny number of outlets that are entirely free for all to access. </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quotes 3 -->
          <?php
          $quotes_block_3 = array(
          'We need bodies such as Novara to really challenge ideas and make sure that we are supporting policies that can really work. Economics can be complex (at least to me) so Novara can really play a role in providing accessible information around valid and workable ideas that people can get behind.',
          'I give a small amount to help ensure access to a different perspective and, hopefully, it will help ensure you don’t have to compromise your content.',
          'Novara is essential. We need an independent, honest media environment with robust journalism to have any hope of a just and visionary society. I\'m supporting because I hope it continues to grow.',
          'We need bodies such as Novara to really challenge ideas and make sure that we are supporting policies that can really work. Economics can be complex (at least to me) so Novara can really play a role in providing accessible information around valid and workable ideas that people can get behind.',
          'I give a small amount to help ensure access to a different perspective and, hopefully, it will help ensure you don’t have to compromise your content.',
          'Novara is essential. We need an independent, honest media environment with robust journalism to have any hope of a just and visionary society. I\'m supporting because I hope it continues to grow.',
          );
          ?>
      <section class="container support-page__quote-carousel ux-gallery-carousel mb-5" data-autoplay="true">
        <div class="swiper">
          <div class="swiper-wrapper">
          <?php foreach ( $quotes_block_2 as $quote ) { ?>
              <div class="swiper-slide text-align-center ui-rounded-box ui-rounded-box--large">
                <h5 class="ui-boxed-title ui-boxed-title--black mb-s-5">Supporters Say</h5>
                <div class="support-page__quote-container">
                  <div class="font-serif quote support-page__quote-mark text-align-center">“</div>
                  <p class="font-serif font-size-13 text-wrap-balance"><?php echo esc_html( $quote ); ?></p>
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </section>

      <!-- our story -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="grid-row grid-row--nested background-black ui-rounded-box ui-rounded-box--top">
              <div class="grid-item is-xxl-24 support-page__our-story-background ui-rounded-box ui-rounded-box--top"></div>
            </div>
            <div class="grid-row grid-row--nested p-6 p-l-5 p-s-4 pt-s-5 pb-s-5 background-black font-color-white ui-rounded-box ui-rounded-box--bottom">
              <div class="grid-item is-xxl-24 text-align-center mb-6 mb-s-5">
                <h3 class="ui-boxed-title ui-boxed-title--white">
                  Our Story
                </h3>
              </div>
              <div class="grid-item is-s-24 is-xxl-11">
                <h3 class="font-size-14 font-size-l-13 font-weight-bold mb-s-4 text-wrap-balance">
                  Born amid anti-austerity movements as a show on community radio, our supporters are the reason we’ve grown to be one of Britain’s most influential media organisations.
                </h3>
              </div>
              <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-11 font-weight-bold text-wrap-pretty">
                <p class="mb-4">
                  From our breakthrough coverage during the 2017 General Election to our vital reporting during the COVID-19 pandemic and on Israel's actions in Gaza, Novara Media cuts through the bullshit to provide clear, rigorous analysis, drawing hundreds of thousands of viewers, readers and listeners each and every day.
                </p>
                <p class="mb-4">
                  While mainstream media exist to serve the ultra-wealthy, Novara Media stays committed to uncovering the truth to report on what it takes to build a society that works for everybody.
                </p>
                <p class="mb-4">
                  Funded by our audience, the Novara team is now 25 people-strong, working tirelessly to provide independent, thorough analysis you just can’t find anywhere else.
                </p>
                <p>
                  Novara Media is a not-for-profit organisation. We don’t have any shareholders, so the funds we raise from all income streams go directly into supporting our journalism and creating new content.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- donation form -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
          <?php render_support_form( 'banner', true ); ?>
          </div>
        </div>
      </div>

    <div id="other-donation-methods" class="container">
      <!-- already a supporter -->
      <div class="grid-row mt-5">
        <div class="grid-item is-s-24 is-xxl-12 mb-4">
          <h4 class="font-size-13 font-size-s-14 font-weight-bold mb-3">Already a supporter?</h4>
          <p>Are you able to increase your monthly donation? Increasing by just £2 or £3 helps strengthen funding base and makes us even more secure for the future.</p>
          <p class="mt-4"><a href="https://donate.novaramedia.com/login" class="ui-button ui-button--red ui-button--small">Log in to your account</a></p>
        </div>

        <!-- Other donation methods -->
        <div class="grid-item is-s-24 is-xxl-12">
          <div class="mb-5 mb-s-4">
            <h4 class="font-size-13 font-size-s-14 font-weight-bold mb-3">Other Donation Methods</h4>
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
  </article>
  <?php
}
?>
</main>
<?php
get_footer();
?>
