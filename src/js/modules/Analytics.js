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
    $('.site-header__nav-toggle').on('click', function() {
      dataLayer.push({
        event: 'headerToggled'
      });
    });

    $('.related-posts .post').on('click', function () {
      dataLayer.push({
        event: 'relatedPostClicked',
      });
    });
  }
}
