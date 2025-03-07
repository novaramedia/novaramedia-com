<?php
  $local_term = get_term_by( 'slug', 'committed', 'category' );

if ( $local_term ) {
    $url = get_term_link( $local_term );
  if ( $url ) {
    ?>
<div class="foreign-agent-banner__backgrounded" style="background-color:rgb(254, 105, 25); overflow: hidden; position: relative">
  <style type="text/css">
    .fallback .foreign-agent-banner__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-background.png'; ?>);
      background-size: cover;
      background-position: center center;
      background-repeat: no-repeat;
    }
    .webp .foreign-agent-banner__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-background.webp'; ?>);
      background-size: cover;
      background-position: top center;
      background-repeat: no-repeat;
    }
    .avif .foreign-agent-banner__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-background.avif'; ?>);
      background-size: cover;
      background-position: top center;
      background-repeat: no-repeat;
    }
    .foreign-agent-banner__title {
      max-width: 280px;
      margin: 1rem 0;
    }
    .foreign-agent-banner__box {
      border: 2px solid;
      border-radius: 0px;
      padding: .8rem 1rem;
      background-color: #FFF9F5;
    }
    .foreign-agent-banner__caption {
      transform: rotate(1deg);
    }
    .foreign-agent-banner__button {
      transform: rotate(-2deg);
      margin-left: 2rem
    }
    .committed-banner__grid-item {
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }
    .committed-banner__grid-item-left {
      justify-content: flex-start;
    }
     .committed-banner__grid-item-right {
      justify-content: flex-end;
    }
    .committed-banner__button-container {
      flex-direction: column;
    }
    .committed-banner__logo {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-logo-white.png'; ?>);
      background-size: contain;
      background-repeat: no-repeat;
    }

    @media screen and (max-width: 910px) {
      .foreign-agent-banner__title {
        max-width: 200px;
      }
    }
    @media screen and (max-width: 759px) {
      .foreign-agent-banner__caption {
        transform: rotate(-1deg);
        display: inline-block;
        width: 70%;
      }
      .foreign-agent-banner__button {
        transform: rotate(1deg);
        margin-left: 1rem
      }
    }
  </style>
  <div class="container pt-6 pb-6">
    <div class="grid-row">
      <div class="grid-item is-s-24 is-l-8 is-xxl-8 mb-s-3 committed-banner__grid-item committed-banner__grid-item-left">
        <p class="font-color-white font-weight-bold font-size-10">Would you go to prison</br>for your politics?</p>
      </div>
      <div class="grid-item is-s-24 is-l-8 is-xxl-8 mb-s-3 committed-banner__grid-item committed-banner__logo">
      </div>
      <div class="grid-item is-s-24 is-l-8 is-xxl-8 mb-s-3 committed-banner__grid-item committed-banner__grid-item-right">
        <div class="committed-banner__button-container">
          <p class="font-color-white font-weight-bold mb-3">Meet the people who</br>have done just that</p>
          <a href="<?php echo $url; ?>" class="ui-button ui-button--white">Listen Now</a>
        </div>
      </div>
    </div>
  </div>
</div>
    <?php
  }
}
?>
