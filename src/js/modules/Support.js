/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global WP */

import $ from 'jquery';
import Cookies from 'js-cookie';
import isNonEmptyString from '../functions/isNonEmptyString.js';

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

      // Ensure the first value button is also selected and active
      const $firstValueBtn = $form
        .find('.support-form__value-option:visible')
        .first();
      if ($firstValueBtn.length) {
        $firstValueBtn
          .addClass('ui-button--active')
          .attr('aria-checked', 'true')
          .attr('tabindex', '0');

        $form
          .find('.support-form__value-input')
          .val($firstValueBtn.data('value'));
      }

      // Highlight correct schedule option
      $form
        .find(`.support-form__schedule-option[data-value="${showFirst}"]`)
        .addClass('ui-button--active')
        .attr('aria-checked', 'true')
        .attr('tabindex', '0');

      const $valueInput = $form.find('.support-form__value-input').first();

      $form.find('.support-form__button').on({
        click(event) {
          event.preventDefault();

          const $button = $(this);
          const data = $button.data();
          if (data.action === 'set-type') {
            _this.clearActiveButtonState($form);

            _this.setAutoValues($form, data.value);
            _this.updateSupportSectionCopy(data, $form);

            $form.attr('action', _this.donationAppUrl + data.value);

            // Set active class on all buttons with the same data-value (both visible and hidden)
            $form
              .find(`[data-action="set-type"][data-value="${data.value}"]`)
              .addClass('ui-button--active');

            $form.find('[data-action="set-type"]').each((index, button) => {
              const $button = $(button);
              const isSelected = $button.data('value') === data.value;
              button.setAttribute('aria-checked', isSelected.toString());
              button.setAttribute('tabindex', isSelected ? '0' : '-1');
            });
          } else if (data.action === 'set-value') {
            // if the button is setting the donation value
            $valueInput.val(data.value);

            _this.clearActiveButtonState($form, 'set-value');

            $('.support-form__custom-input-container').removeClass(
              'support-form__custom-input-container--active'
            );

            $button.addClass('ui-button--active');

            // Accessibility state management for custom radio buttons
            $form.find('[data-action="set-value"]').each((index, button) => {
              const isSelected = button === $button[0];
              button.setAttribute('aria-checked', isSelected.toString());
              button.setAttribute('tabindex', isSelected ? '0' : '-1');
            });
          }
        },
        keydown(event) {
          const $buttons = $(this)
            .closest('.support-form')
            .find('.support-form__button');
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
        },
      });

      $form.find('.support-form__custom-input').on({
        input(event) {
          event.preventDefault();
          $valueInput.val(event.target.value);
          _this.clearActiveButtonState($form, 'set-value');

          $(this)
            .closest('.support-form__custom-input-container')
            .addClass('support-form__custom-input-container--active');

          // Clear ARIA radio state for value buttons when custom input is used
          $form.find('[data-action="set-value"]').each((index, button) => {
            button.setAttribute('aria-checked', 'false');
            button.setAttribute('tabindex', '-1');
          });
        },
        keydown(event) {
          if (event.key === 'Enter') {
            event.preventDefault();
            const inputValue = $(this).val().trim();
            if (inputValue !== '') {
              _this.clearActiveButtonState($form, 'set-value');
              $(this)
                .closest('.support-form__custom-input-container')
                .addClass('support-form__custom-input-container--active');
              $form.find('[data-action="set-value"]').each((index, button) => {
                button.setAttribute('aria-checked', 'false');
                button.setAttribute('tabindex', '-1');
              });
            }
          }
        },
        focus() {
          // on focus add active class to container
          $(this)
            .closest('.support-form__custom-input-container')
            .addClass('support-form__custom-input-container--active');
        },
        blur() {
          // on blur remove active class from container if input is empty
          if (!$(this).val()) {
            $(this)
              .closest('.support-form__custom-input-container')
              .removeClass('support-form__custom-input-container--active');
          }
        },
      });
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
        .removeClass('ui-button--active');
    } else {
      $form.find('.support-form__button').removeClass('ui-button--active');
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
    $buttons.each((index, inputElement) => {
      const $input = $(inputElement);
      const name = $input.data('name');
      const value = _this.autovalues[`${donationType}_${name}`];

      $input
        .data('value', value)
        .text(`£${value}`)
        .attr('aria-checked', 'false')
        .attr('tabindex', '-1');
    });

    // Select the first button by default
    const $first = $buttons.filter(':visible').first();
    if ($first.length) {
      $first
        .addClass('ui-button--active')
        .attr('aria-checked', 'true')
        .attr('tabindex', '0');

      $valueInput.val($first.data('value'));
    }

    const $customInput = $form.find('.support-form__custom-input');
    const $customInputContainer = $form.find(
      '.support-form__custom-input-container'
    );
    $customInput.val('');
    $customInputContainer.removeClass('ui-button--active');
    $customInput
      .siblings('.support-form__custom-input-prefix')
      .css('color', '');
  }

  updateSupportSectionCopy(data, $form) {
    const $heading = $form.find('.support-form__dynamic-heading');
    const $text = $form.find('.support-form__dynamic-text');
    const overrideCopy =
      WP.supportSectionCopy && WP.supportSectionCopy[data.value];
    const defaultSectionCopy =
      WP.supportSectionCopy && WP.supportSectionCopy['default'];

    let headingText = '';
    if (overrideCopy && isNonEmptyString(overrideCopy.heading)) {
      headingText = overrideCopy.heading;
    } else if (defaultSectionCopy && isNonEmptyString(defaultSectionCopy.heading)) {
      headingText = defaultSectionCopy.heading;
    }

    let textCopy = '';
    if (overrideCopy && isNonEmptyString(overrideCopy.text)) {
      textCopy = overrideCopy.text;
    } else if (defaultSectionCopy && isNonEmptyString(defaultSectionCopy.text)) {
      textCopy = defaultSectionCopy.text;
    }

    // Update DOM elements if they exist and we have content
    if ($heading.length && headingText) {
      $heading.text(headingText);
    }
    if ($text.length && textCopy) {
      $text.text(textCopy);
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
}
