/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import debounce from 'lodash/debounce';

export class Scrollers {
  constructor() {
    this.scrollers = [];

    $('.ux-scroller').each((index, scroller) => {
      $(scroller).attr('id', `ux-scroller-${index}`);
      this.scrollers.push(new Scroller(scroller));
    });
  }

  onReady() {
    const _this = this;

    _this.scrollers.forEach((scroller) => {
      scroller.onReady();
    });
  }
}

class Scroller {
  constructor(scroller) {
    this.$scroller = $(scroller);
    this.$inner = this.$scroller.find('.ux-scroller__inner');
    this.$fadeBottom = this.$scroller.find('.ux-scroller__fade-bottom');
  }

  onReady() {
    const _this = this;

    _this.bind();
  }

  onResize() {
    // const _this = this;
  }

  bind() {
    const _this = this;

    _this.$inner[0].addEventListener(
      'scroll',
      debounce(_this.handleScroll.bind(_this), 50)
    );
  }

  handleScroll() {
    const _this = this;

    if (_this.$inner[0].scrollTop === 0) {
      _this.$scroller
        .removeClass('ux-scroller--at-bottom')
        .addClass('ux-scroller--at-top');
    } else if ((_this.$inner[0].scrollTop + _this.$inner.outerHeight(true)) >= _this.$inner[0].scrollHeight) {
      _this.$scroller
        .removeClass('ux-scroller--at-top')
        .addClass('ux-scroller--at-bottom');
    } else {
      _this.$scroller
        .removeClass('ux-scroller--at-top', 'ux-scroller--at-bottom');
    }

  }
}
