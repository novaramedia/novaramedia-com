/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global */


import $ from 'jquery';

export class MailchimpSignup {
  constructor() {
    this.forms = [];
    this.bind();
  }

  bind() {
    var _this = this;

    $('.email-signup__form').each(function(index, form) {
      const $form = $(form);
      const url = $(form).attr('action');
      const $formInputs = $form.find('input');
      const $feedbackMessageSpan = $form.find('.email-signup__feedback-message');

      _this.forms[index] = $form;

      $form.on('submit', function(event) {
        event.preventDefault();

        const data = $form.serialize();

        $form.addClass('email-signup__form--processing');
        $formInputs.prop('disabled', true);
        $form.removeClass('email-signup__form--failed');

        $.post(url, data)
          .done(function() {
            $form.removeClass('email-signup__form--processing');
            $form.addClass('email-signup__form--completed');
          })
          .fail(function(jqXHR) {
            $form.removeClass('email-signup__form--processing');
            $formInputs.prop('disabled', false);

            $form.addClass('email-signup__form--failed');

            if (jqXHR.statusText === 'error') {
              $feedbackMessageSpan.text('General error');
            }

            try {
              const response = JSON.parse(jqXHR.responseText);

              $feedbackMessageSpan.text(response.message);
            } catch(error) {
              $feedbackMessageSpan.text('General error');
            }
          })
          .always(function() {
            $form.removeClass('email-signup__form--processing');
            $formInputs.prop('disabled', false);
          });
      });
    });

    $('.email-signup__feedback-failed').each(function(index, element) {
      const $element = $(element);

      $element.on('click', function() {
        _this.forms[index].removeClass('email-signup__form--failed');
      });
    });
  }

}
