<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * The template for displaying the Support Us page.
 *
 * @package Novara_Media
 */

$quotes_block_1 = array(
  'I support because I enjoy Ash Sarkar\'s writing, but also to support a media source which doesn\'t simply exist to amplify the concerns of the wealthy.',
  'In a time where misinformation is omnipresent, Novara Media provides much needed clarity and nuance.',
  'I support because you are one of the few media outlets that can claim independence as you are financed by members of the community and not corporations or individuals who will control and direct the narratives.',
  'It takes lots of little people to counter one Murdoch.',
  'I want a strong left-wing view so decided to support you properly with a bit of my own money.',
  'I have never supported anything financially really, but when you had your recent push for new backers it occurred to me that I consume Novara content more than that of other media providers I actually pay for. ',
);

$quotes_block_2 = array(
  'We need bodies such as Novara to really challenge ideas. Economics can be complex (at least to me) so Novara can really play a role in providing accessible information.',
  'I give a small amount to help ensure access to a different perspective and, hopefully, it will help ensure you don\'t have to compromise your content.',
  'Novara is essential. We need an independent, honest media environment with robust journalism to have any hope of a just and visionary society. I\'m supporting because I hope it continues to grow.',
  'We need bodies such as Novara to really challenge ideas. Economics can be complex (at least to me) so Novara can really play a role in providing accessible information.',
  'I give a small amount to help ensure access to a different perspective and, hopefully, it will help ensure you don\'t have to compromise your content.',
  'Novara is essential. We need an independent, honest media environment with robust journalism to have any hope of a just and visionary society. I\'m supporting because I hope it continues to grow.',
);

get_header();
?>
<main id="main-content" data-testid="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );

    $youtube_short_id = ! empty( $meta['_nm_support_youtube_short'] ) ? $meta['_nm_support_youtube_short'][0] : false;
    $youtube_long_id  = ! empty( $meta['_nm_support_youtube_long'] ) ? $meta['_nm_support_youtube_long'][0] : false;
    $header_first_line = ! empty( $meta['_nm_support_header_first_line'] ) ? $meta['_nm_support_header_first_line'][0] : '';
    $header_second_line = ! empty( $meta['_nm_support_header_second_line'] ) ? $meta['_nm_support_header_second_line'][0] : '';

    // Get repeatable funds lines - properly unserialize CMB2 repeatable fields
    $how_we_spend_our_funds_lines = ! empty( $meta['_nm_support_funds_lines'] ) ? maybe_unserialize( $meta['_nm_support_funds_lines'][0] ) : array();
    // Ensure it's an array and filter out empty lines
    $how_we_spend_our_funds_lines = is_array( $how_we_spend_our_funds_lines ) ? array_filter( $how_we_spend_our_funds_lines ) : array();
  }
  ?>
  <article id="page" class="support-page background-gray-base support-page__background-cover-image" data-testid="support-page">
    <div class="container">
      <div class="grid-item">
        <h4 class="font-size-9 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
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
              echo 'We need to get bigger.';
            }
            ?>
          </div>
          <div class="font-color-white">
            <?php
            if ( ! empty( $header_second_line ) ) {
              echo $header_second_line;
            } else {
              echo 'Much bigger.';
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
                  The next few years are critical. We need to <span class="font-color-red">make more space for the left.</span>
                </div>
                <div class="font-size-12 mb-4">
                  Help us grow our supporter base by <span class="font-color-red">2000 people.</span> Use your card or scroll down to set up a direct debit.
                </div>
              </div>
            </div>
          </div>

          <div class="grid-row grid-row--nested u-relative only-desktop background-white ui-rounded-box">
            <div class="grid-item is-xxl-12 is-m-24">
              <div class="ui-backgrounded-box-padding pl-s-2 pr-s-2 font-weight-bold text-wrap-balance">
                <div class="font-size-16 font-size-xl-15 font-size-s-14 mb-4">
                  The next few years are critical. We need to <span class="font-color-red">make more space for the left.</span>
                </div>
                <div class="font-size-13 font-size-l-12 mb-4 mb-m-0">
                  Help us grow our supporter base by <span class="font-color-red">2000 people.</span> Use your card or scroll down to set up a direct debit.
                </div>
              </div>
            </div>
            <div class="grid-item is-xxl-12 is-m-24">
              <div class="support-page__donation-form-sticky ui-backgrounded-box-padding">
                <?php render_support_form( 'condensed' ); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="support-page__below-the-fold">
      <!-- Block 1 -->
      <div class="container mt-5 mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="grid-row grid-row--nested background-black ui-rounded-box ui-rounded-box--top">
              <?php if ( $youtube_short_id ) { ?>
              <div class="grid-item is-xxl-24 ui-rounded-box ui-rounded-box--top">
                <div class="pt-4 pl-2 pr-2">
                  <div class="u-video-embed-container">
                    <?php echo render_youtube_embed_iframe( $youtube_short_id, false, 'eager', get_the_title() ); ?>
                  </div>
                </div>
              </div>
              <?php } else { ?>
              <div class="grid-item is-xxl-24 support-page__invert-section-background support-page__crowd-background ui-rounded-box ui-rounded-box--top"></div>
              <?php } ?>
            </div>
            <div class="grid-row grid-row--nested ui-backgrounded-box-padding background-black font-color-white ui-rounded-box ui-rounded-box--bottom">
              <div class="grid-item is-s-24 is-xxl-11">
                <h3 class="font-size-14 font-size-s-13 font-weight-bold mb-s-4 text-wrap-balance">
                  To protect their power, elites are throwing their money into media that makes leftwing politics seem impossible.
                </h3>
              </div>
              <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-12 font-size-s-11 font-weight-bold text-wrap-pretty">
                <p>We’re expanding ahead of the next general election, and moving to a new space.</p>
                <p class="mt-3">Take a look at what we’ve got planned.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quotes 1 -->
      <?php render_support_quotes_carousel( $quotes_block_1 ); ?>

      <!-- Direct debit block -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="background-red ui-rounded-box ui-backgrounded-box-padding font-color-white">
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-11">
                  <img class="mb-4" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/support-logo-directdebit.svg" alt="Direct Debit" style="filter: brightness(0) invert(1); height: 2em;" />
                  <h3 class="font-size-13 font-size-l-12 font-size-s-11 font-weight-bold text-wrap-balance">
                    In the UK? Set up a direct debit today.
                  </h3>
                </div>
                <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-12 font-size-s-11 font-weight-bold">
                  <div class="mt-s-3">
                    <div class="mb-3">
                      <a class="ui-button ui-button--white mr-3" href="https://pay.gocardless.com/AL00033222M0PQ" target="_blank" rel="noopener">£5 per month</a>
                      <a class="ui-button ui-button--white" href="https://pay.gocardless.com/AL00033226P4MM" target="_blank" rel="noopener">£10 per month</a>
                    </div>
                    <div>
                      <a class="ui-button ui-button--white mr-3" href="https://pay.gocardless.com/AL00033228M1D0" target="_blank" rel="noopener">£20 per month</a>
                      <a class="ui-button ui-button--white" href="https://pay.gocardless.com/AL00033229Y952" target="_blank" rel="noopener">£50 per month</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Block 2 -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="background-white ui-rounded-box ui-backgrounded-box-padding">
              <div class="text-align-center mb-6 mb-s-5">
                <h3 class="ui-boxed-title">People-powered Media</h3>
              </div>
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-12">
                  <h3 class="font-size-16 font-size-xl-15 font-size-s-14 font-weight-bold mb-s-4 text-wrap-balance">
                    Small, regular donations <span class="font-color-red">make us strong.</span>
                  </h3>
                </div>
                <div class="grid-item is-s-24 is-xxl-12 font-size-12 font-size-s-12 font-weight-bold text-wrap-balance">
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
            <div class="background-white ui-rounded-box ui-backgrounded-box-padding">
              <div class="grid-row grid-row--nested">
                <div class="grid-item is-s-24 is-xxl-10">
                  <h3 class="font-size-16 font-size-xl-15 font-size-s-14 font-weight-bold mb-s-4 text-wrap-balance">
                    No ads, no distractions.<br/>
                    <span class="font-color-red">Just quality journalism.</span>
                  </h3>
                </div>
                <div class="grid-item offset-s-0 is-s-24 offset-xxl-2 is-xxl-12 font-size-12 font-size-s-12 font-weight-bold">
                  <p>Paywalls prevent ordinary people from accessing the news. Many outlets have you dodging obnoxious pop ups and whack-a-mole-ing ads left, right and centre.</p>
                  <p class="mt-3">But we don’t have any ad partnerships or sponsored content. We don’t ask for your data to read an article in full. Absolutely none of our content is behind a paywall.</p>
                  <p class="mt-3">Thanks to our supporters, we’re one of a tiny number of outlets that are entirely free for all to access. </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quotes 2 -->
      <?php render_support_quotes_carousel( $quotes_block_2 ); ?>

      <?php if ( $youtube_long_id ) { ?>
      <!-- YouTube block -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="grid-row grid-row--nested background-black ui-rounded-box ui-rounded-box--top">
              <div class="grid-item is-xxl-24 ui-rounded-box ui-rounded-box--top">
                <div class="pt-4 pl-2 pr-2">
                  <div class="u-video-embed-container">
                    <?php echo render_youtube_embed_iframe( $youtube_long_id, false, 'lazy', get_the_title() ); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="grid-row grid-row--nested ui-backgrounded-box-padding background-black font-color-white ui-rounded-box ui-rounded-box--bottom">
              <div class="grid-item is-s-24 is-xxl-11">
                <h3 class="font-size-15 font-size-xl-14 font-size-s-13 font-weight-bold mb-s-4 text-wrap-balance">
                  Want to see what we've got planned?
                </h3>
              </div>
              <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-12 font-size-s-11 font-weight-bold text-wrap-pretty">
                <p>Go behind the scenes with the team to find out more about the new space, and what it will mean for our journalism.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>

      <!-- our story -->
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="grid-row grid-row--nested background-black ui-rounded-box ui-rounded-box--top">
              <div class="grid-item is-xxl-24 support-page__invert-section-background support-page__our-story-background ui-rounded-box ui-rounded-box--top"></div>
            </div>
            <div class="grid-row grid-row--nested ui-backgrounded-box-padding background-black font-color-white ui-rounded-box ui-rounded-box--bottom">
              <div class="grid-item is-xxl-24 text-align-center mb-6 mb-s-5">
                <h3 class="ui-boxed-title ui-boxed-title--white">
                  Our Story
                </h3>
              </div>
              <div class="grid-item is-s-24 is-xxl-11">
                <h3 class="font-size-15 font-size-xl-14 font-size-s-13 font-weight-bold mb-s-4 text-wrap-balance">
                  Born amid anti-austerity movements as a show on community radio, our supporters are the reason we’ve grown to be one of Britain’s most influential media organisations.
                </h3>
              </div>
              <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-12 font-size-s-11 font-weight-bold text-wrap-pretty">
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
          <div class="mb-4 mb-s-4">
            <h4 class="font-size-13 font-size-s-14 font-weight-bold mb-3">Other Donation Methods</h4>
            <p>The best way to ensure we receive as much of your donation as possible after processing fees is to make a payment directly through our donation website, however we also have an option for UK Direct Debits.</p>
          </div>

           <!-- GoCardless -->
          <div class="grid-row grid--nested mb-5">
            <div class="grid-item is-xxl-12 is-s-24 mb-3">
              <img class="support-page__direct-debit-logo mb-3" src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/dist/img/pages/support-page/support-logo-directdebit.svg" alt="Direct Debit logo" />
              <p>You can donate to us via a UK Direct Debit regular bank transfer using the GoCardless platform.</p>
            </div>
            <div class="grid-item is-xxl-12 is-s-24">
              <div class="mb-3">
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
