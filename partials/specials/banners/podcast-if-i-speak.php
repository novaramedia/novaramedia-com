
<?php
  $term = get_term_by('slug', 'if-i-speak', 'category');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="if-i-speak-banner__container padding-top-basic padding-bottom-basic">
  <style type="text/css">
    .if-i-speak-banner__container {
      color: var(--color-red);
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
      border: 1px solid var(--color-red);
      margin-top: 0;
    }

    .if-i-speak-banner__on-smaller {
      display: none;
    }

    @media screen and (max-width: 1408px) {
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
  </style>

  <div class="container">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <a href="<?php echo $url; ?>"><h4 class="fs-3-sans font-uppercase font-bold">Podcast</h4></a>
      </div>
    </div>
    <div class="grid-row pb-4">
      <div class="grid-item is-xxl-24">
        <a href="<?php echo $url; ?>">
          <h3 class="if-i-speak-banner__title">If I Speak...</h3>
        </a>
      </div>
    </div>
    <div class="grid-row pb-4">
      <div class="if-i-speak-banner__off-smaller grid-item is-xxl-8 fs-6">
        <span class="font-uppercase fs-4-sans">with</span> Moya Lothian-McLean<br>
        <span class="font-uppercase fs-4-sans">and</span> Ash Sarkar
      </div>
      <div class="grid-item is-m-12 is-l-6 is-xxl-8">
        <a href="<?php echo $url; ?>">
          <div class="if-i-speak-banner__image"></div>
        </a>
      </div>
      <div class="if-i-speak-banner__cta-container grid-item is-m-12 is-l-10 is-xxl-8 text-align-right">
        <div class="if-i-speak-banner__on-smaller mb-2 fs-6 fs-s-5-sans font-weight-bold">
          <span class="font-uppercase fs-4-sans">with</span> Moya Lothian-McLean<br>
          <span class="font-uppercase fs-4-sans">and</span> Ash Sarkar
        </div>

        <a href="<?php echo $url; ?>"><p class="fs-6 font-weight-bold mb-3">New episodes out every Friday</p></a>
        <a href="<?php echo $url; ?>" class="ui-button ui-button--red">Listen Now</a>
      </div>
      <div class="grid-item is-xxl-24">
        <hr class="if-i-speak-banner__border">
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
