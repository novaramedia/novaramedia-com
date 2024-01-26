
<?php
  $term = get_term_by('slug', 'if-i-speak', 'category');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="if-i-speak-banner__container padding-top-basic padding-bottom-basic">
  <style type="text/css">
    .if-i-speak-banner__container {
      color: rgb(220, 0, 5);
    }

    .if-i-speak-banner__container .nm-button {
      border-color: rgb(220, 0, 5);
      color: rgb(220, 0, 5);
    }

    .if-i-speak-banner__container .nm-button:hover {
      background-color: rgb(220, 0, 5);
      color: rgb(255, 255, 255);
    }

    .if-i-speak-banner__title {
      font-size: 18.5rem;
      line-height: .9;
      letter-spacing: -0.03em;
    }

    .if-i-speak-banner__image {
      background-size: cover;
      background-position: top;
      height: 210px;
      width: 360px;
      margin: 0 auto;
      margin-top: -80px;
    }

    .avif .if-i-speak-banner__image, .webp .if-i-speak-banner__image {
      background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/banners/if-i-speak.webp'; ?>);
    }

    .fallback .if-i-speak-banner__image {
      background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/banners/if-i-speak.png'; ?>);
    }

    .if-i-speak-banner__border {
      border: 1px solid rgb(220, 0, 5);
      margin-top: 0;
    }

    .if-i-speak-banner__on-smaller {
      display: none;
    }

    @media screen and (max-width: 1336px) {
      .if-i-speak-banner__title {
        font-size: 14.5rem;
      }

      .if-i-speak-banner__image {
        height: 170px;
        width: 300px;
        margin-top: -70px;
      }
    }

    @media screen and (max-width: 1104px) {
      .if-i-speak-banner__title {
        font-size: 12.5rem;
      }

      .if-i-speak-banner__image {
        width: 280px;
        margin-top: -70px;
      }
    }

    @media screen and (max-width: 910px) {
      .if-i-speak-banner__title {
        font-size: 11.4rem;
      }

      .if-i-speak-banner__image {
        height: 180px;
        width: 290px;
        margin-top: -10px;
      }

      .if-i-speak-banner__on-smaller {
        display: block;
      }

      .if-i-speak-banner__off-smaller {
        display: none;
      }

      .if-i-speak-banner__cta-container {
        text-align: left;
      }
    }

    @media screen and (max-width: 759px) {
      .if-i-speak-banner__title {
        font-size: 7rem;
      }

      .if-i-speak-banner__image {
        width: 100%;
      }
    }
  }
  </style>

  <div class="container">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <a href="<?php echo $url; ?>"><h4>Podcast</h4></a>
      </div>
    </div>
    <div class="flex-grid-row padding-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12">
        <a href="<?php echo $url; ?>">
          <h3 class="if-i-speak-banner__title">If I Speak...</h3>
        </a>
      </div>
    </div>
    <div class="flex-grid-row padding-bottom-small">
      <div class="if-i-speak-banner__off-smaller flex-grid-item flex-item-xxl-4 font-size-2 font-bold">
        <span class="font-uppercase font-smaller">with</span> Moya Lothian-McLean<br>
        <span class="font-uppercase font-smaller">and</span> Ash Sarkar
      </div>
      <div class="flex-grid-item flex-item-m-6 flex-item-l-3 flex-item-xxl-4">
        <a href="<?php echo $url; ?>">
          <div class="if-i-speak-banner__image"></div>
        </a>
      </div>
      <div class="if-i-speak-banner__cta-container flex-grid-item flex-item-m-6 flex-item-l-5 flex-item-xxl-4 text-align-right">
        <div class="if-i-speak-banner__on-smaller padding-bottom-small font-size-2 font-bold">
          <span class="font-uppercase font-smaller">with</span> Moya Lothian-McLean<br>
          <span class="font-uppercase font-smaller">and</span> Ash Sarkar
        </div>

        <a href="<?php echo $url; ?>"><p class="font-size-2 font-bold">New episodes out every Friday</p></a>
        <a href="<?php echo $url; ?>" class="nm-button nm-button--white nm-button--inline font-color-black">Listen Now</a>
      </div>
      <div class="flex-grid-item flex-item-xxl-12">
        <hr class="if-i-speak-banner__border">
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
