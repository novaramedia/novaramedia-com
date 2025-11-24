<?php
  $local_term = get_term_by( 'slug', 'committed', 'category' );
  $base_image_path = get_stylesheet_directory_uri() . '/dist/img/specials/committed/';
  $url = $local_term ? get_term_link( $local_term ) : false;
if ( $local_term && $url ) {
  ?>
<div class="committed-banner container">
  <div class="grid-row">
    <div class="grid-item is-xxl-24">
      <div class="committed-banner__backgrounded ui-rounded-box">
        <style type="text/css">
          .committed-banner__backgrounded {
            background-color:rgb(254, 105, 25);
            overflow: hidden;
            position: relative
          }
          .fallback .committed-banner__backgrounded {
            background-image: url(<?php echo $base_image_path . 'committed-background.jpg'; ?>);
          }
          .webp .committed-banner__backgrounded {
            background-image: url(<?php echo $base_image_path . 'committed-background.webp'; ?>);
          }
          .avif .committed-banner__backgrounded {
            background-image: url(<?php echo $base_image_path . 'committed-background.avif'; ?>);
          }
          .committed-banner__backgrounded {
            background-size: cover;
            background-position: 0% 50%;
            background-repeat: no-repeat;
          }
          .committed-banner__grid-row {
            align-items: center;
            height: 100%;
          }
          @media screen and (max-width: 759px) {
            .committed-banner__grid-row {
              align-items: flex-start;
            }
            .committed-banner__logo {
              order: 1;
              height: 80px;
            }
            .committed-banner__grid-item-left {
              order: 2;
              justify-content: flex-start;
              align-items: flex-start;
            }
            .committed-banner__grid-item-right {
              order: 3;
              align-items: flex-start;
            }
          }
        </style>
        <div class="pt-5 pb-6 p-s-4">
          <div class="grid-row committed-banner__grid-row">
            <div class="committed-banner__grid-item-left grid-item is-s-12 is-xxl-6 is-m-7 mt-s-6">
              <a href="<?php echo esc_url( $url ); ?>">
                <p class="font-color-white font-weight-bold font-size-11 font-size-s-11 text-wrap-balance ui-hover p-3">Would you go to prison for your politics?</p>
              </a>
            </div>
            <div class="committed-banner__logo grid-item is-s-24 is-xs-20 is-xxl-12 is-m-10 mt-s-2 ui-hover">
              <a href="<?php echo esc_url( $url ); ?>" class="u-display-block">
                <img src="<?php echo esc_url( $base_image_path . 'committed-logo-white.png' ); ?>" alt="Committed podcast logo">
              </a>
            </div>
            <div class="committed-banner__grid-item-right grid-item is-s-12 is-xxl-6 is-m-7 mt-s-6">
              <div class="p-3">
                <p class="font-color-white font-weight-bold font-size-11 font-size-s-11 text-wrap-balance ui-hover mb-3 pt-6 pt-s-0">
                  <a href="<?php echo esc_url( $url ); ?>">
                    Meet the people who have done just that.
                  </a>
                </p>
                <a href="<?php echo esc_url( $url ); ?>" class="ui-button ui-button--small ui-button--white">Listen Now</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
     <?php
}
?>
