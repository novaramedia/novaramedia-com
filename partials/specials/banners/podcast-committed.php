<?php
  $local_term = get_term_by( 'slug', 'committed', 'category' );

if ( $local_term ) {
   $url = get_term_link( $local_term );
  if ( $url ) {
    ?>
<div class="committed-banner__backgrounded" style="background-color:rgb(254, 105, 25); overflow: hidden; position: relative">
  <style type="text/css">
    .fallback .committed-banner__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-background.jpg'; ?>);
    }
    .webp .committed-banner__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-background.webp'; ?>);
    }
    .avif .committed-banner__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-background.avif'; ?>);
    }
    .committed-banner__backgrounded {
      height: 267px;
      background-size: cover;
      background-position: 0% 29%;
      background-repeat: no-repeat;
    }
    .committed-banner__grid-row{
      justify-content: space-between;
    }
    .committed-banner__grid-item {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0px;
    }
    .committed-banner__grid-item-left {
      justify-content: flex-start;
    }
     .committed-banner__grid-item-right {
      justify-content: flex-end;
    }
    .committed-banner__container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 100%;
    }
    .committed-banner__logo {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-logo-white.png'; ?>);
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }
    @media screen and (max-width: 1408px) {
      .committed-banner__backgrounded {
        background-position: 50% 30%;
        background-size: 150%;
      }
    }
    @media screen and (max-width: 910px) {
      .committed-banner__backgrounded {
        background-position: 50% 29.5%;
         background-size: 190%;
      }
    }
    @media screen and (max-width: 759px) {
      .committed-banner__container {
        justify-content: center;
      }
      .committed-banner__backgrounded {
        background-position: 50% 31%;
        background-size: 200%;
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
  <div class="container committed-banner__container pt-6 pb-8 p-s-4">
    <div class="grid-row committed-banner__grid-row">
      <div class="grid-item is-s-12 is-xxl-6 is-m-7 committed-banner__grid-item committed-banner__grid-item-left mt-s-4">
        <p class="font-color-white font-weight-bold font-size-11 font-size-s-11">Would you go to prison</br>for your politics?</p>
      </div>
      <div class="grid-item is-s-24 is-xs-20 is-xxl-12 is-m-10 committed-banner__grid-item committed-banner__logo">
      </div>
      <div class="grid-item is-s-12 is-xxl-6 is-m-7 committed-banner__grid-item committed-banner__grid-item-right mt-s-4">
        <div class="committed-banner__button-container committed-banner__grid-item-right">
          <p class="font-color-white font-weight-bold font-size-11 mb-3 pt-6 pt-s-0 font-size-s-11">Meet the people who</br>have done just that.</p>
          <a href="<?php echo $url; ?>" class="ui-button ui-button--small ui-button--white">Listen Now</a>
        </div>
      </div>
    </div>
  </div>
</div>
     <?php
  }
}
?>
