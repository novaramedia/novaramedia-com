/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';

export class Support {
  onReady() {
    const _this = this;

    if ($('.support-section').length) {
      _this.bind();
    }
  }

  bind() {
    $('.support-form').each(function (index, value) {
      const $form = $(value);

      const $valueInput = $form.find('.support-form__value-input').first();

      $form.find('.support-form__button').on({
        click: function (event) {
          event.preventDefault();

          const $button = $(this);
          const data = $button.data();

          if (data.action === 'set-type') {
            $form.attr('action', data.value);

            $form
              .find('.support-form__button[data-action="set-type"]')
              .removeClass('support-form__button--active');

            $button.addClass('support-form__button--active');
          } else if (data.action === 'set-value') {
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
        input: function (event) {
          event.preventDefault();

          $valueInput.val(event.target.value);

          $form
            .find('.support-form__button[data-action="set-value"]')
            .removeClass('support-form__button--active');

          $(this).addClass('support-form__button--active');
        },
      });
    });
  }

  numberWithCommas(x) {
    // https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript#2901298
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }
}
