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

  $above_the_fold_content = '<p class="mb-3">We are a <span class="font-color-red">not-for-profit</span> organisation <span class="font-color-red">directly powered by our supporters.</span></p>
                  <p class="mb-3">Every penny we make flows directly into producing <span class="font-color-red">independent journalism</span> and expanding our coverage.</p>
                  <p><span class="font-color-red">We maintain our editorial autonomy</span> by refusing corporate sponsorships, venture capital, subscription barriers, and paid content.</p>';
  $our_funding_content = get_post_meta( $post->ID, '_nm_funding_sources', true );
  ?>
  <article id="page" class="how-we-are-funded-page">
      <div class="container">
        <div class="grid-item">
          <h4 class="font-size-9 font-weight-bold pt-4 pb-3 ui-border-bottom ui-border--black">
            Support Us
          </h4>
        </div>
        <div class="grid-row mt-4 mb-5 font-weight-bold">
          <div class="grid-item support-page__headline font-size-20 font-size-xl-19 font-size-m-18 font-size-s-17">
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
            <div class="grid-row grid-row--nested background-white ui-rounded-box">
              <div class="grid-item is-xxl-12 is-m-24">
                <div class="p-4 p-s-3 font-weight-bold">
                  <div class="u-video-embed-container only-mobile mt-2 mb-4">
                    <?php echo render_youtube_embed_iframe( $youtube_id, true ); ?>
                  </div>
                  <div class="font-size-15 font-size-l-14 font-size-s-14 mb-4">
                    <span class="font-color-red">We believe in the power of people</span> to build a new media for a different politics.
                  </div>
                  <div class="font-size-s-12 font-weight-bold mb-4 only-mobile">
                    <?php echo $above_the_fold_content; ?>
                  </div>
                  <div class="font-size-12 font-size-s-12 mr-8 mr-s-0 background-gray-base ui-rounded-box p-4">
                    <p class="mb-3">Thousands of people like you are already supporting our vision of a free media. You can join them today.</p>
                    <a href="<?php echo site_url( 'support/' ); ?>" class="ui-button ui-button--red ui-button">Be part of the change</a>
                  </div>
                </div>
              </div>
              <div class="grid-item is-xxl-12 is-m-24 only-desktop">
                <div class="p-4 font-size-13 font-weight-bold">
                  <?php echo $above_the_fold_content; ?>
                </div>
              </div>
              <div class="grid-item is-xxl-24 only-desktop">
                <div class="pl-4 pr-4 pb-5">
                <?php
                if ( $youtube_id ) {
                  ?>
                  <div class="u-video-embed-container">
                    <?php echo render_youtube_embed_iframe( $youtube_id, true ); ?>
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
    </div>

    <div class="container mb-5">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <div class="background-white ui-rounded-box p-4">
            <div class="text-align-center mt-4 mb-6 mb-s-5">
              <h3 class="ui-boxed-title">Our Funding</h3>
            </div>

            <div class="grid-row grid-row--nested">
              <div class="grid-item is-m-24 is-xxl-11 font-size-14 font-size-s-13 font-weight-bold mb-s-4">
                Because the vast majority of our income is raised directly from supporters, we can be <span class="font-color-red">editorially independent</span> without ever having to toe someone else’s line.
              </div>
              <div class="grid-item offset-s-0 is-s-24 offset-xxl-1 is-xxl-12 font-size-12 font-size-s-11 font-weight-bold">
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
            <div class="text-align-center mt-4 mb-6 mb-s-5">
              <h3 class="ui-boxed-title">Other Sources</h3>
            </div>
            <?php
            foreach ( $our_funding_content as $index => $content ) {
              ?>
                <div class="grid-row grid-row--nested mb-5 <?php echo $index % 2 !== 0 ? 'grid-row--reverse' : ''; ?>">
                  <div class="grid-item is-xxl-8 is-m-24 mb-s-4">
                    <h4 class="font-weight-bold font-size-14 font-size-s-14 mb-3"><?php echo esc_html( $content['title'] ); ?></h4>
                    <?php if ( ! empty( $content['subtitle'] ) ) { ?>
                      <p class="font-weight-bold font-size-12 font-size-s-12 mb-2"><?php echo esc_html( $content['subtitle'] ); ?></p>
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
