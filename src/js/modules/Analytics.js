/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global dataLayer */

import $ from 'jquery';

export class Analytics {
  constructor() {
    if (typeof dataLayer !== 'undefined') {
      this.bind();
    }
  }

  bind() {
    const _this = this;

    $('.support-form-slider').on(
      'input',
      _this.debounce(function () {
        dataLayer.push({
          event: 'sliderChanged',
          target: $(this).data('target'),
          amount: this.value,
        });
      }, 250)
    );

    $('#menu-toggle').click(function () {
      // bind hamburger click
      dataLayer.push({
        event: 'headerToggled',
      });
    });

    $('.related-posts .post').click(function () {
      dataLayer.push({
        event: 'relatedPostClicked',
      });
    });
  }

  debounce(func, wait, immediate) {
    var timeout;
    return function () {
      var context = this,
        args = arguments;
      var later = function () {
        timeout = null;
        if (!immediate) {
          func.apply(context, args);
        }
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) {
        func.apply(context, args);
      }
    };
  }
}
