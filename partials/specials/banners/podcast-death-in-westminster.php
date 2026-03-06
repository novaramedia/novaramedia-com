<?php
  $local_term = get_term_by( 'slug', 'death-in-westminster', 'category' );
  $base_image_path = get_stylesheet_directory_uri() . '/dist/img/specials/death-in-westminster/';
  $url = $local_term ? get_term_link( $local_term ) : false;
if ( $local_term && $url ) {
  ?>
<div class="diw-banner container">
  <div class="grid-row">
    <div class="grid-item is-xxl-24">
      <div class="diw-banner__backgrounded ui-rounded-box">
        <style type="text/css">
          .diw-banner__backgrounded {
            --diw-red: #FF3817;
            --diw-black-muted: #272727;
            background-color: #dbd9cf;
            overflow: hidden;
            position: relative;
            background-size: cover;
            background-position: top center;
            background-repeat: no-repeat;
          }
          .avif .diw-banner__backgrounded {
            background-image: url(<?php echo esc_url( $base_image_path . 'diw-header-background.avif' ); ?>);
          }
          .webp .diw-banner__backgrounded {
            background-image: url(<?php echo esc_url( $base_image_path . 'diw-header-background.webp' ); ?>);
          }
          .fallback .diw-banner__backgrounded {
            background-image: url(<?php echo esc_url( $base_image_path . 'diw-header-background.jpg' ); ?>);
          }
          .diw-banner__artwork {
            position: absolute;
            left: 50%;
            top: 0;
            transform: translateX(-50%);
            height: 95%;
            width: auto;
            z-index: 2;
          }
          .diw-banner__grid-row {
            position: relative;
            align-items: center;
            min-height: 240px;
            padding: 1.5rem;
            z-index: 0;
          }
          .diw-banner__title {
            font-size: 5.35rem;
            letter-spacing: -6%;
            line-height: 0.8;
            color: var(--diw-black-muted);
            text-align: center;
          }
          .diw-banner__cta-text {
            color: var(--diw-black-muted);
          }
          @media screen and (max-width: 1408px) {
            .diw-banner__title {
              font-size: 4rem;
            }
            .diw-banner__grid-row {
              min-height: 220px;
            }
          }
          @media screen and (max-width: 1104px) {
            .diw-banner__title {
              font-size: 3.5rem;
            }
          }
          @media screen and (max-width: 910px) {
            .diw-banner__grid-row {
              flex-direction: column;
              text-align: center;
              min-height: 0;
              margin-top: -2rem;
            }
            .diw-banner__artwork {
              position: relative;
              left: auto;
              transform: none;
              height: 140px;
              margin: 0 auto;
              display: block;
            }
            .diw-banner__title {
              font-size: 4rem;
            }
          }
          @media screen and (max-width: 759px) {
            .diw-banner__title {
              font-size: 3.2rem;
            }
            .diw-banner__grid-row {
              padding-left: 0;
              padding-right: 0;
            }
          }
        </style>
        <a href="<?php echo esc_url( $url ); ?>">
          <picture>
            <source srcset="<?php echo esc_url( $base_image_path . 'diw-artwork.avif' ); ?>" type="image/avif">
            <source srcset="<?php echo esc_url( $base_image_path . 'diw-artwork.webp' ); ?>" type="image/webp">
            <img
              src="<?php echo esc_url( $base_image_path . 'diw-artwork.png' ); ?>"
              alt="Death in Westminster artwork"
              class="diw-banner__artwork"
            />
          </picture>
        </a>
          <div class="grid-row diw-banner__grid-row">
            <div class="grid-item is-xxl-9 is-m-24 is-s-24">
              <a href="<?php echo esc_url( $url ); ?>" class="ui-hover">
                <h3 class="diw-banner__title font-weight-bold">Death in Westminster</h3>
              </a>
            </div>
            <div class="grid-item is-xxl-7 is-m-24 is-s-24"></div>
            <div class="grid-item is-xxl-8 is-m-24 is-s-24 mt-m-3">
              <p class="diw-banner__cta-text font-weight-bold font-size-11 font-size-s-11 text-wrap-balance mb-3">What death on Parliament's doorstep reveals about Britain's hidden wealth.</p>
              <a href="<?php echo esc_url( $url ); ?>" class="ui-button ui-button--small ui-button--white">Listen now</a>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
     <?php
}
?>
