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
    this.scrollers.forEach((scroller) => {
      scroller.onReady();
    });
  }
}

class Scroller {
  constructor(scroller) {
    this.$scroller = $(scroller);
    this.$inner = this.$scroller.find('.ux-scroller__inner');
    this.$fadeBottom = this.$scroller.find('.ux-scroller__fade-bottom');
    this.$scroller.addClass('ux-scroller--at-top');
  }

  onReady() {
    this.innerHeight = this.$inner.outerHeight(true);
    this.bind();
  }

  onResize() {
    this.innerHeight = this.$inner.outerHeight(true);
  }

  bind() {
    const _this = this;

    _this.$inner[0].addEventListener(
      'scroll',
      debounce(_this.handleScroll.bind(_this), 50)
    );

    $(window).on('resize', debounce(_this.onResize.bind(_this), 50));
  }

  handleScroll() {
    if (this.$inner[0].scrollTop === 0) {
      this.$scroller
        .removeClass('ux-scroller--at-bottom')
        .addClass('ux-scroller--at-top');
    } else if (
      this.$inner[0].scrollTop + this.innerHeight >=
      this.$inner[0].scrollHeight
    ) {
      this.$scroller
        .removeClass('ux-scroller--at-top')
        .addClass('ux-scroller--at-bottom');
    } else {
      this.$scroller.removeClass('ux-scroller--at-top ux-scroller--at-bottom');
    }
  }
}
