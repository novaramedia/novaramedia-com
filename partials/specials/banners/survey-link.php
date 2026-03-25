<?php
/**
 * Audience Survey 2026 Banner
 *
 * Simple banner with an SVG design link on one side and
 * preset text with a CTA button on the other.
 */

$url = 'https://novaramedia.com/survey2026';
?>
<div class="survey-link-banner container">
  <div class="grid-row">
    <div class="grid-item is-xxl-24">
      <div class="survey-link-banner__box background-red font-color-white ui-backgrounded-box-padding ui-rounded-box">
        <style type="text/css">
          .survey-link-banner__box {
            overflow: hidden;
            position: relative;
          }
          .survey-link-banner__svg-col {
            display: flex;
            align-items: center;
            justify-content: center;
          }
          .survey-link-banner__svg-col a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
          }
          .survey-link-banner__svg-col svg {
            max-height: 200px;
            width: auto;
            height: auto;
          }
          .survey-link-banner__text-col {
            display: flex;
            flex-direction: column;
            justify-content: center;
          }
          .survey-link-banner__text-col .ui-button {
            align-self: flex-start;
          }
          @media screen and (max-width: 1104px) {
            .survey-link-banner__svg-col svg {
              max-height: 160px;
            }
          }
          @media screen and (max-width: 910px) {
            .survey-link-banner__svg-col svg {
              max-height: 130px;
            }
          }
          @media screen and (max-width: 759px) {
            .survey-link-banner__svg-col {
              margin-bottom: 1rem;
            }
            .survey-link-banner__svg-col svg {
              max-height: 140px;
            }
          }
        </style>
        <div class="grid-row">
          <div class="grid-item is-s-24 is-xxl-12 survey-link-banner__svg-col">
            <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="nofollow">
              <?php echo nm_get_file( '/dist/img/specials/banners/survey-link-banner.svg' ); ?>
            </a>
          </div>
          <div class="grid-item is-s-24 is-xxl-12 survey-link-banner__text-col">
            <p class="font-size-12 font-size-m-11 font-size-s-11 font-weight-bold mb-4">Where do you think Novara Media is at? And most importantly, where do you think it should go? We have ideas, and would like to hear yours.</p>
            <a href="<?php echo esc_url( $url ); ?>" class="ui-button ui-button--white" target="_blank" rel="nofollow">Take the survey</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
