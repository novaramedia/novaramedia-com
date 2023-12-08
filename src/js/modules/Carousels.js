/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import swipeDetect from '../functions/swipeDetect';
// import debounce from 'lodash/debounce';

export class Carousels {
  constructor() {
    this.carousels = [];

    $('.ux-carousel').each((index, carousel) => {
      $(carousel).attr('id', `ux-carousel-${index}`);
      this.carousels.push(new Carousel(carousel));
    });
  }

  onReady() {
    const _this = this;

    _this.carousels.forEach((carousel) => {
      carousel.onReady();
    });

    console.log('Carousels ready', _this.carousels);
  }
}

class Carousel {
  constructor(carousel) {
    this.$carousel = $(carousel);
    this.$inner = this.$carousel.find('.ux-carousel__inner');
    this.$items = this.$carousel.find('.ux-carousel__item');
    this.$navLeft = this.$carousel.find('.ux-carousel__nav-left');
    this.$navRight = this.$carousel.find('.ux-carousel__nav-right');

    this.carouselLength = this.$items.length;
    this.carouselPosition = 0;
  }

  onReady() {
    const _this = this;

    _this.itemWidth = _this.$items.eq(1).outerWidth(true);

    _this.bind();
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

    swipeDetect(`#${_this.$carousel.attr('id')}`, (direction) => {
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
      _this.$navLeft.removeClass('ux-carousel__nav-left--disabled');
    } else {
      _this.$navLeft.addClass('ux-carousel__nav-left--disabled');
    }

    if (position !== _this.carouselLength - 1) {
      _this.$navRight.removeClass('ux-carousel__nav-right--disabled');
    } else {
      _this.$navRight.addClass('ux-carousel__nav-right--disabled');
    }

    _this.carouselPosition = position;
  }
}
