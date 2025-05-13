/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global WP */

import $ from 'jquery';
import Cookies from 'js-cookie';

export class Support {
  constructor() {
    this.donationAppUrl = 'https://donate.novaramedia.com/';
    this.saveClosedStateTimeout = 21; // days
    this.hasApprovalCookie =
      Cookies.get('cookie-approval') === 'true' ? true : false;
  }

  onReady() {
    const _this = this;

    if ($('.support-section').length) {
      _this.setupAutovalues();
      _this.bind();
    }

    if ($('.support-bar').length) {
      _this.initSupportBar();
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
        typeof WP.supportSectionAutovalues[urlParamSupportCode] !== 'undefined'
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

      _this.setAutoValues($form, this.autovalues['show_first']);

      if (this.autovalues['show_first'] === 'oneoff') {
        // if the setting is to show one off first then update buttons active state
        _this.clearActiveButtonState($form);
        $form
          .find('.support-form__schedule-option[data-value=oneoff]')
          .addClass('support-form__button--active');
      }

      const $valueInput = $form.find('.support-form__value-input').first();

          $form.find('.support-form__button').on({
      click(event) {
        event.preventDefault();

        const $button = $(this);
        const data = $button.data();

        function updateSupportSection(data) {
          const headingId = data.value === 'regular' ? 'support-heading' : 'support-heading-desktop';
          const textId = data.value === 'regular' ? 'support-text' : 'support-text-desktop';

          console.log('Updating heading and text:', headingId, textId);

          const heading = document.getElementById(headingId);
          const text = document.getElementById(textId);

          if (heading && text) {
            heading.innerHTML = window.SupportFormCopy[data.value].heading;
            text.innerHTML = window.SupportFormCopy[data.value].text;
          }
        }

        if (data.action === 'set-type') {
          // if the button is setting the type of donation
          _this.setAutoValues($form, data.value);

          $form.attr('action', _this.donationAppUrl + data.value);

          _this.clearActiveButtonState($form);

          $button.addClass('support-form__button--active');

          updateSupportSection(data);

        } else if (data.action === 'set-value') {
            // if the button is setting the donation value
            $valueInput.val(data.value);

            _this.clearActiveButtonState($form, 'set-value');

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

          _this.clearActiveButtonState($form, 'set-value');

          $(this).addClass('support-form__button--active');
        },
      });

      $form.addClass('support-form--active');
    });
  }

  /**
   * Clears the active state from input buttons. Default clears all but optional param clears just one action type of button
   *
   * @param {Object}   $form         jQuery object of the form in question.
   * @param {String}   [actionType]  Action type filter to clear just one type of button. 'set-value' or 'set-type'
   */
  clearActiveButtonState($form, actionType) {
    if (actionType) {
      $form
        .find(`.support-form__button[data-action="${actionType}"]`)
        .removeClass('support-form__button--active');
    } else {
      $form
        .find('.support-form__button')
        .removeClass('support-form__button--active');
    }
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
      const value =
        _this.autovalues[`${donationType}_${$(input).data('name')}`];

      $(input).data('value', value).text(`£${value}`);
    });
  }

  initSupportBar() {
    var _this = this;
    const $bar = $('.support-bar');
    const $barClose = $bar.find('.support-bar__close-trigger');
    const $barOpen = $bar.find('.support-bar__open-trigger');

    _this.hasClosedSupportBarCookie =
      Cookies.get('support-bar-closed') === 'true' ? true : false;

    if (!_this.hasClosedSupportBarCookie) {
      $bar.removeClass('support-bar--closed').addClass('support-bar--open');
    }

    $bar.addClass('support-bar--active');

    $barOpen.on({
      click(event) {
        event.preventDefault();

        $bar.removeClass('support-bar--closed').addClass('support-bar--open');

        if (_this.hasApprovalCookie) {
          Cookies.set('support-bar-closed', 'false', {
            expires: _this.saveClosedStateTimeout,
          });
        }
      },
    });

    $barClose.on({
      click(event) {
        event.preventDefault();

        $bar.removeClass('support-bar--open').addClass('support-bar--closed');

        if (_this.hasApprovalCookie) {
          Cookies.set('support-bar-closed', 'true', {
            expires: _this.saveClosedStateTimeout,
          });
        }
      },
    });
  }

  static numberWithCommas(x) {
    // https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript#2901298
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }
}
