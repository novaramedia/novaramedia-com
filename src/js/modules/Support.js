/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global WP */

import $ from 'jquery';

export class Support {
  constructor() {
    this.donationAppUrl = 'https://donate.novaramedia.com/';
  }

  onReady() {
    const _this = this;

    if ($('.support-section').length) {
      _this.setupAutovalues();
      _this.bind();
    }
  }

  /**
   * Checks url parameters for valid alternative autovalues codes and uses them if found
   */
  setupAutovalues() {
    const urlParams = new URLSearchParams(window.location.search);
    const urlParamSupportCode = urlParams.get('sv');
    let autovaluesKey = 'default';

    if (urlParamSupportCode !== null) {
      if (
        typeof WP.supportSectionAutovalues[urlParamSupportCode] !== 'undefined' &&
        WP.supportSectionAutovalues[urlParamSupportCode].length === 6
      ) {
        autovaluesKey = urlParamSupportCode;
      }
    }

    this.autovalues = WP.supportSectionAutovalues[autovaluesKey];
  }

  bind() {
    const _this = this;

    $('.support-form').each((index, value) => {
      const $form = $(value);

      _this.setAutoValues($form);

      const $valueInput = $form.find('.support-form__value-input').first();

      $form.find('.support-form__button').on({
        click(event) {
          event.preventDefault();

          const $button = $(this);
          const data = $button.data();

          if (data.action === 'set-type') {
            // if the button is setting the type of donation
            _this.setAutoValues($form, data.value);

            $form.attr('action', _this.donationAppUrl + data.value);

            $form
              .find('.support-form__button[data-action="set-type"]')
              .removeClass('support-form__button--active');

            $button.addClass('support-form__button--active');
          } else if (data.action === 'set-value') {
            // if the button is setting the donation value
            $valueInput.val(data.value);

            $form
              .find('.support-form__button[data-action="set-value"]')
              .removeClass('support-form__button--active');

            $('.support-form__custom-input').removeClass(
              'support-form__button--active'
            );

            $button.addClass('support-form__button--active');
          }
        },
      });

      $form.find('.support-form__custom-input').on({
        input(event) {
          event.preventDefault();

          $valueInput.val(event.target.value);

          $form
            .find('.support-form__button[data-action="set-value"]')
            .removeClass('support-form__button--active');

          $(this).addClass('support-form__button--active');
        },
      });

      $form.addClass('support-form--active');
    });
  }

  /**
   * Switches the quick auto values displayed on the form depending on the type of donation.
   *
   * @param {Object}   $form         jQuery object of the form in question.
   * @param {String}   [donationType=regular]  Type of donation. oneoff OR regular
   */
  setAutoValues($form, donationType = 'regular') {
    const _this = this;

    $form.find('.support-form__value-option').each((index, input) => {
      const autovaluesIndex = donationType === 'oneoff' ? index + 3 : index;
      const value = _this.autovalues[autovaluesIndex];
      $(input).data('value', value).text(`£${value}`);
    });
  }

  numberWithCommas(x) {
    // https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript#2901298
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }
}
