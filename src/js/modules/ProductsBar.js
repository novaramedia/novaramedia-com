/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import swipeDetect from '../functions/swipeDetect';
// import debounce from 'lodash/debounce';

export class ProductsBar {
  constructor() {
    this.$productsBar = $('.front-page__products-bar');
    this.$inner = this.$productsBar.find('.products-bar__inner');
    this.$navLeft = this.$productsBar.find('.products-bar__nav-left');
    this.$navRight = this.$productsBar.find('.products-bar__nav-right');
    this.$navItems = this.$productsBar.find('.products-bar__item');

    this.carouselLength = this.$navItems.length;
    this.carouselPosition = 0;
  }

  onReady() {
    const _this = this;

    if (this.$productsBar.length !== 0) {
      _this.itemWidth = _this.$navItems.eq(1).outerWidth(true);

      _this.bind();
    }
  }

  bind() {
    const _this = this;

    this.$navLeft.on({
      click: function () {
        _this.animateToPosition(_this.carouselPosition - 1);
      },
    });

    this.$navRight.on({
      click: function () {
        _this.animateToPosition(_this.carouselPosition + 1);
      },
    });

    // could also trottle mouseover triggers as well? https://lodash.com/docs/4.17.15#throttle

    swipeDetect('.front-page__products-bar', (direction) => {
      if (direction === 'left') {
        _this.animateToPosition(_this.carouselPosition + 1);
      } else if (direction === 'right') {
        _this.animateToPosition(_this.carouselPosition - 1);
      }
    });
  }

  animateToPosition(position) {
    const _this = this;

    if (position < 0) {
      position = 0;
    }

    if (position > _this.carouselLength - 1) {
      position = _this.carouselLength - 1;
    }

    _this.$inner.css({
      transform: `translateX(-${position * _this.itemWidth}px)`,
    });

    if (position !== 0) {
      _this.$navLeft.removeClass('products-bar__nav-left--disabled');
    } else {
      _this.$navLeft.addClass('products-bar__nav-left--disabled');
    }

    _this.carouselPosition = position;
  }
}
