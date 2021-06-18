/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';

export class Support {
  constructor() {
    this.$progressBar = $('#progress-bar');
  }

  onReady() {
    const _this = this;

    if ($('.support-section').length) {
      _this.bind();
    }

    if (_this.$progressBar.length) {
      _this.initProgressBar();
    }
  }

  bind() {
    const _this = this;

    $('.support-form').each(function(index, value) {
      const $form = $(value);
      
      const $valueInput = $form.find('.support-form__value-input').first();
      
      $form.find('.support-form__button').on({
        'click': function(event) {
          event.preventDefault();
          
          const $button = $(this);
          const data = $button.data();
          
          if (data.action === 'set-type') {
            $form.attr('action', data.value);
            
            $form.find('.support-form__button[data-action="set-type"]').removeClass('support-form__button--active');
            
            $button.addClass('support-form__button--active');
          } else if (data.action === 'set-value') {
            $valueInput.val(data.value);
            
            $form.find('.support-form__button[data-action="set-value"]').removeClass('support-form__button--active');
            $('.support-form__custom-input').removeClass('support-form__button--active');
            
            $button.addClass('support-form__button--active');
          }
        }
      });
      
      $form.find('.support-form__custom-input').on({
        'input': function(event) {
          event.preventDefault();
          
          $valueInput.val(event.target.value);
                    
          $form.find('.support-form__button[data-action="set-value"]').removeClass('support-form__button--active');
          
          $(this).addClass('support-form__button--active');
        }
      })
    })
  }
  
  initProgressBar() {
    const _this = this;

    _this.$progressBar.css('width', '0%');

    $.get('https://payment.novaramedia.com/api/goal', null, function(data, textStatus) {

      if (textStatus === 'success') {
        var percent = data.percent;

        if (percent > 1) {
          percent = 1;
        }

        $('#progress-bar-row').slideDown(1250, function() {
          _this.$progressBar.css('width', (percent * 100) + '%');

          _this.displayProgressText(data);
        });
      }

    }, 'json');
  }

  displayProgressText(data) {
    var _this = this;

    var total = data.total / data.percent;
    var text = ': £' + _this.numberWithCommas(data.total) + ' of £' + _this.numberWithCommas(Math.round(total));

    $('#progress-text').text(text);
  }

  numberWithCommas(x) {
    // https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript#2901298
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }
}