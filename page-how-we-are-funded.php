<?php
get_header();
?>
<main id="main-content" class="background-gray-base pb-5">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );

    $youtube_id = ! empty( $meta['_nm_support_youtube'] ) ? $meta['_nm_support_youtube'][0] : false;  }

  if ( ! $youtube_id ) {
    $youtube_id = 'v-3cksTJ8e4';
  }

  // Get funding sources from CMB2 metabox
  $our_funding_content = get_post_meta( $post->ID, '_nm_funding_sources', true );
  ?>
  <article id="page" class="how-we-are-funded-page">
      <div class="container">
        <div class="grid-item">
          <h4 class="font-size-10 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
            Support Us
          </h4>
        </div>
        <div class="grid-row mt-4 mb-5 font-weight-bold">
          <div class="grid-item support-page__headline font-size-17">
            <div class="font-color-black">
              Billionaire-backed?
            </div>
            <div class="font-color-red">
              Not us.
            </div>
          </div>
        </div>
      </div>
      <div class="container mb-5">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <div class="grid-row grid-row--nested background-white ui-rounded-box--top">
              <div class="grid-item is-xxl-12 is-m-24">
                <div class="p-4 font-weight-bold">
                  <div class="font-size-15 font-size-l-14 mb-4">
                    <span class="font-color-red">We believe in the power of people</span> to build a new media for a different politics.
                  </div>
                  <div class="font-size-12 mr-8 background-gray-base ui-rounded-box p-4">
                    <p class="mb-3">Thousands of people like you are already supporting our vision of a free media. You can join them today.</p>
                    <a href="<?php echo site_url( 'support/' ); ?>" class="ui-button ui-button--red ui-button--small">Be part of the change</a>
                  </div>
                </div>
              </div>
              <div class="grid-item is-xxl-12 is-m-24">
                <div class="p-4 font-size-13 font-weight-bold">
                  <p class="mb-3">We are a <span class="font-color-red">not-for-profit</span> organisation <span class="font-color-red">directly powered by our supporters.</span></p>
                  <p class="mb-3">Every penny we make flows directly into producing <span class="font-color-red">independent journalism</span> and expanding our coverage.</p>
                  <p><span class="font-color-red">We maintain our editorial autonomy</span> by refusing corporate sponsorships, venture capital, subscription barriers, and paid content.</p>
                </div>
              </div>
            </div>
          </div>
          <div class="grid-item is-xxl-24">
            <div class="background-white ui-rounded-box--bottom pl-4 pr-4 pb-4">
              <div class="p-2">
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
    </div>

    <div class="container mb-5">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <div class="background-white ui-rounded-box p-4">
            <div class="text-align-center mt-4 mb-6">
              <h3 class="ui-boxed-title">Our Funding</h3>
            </div>

            <div class="p-2 mb-5">
              <svg style="width: 100%; height: auto;" width="1350" height="186" viewBox="0 0 1350 186" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2094_6893)">
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 0 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 0 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 0 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 0 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 55 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 55 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 55 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 55 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 110 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 110 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 110 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 110 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 165 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 165 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 165 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 165 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 220 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 220 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 220 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 220 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 275 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 275 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 275 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 275 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 330 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 330 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 330 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 330 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 385 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 385 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 385 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 385 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 440 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 440 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 440 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 440 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 495 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 495 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 495 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 495 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 550 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 550 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 550 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 550 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 605 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 605 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 605 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 605 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 660 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 660 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 660 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 660 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 715 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 715 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 715 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 715 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 770 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 770 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 770 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 770 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 825 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 825 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 825 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 825 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 880 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 880 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 880 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 880 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 935 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 935 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 935 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 935 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 990 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 990 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 990 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 990 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1045 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1045 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1045 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1045 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1100 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1100 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1100 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1100 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1155 0)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1155 50)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1155 100)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1155 150)" fill="#FF1941"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1210 0)" fill="#131313"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1210 50)" fill="#131313"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1210 100)" fill="#131313"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1210 150)" fill="#131313"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1265 0)" fill="#131313"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1265 50)" fill="#131313"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1265 100)" fill="#FFB5BA"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1265 150)" fill="#FFB5BA"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1320 0)" fill="#FFB5BA"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1320 50)" fill="#FFB5BA"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1320 100)" fill="#D4D4D4"/>
                <circle cx="15" cy="15" r="15" transform="matrix(-4.37114e-08 1 1 4.37114e-08 1320 150)" fill="#D4D4D4"/>
                </g>
                <defs>
                <clipPath id="clip0_2094_6893">
                <rect width="186" height="1350" fill="white" transform="matrix(-4.37114e-08 1 1 4.37114e-08 0 0)"/>
                </clipPath>
                </defs>
                </svg>
            </div>

            <div class="grid-row grid-row--nested mb-6 text-align-center">
              <div class="grid-item is-xxl-4 offset-xxl-2">
                <div class="support-page__big-stat">
                  <div class="font-weight-bold">
                    <span class="font-size-17">89</span><span class="font-size-15 font-size-s-14">%</span>
                  </div>
                  <div class="font-size-13 font-size-s-11 font-weight-bold">
                    <span class="ui-dot ui-dot--red"></span> Direct
                  </div>
                </div>
              </div>
              <div class="grid-item is-xxl-4">
                <div class="support-page__big-stat">
                  <div class="font-weight-bold">
                    <span class="font-size-17">6</span><span class="font-size-15 font-size-s-14">%</span>
                  </div>
                  <div class="font-size-13 font-size-s-11 font-weight-bold">
                    <span class="ui-dot"></span> YouTube
                  </div>
                </div>
              </div>
              <div class="grid-item is-xxl-4">
                <div class="support-page__big-stat">
                  <div class="font-weight-bold">
                    <span class="font-size-17">4</span><span class="font-size-15 font-size-s-14">%</span>
                  </div>
                  <div class="font-size-13 font-size-s-11 font-weight-bold">
                    <span class="ui-dot ui-dot--pink"></span> Merch
                  </div>
                </div>
              </div>
              <div class="grid-item is-xxl-4">
                <div class="support-page__big-stat">
                  <div class="font-weight-bold">
                    <span class="font-size-17">2</span><span class="font-size-15 font-size-s-14">%</span>
                  </div>
                  <div class="font-size-13 font-size-s-11 font-weight-bold">
                    <span class="ui-dot ui-dot--gray"></span> Other
                  </div>
                </div>
              </div>
              <div class="grid-item is-xxl-4">
                <div class="support-page__big-stat">
                  <div class="font-weight-bold">
                    <span class="font-size-17">0</span><span class="font-size-15 font-size-s-14">%</span>
                  </div>
                  <div class="font-size-13 font-size-s-11 font-weight-bold">Murdoch</div>
                </div>
              </div>
            </div>

            <div class="grid-row grid-row--nested">
              <div class="grid-item is-m-24 is-xxl-11 font-size-14 font-weight-bold">
                Because the vast majority of our income is raised directly from supporters, we can be <span class="font-color-red">editorially independent</span> without ever having to toe someone else’s line.
              </div>
              <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-12 font-weight-bold">
                <p class="mb-3">It's a key principle that has always underpinned our funding model.</p>
                <p class="mb-3">Our supporters are individuals who donate to us on a monthly or one-off basis. We ask for one hour’s wage a month, but you can become a supporter with whatever you can afford.</p>
                <p>Supporter donations made via our website make up the vast majority of our annual income (around 89% in 2022). These donations are what we use to plan our future and grow the organisation, whether it’s taking on more staff or expanding our range of content.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container mb-5">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <?php render_support_form( 'banner', true ); ?>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <div class="background-white ui-rounded-box p-4">
            <div class="text-align-center mt-4 mb-6">
              <h3 class="ui-boxed-title">Other Sources</h3>
            </div>
            <?php
            foreach ( $our_funding_content as $index => $content ) {
              ?>
                <div class="grid-row grid-row--nested mb-5 <?php echo $index % 2 !== 0 ? 'grid-row--reverse' : ''; ?>">
                  <div class="grid-item is-xxl-8 is-m-24">
                    <h4 class="font-weight-semibold font-size-14 mb-3"><?php echo esc_html( $content['title'] ); ?></h4>
                    <?php if ( ! empty( $content['subtitle'] ) ) { ?>
                      <p class="font-weight-semibold font-size-12 mb-2"><?php echo esc_html( $content['subtitle'] ); ?></p>
                    <?php } ?>
                    <?php if ( ! empty( $content['text'] ) ) { ?>
                      <p><?php echo esc_html( $content['text'] ); ?></p>
                    <?php } ?>
                    <?php
                    if ( ! empty( $content['cta'] ) && ! empty( $content['cta_url'] ) ) {
                      ?>
                      <a href="<?php echo esc_url( $content['cta_url'] ); ?>" class="ui-button ui-button--red ui-button--small mt-3"><?php echo esc_html( $content['cta'] ); ?></a>
                      <?php
                    }
                    ?>
                  </div>
                  <div class="grid-item is-xxl-16 is-m-24">
                    <?php
                    if ( ! empty( $content['image_id'] ) ) {
                      $image_id = $content['image_id'];
                      echo wp_get_attachment_image( $image_id, 'col18-16to9', false, array( 'class' => 'ui-rounded-box' ) );
                    }
                    ?>
                  </div>
                </div>
                <?php
            }
            ?>
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
