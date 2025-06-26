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

      // Always call setAutoValues with the preferred initial type
      const showFirst = this.autovalues['show_first'];
      _this.setAutoValues($form, showFirst);

      // Highlight correct schedule option
      $form
        .find(`.support-form__schedule-option[data-value="${showFirst}"]`)
        .addClass('support-form__button--active ui-button--active')
        .attr('aria-checked', 'true')
        .attr('tabindex', '0');

      const $valueInput = $form.find('.support-form__value-input').first();

      $form.find('.support-form__button').on({
        click(event) {
          event.preventDefault();

          const $button = $(this);
          const data = $button.data();
          // function to update the support section copy depending on the type of donation
          function updateSupportSection(data, $form) {
            const heading = $form.find('.support-form__dynamic-heading');
            const text = $form.find('.support-form__dynamic-text');
            const copy =
              WP.supportSectionCopy && WP.supportSectionCopy[data.value];

            if (!copy) {
              if (heading.length) {
                heading.text('Support Us');
              }
              if (text.length) {
                text.text(
                  'You make us strong. Help build people-powered media and donate one hour’s wage per month—or whatever you can afford—today'
                );
              }
              return;
            }

            if (heading.length) {
              heading.text(copy.heading);
            }
            if (text.length) {
              text.text(copy.text);
            }
          }
          if (data.action === 'set-type') {
            _this.clearActiveButtonState($form);

            _this.setAutoValues($form, data.value);
            $form.attr('action', _this.donationAppUrl + data.value);
            $button.addClass('support-form__button--active ui-button--active');

            updateSupportSection(data, $form);

            $form.find('[data-action="set-type"]').each((i, btn) => {
              const isSelected = btn === $button[0];
              btn.setAttribute('aria-checked', isSelected.toString());
              btn.setAttribute('tabindex', isSelected ? '0' : '-1');
            });
          } else if (data.action === 'set-value') {
            // if the button is setting the donation value
            $valueInput.val(data.value);

            _this.clearActiveButtonState($form, 'set-value');

            $('.support-form__custom-input').removeClass(
              'support-form__button--active ui-button--active'
            );

            $button.addClass('support-form__button--active ui-button--active');

            // Accessibility state management for custom radio buttons
            $form.find('[data-action="set-value"]').each((i, btn) => {
              const isSelected = btn === $button[0];
              btn.setAttribute('aria-checked', isSelected.toString());
              btn.setAttribute('tabindex', isSelected ? '0' : '-1');
            });
          }
        },
      });

      $form.find('.support-form__button').on('keydown', function(event) {
        const $buttons = $(this).closest('.support-form').find('.support-form__button');
        let index = $buttons.index(this);

        if (event.key === 'ArrowRight') {
          event.preventDefault();
          index = (index + 1) % $buttons.length;
          $buttons.eq(index).focus();
        } else if (event.key === 'ArrowLeft') {
          event.preventDefault();
          index = (index - 1 + $buttons.length) % $buttons.length;
          $buttons.eq(index).focus();
        } else if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          $(this).trigger('click'); // Activate on Enter or Space
        }
      });

      $form.find('.support-form__custom-input').on({
        input(event) {
          event.preventDefault();
          $valueInput.val(event.target.value);
          _this.clearActiveButtonState($form, 'set-value');

          $(this).addClass('support-form__button--active ui-button--active');

          // Clear ARIA radio state for value buttons when custom input is used
          $form.find('[data-action="set-value"]').each((i, btn) => {
            btn.setAttribute('aria-checked', 'false');
            btn.setAttribute('tabindex', '-1');
          });
        },
        keydown(event) {
          if (event.key === 'Enter') {
            event.preventDefault();
            const val = $(this).val().trim();
            if (val !== '') {
              _this.clearActiveButtonState($form, 'set-value');
              $(this).addClass('support-form__button--active ui-button--active');
              $form.find('[data-action="set-value"]').each((i, btn) => {
                btn.setAttribute('aria-checked', 'false');
                btn.setAttribute('tabindex', '-1');
              });
              // Optionally, trigger form submission or next step here
            }
          }
        }
      });
      $form.addClass('support-form--active ui-button--active');
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
        .removeClass('support-form__button--active ui-button--active');
    } else {
      $form
        .find('.support-form__button')
        .removeClass('support-form__button--active ui-button--active');
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
    const $buttons = $form.find('.support-form__value-option');
    const $valueInput = $form.find('.support-form__value-input').first();

    // Clear old state before setting new one
    _this.clearActiveButtonState($form, 'set-value');

    // Update each button with new values
    $buttons.each((index, input) => {
      const $input = $(input);
      const name = $input.data('name');
      const value = _this.autovalues[`${donationType}_${name}`];

      $input
        .data('value', value)
        .text(`£${value}`)
        .attr('aria-checked', 'false')
        .attr('tabindex', '-1');
    });

    // Select the first button by default
    const $first = $buttons.first();
    if ($first.length) {
      $first
        .addClass('support-form__button--active ui-button--active')
        .attr('aria-checked', 'true')
        .attr('tabindex', '0');

      $valueInput.val($first.data('value'));
    }
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
