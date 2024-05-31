/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import swipeDetect from '../functions/swipeDetect';
import debounce from 'lodash/debounce';
import clamp from 'lodash/clamp';
import reduce from 'lodash/reduce';

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
  }
}

class Carousel {
  constructor(carousel) {
    this.$carousel = $(carousel);
    this.$wrapper = this.$carousel.find('.ux-carousel__wrapper');
    this.$inner = this.$carousel.find('.ux-carousel__inner');
    this.$items = this.$carousel.find('.ux-carousel__item');
    this.$navLeft = this.$carousel.find('.ux-carousel__nav-left');
    this.$navRight = this.$carousel.find('.ux-carousel__nav-right');

    this.carouselLength = this.$items.length;
    this.carouselPosition = 0;
    this.atEnd = false;
    this.isAutoplayer = this.$carousel.hasClass('ux-carousel--autoplay');
    this.isAutoplayCancelled = false;
    this.autoplayTimeout = 4000;
  }

  onReady() {
    const _this = this;

    _this.setSizes();
    _this.bind();

    if (_this.isAutoplayer) {
      _this.autoplay();
    }
  }

  onResize() {
    const _this = this;

    _this.setSizes();
    _this.animateToPosition(_this.carouselPosition);
  }

  setSizes() {
    const _this = this;

    _this.itemWidth = _this.$items.eq(1).outerWidth(true);
    _this.itemMarginWidth = _this.itemWidth - _this.$items.eq(2).outerWidth();
    _this.carouselWidth = _this.$carousel.outerWidth();
    _this.wrapperSpacing =
      _this.$wrapper.outerWidth(true) - _this.$wrapper.width();
    _this.innerContainerWidth = reduce(
      _this.$items,
      (sum, item) => {
        return sum + $(item).outerWidth(true);
      },
      0
    );
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

    swipeDetect(`#${_this.$carousel.attr('id')}`, (direction) => {
      if (direction === 'left') {
        _this.animateToPosition(_this.carouselPosition + 1);
        _this.cancelAutoplay();
      } else if (direction === 'right') {
        _this.animateToPosition(_this.carouselPosition - 1);
        _this.cancelAutoplay();
      }
    });

    _this.$carousel[0].addEventListener(
      'wheel',
      debounce(_this.handleWheel.bind(_this), 100)
    );

    $(window).on({
      resize: debounce(_this.onResize.bind(_this), 500),
    });

    if (_this.isAutoplayer) {
      _this.$carousel.on({
        mouseenter: function () {
          _this.pauseAutoplay();
        },
        mouseleave: function () {
          if (!_this.isAutoplayCancelled) {
            _this.autoplay();
          }
        },
      });

      _this.$carousel
        .find('.ux-carousel__nav-left, .ux-carousel__nav-right')
        .on({
          click: function () {
            _this.cancelAutoplay();
          },
        });
    }
  }

  autoplay() {
    const _this = this;

    this.autoplayInterval = setInterval(() => {
      if (_this.atEnd) {
        _this.animateToPosition(0);
        return;
      }

      _this.animateToPosition(_this.carouselPosition + 1);
    }, this.autoplayTimeout);
  }

  pauseAutoplay() {
    clearInterval(this.autoplayInterval);
  }

  cancelAutoplay() {
    this.pauseAutoplay();
    this.isAutoplayCancelled = true;
  }

  handleWheel(event) {
    const _this = this;

    if (event.deltaX > 0) {
      _this.animateToPosition(_this.carouselPosition + 1);
    } else if (event.deltaX < 0) {
      _this.animateToPosition(_this.carouselPosition - 1);
    }

    this.cancelAutoplay();
  }

  animateToPosition(position) {
    const _this = this;

    if (position < 0) {
      position = 0;
    }

    if (position > _this.carouselLength - 1) {
      position = _this.carouselLength - 1;
    }

    const maxScroll =
      _this.innerContainerWidth - _this.carouselWidth + _this.wrapperSpacing;

    const isOverflowing = _this.carouselPosition * _this.itemWidth > maxScroll;

    if (isOverflowing && position - _this.carouselPosition > 0) {
      return;
    }

    const willOverflow = position * _this.itemWidth > maxScroll;

    const scrollDistance = clamp(position * _this.itemWidth, 0, maxScroll);

    _this.$inner.css({
      transform: `translateX(-${scrollDistance}px)`,
    });

    if (position !== 0) {
      _this.$navLeft.removeClass('ux-carousel__nav-left--disabled');
    } else {
      _this.$navLeft.addClass('ux-carousel__nav-left--disabled');
    }

    if (!willOverflow) {
      _this.$navRight.removeClass('ux-carousel__nav-right--disabled');
      _this.atEnd = false;
    } else {
      _this.$navRight.addClass('ux-carousel__nav-right--disabled');
      _this.atEnd = true;
    }

    _this.carouselPosition = position;
  }
}
