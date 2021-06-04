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
            
      $form.on('submit', function(event) {
        event.preventDefault();
        
        const data = $form.serialize();
        
        $form.addClass('email-signup__form--processing');
        $formInputs.prop('disabled', true);
        $form.removeClass('email-signup__form--failed');
                
        _this.forms[index] = $.post(url, data)
          .done(function(data, textStatus, jqXHR) {
            $form.removeClass('email-signup__form--processing');
            $form.addClass('email-signup__form--completed');
          })
          .fail(function(jqXHR) {
            $form.removeClass('email-signup__form--processing');
            $formInputs.prop('disabled', false);

            $form.addClass('email-signup__form--failed');
            
            try {
              const response = JSON.parse(jqXHR.responseText);              
              $feedbackMessageSpan.text(response.message); // this needs to target child of parent
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
  }
  
}