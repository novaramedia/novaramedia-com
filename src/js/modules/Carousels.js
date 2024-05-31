/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import Swiper from 'swiper';
import { Navigation, Autoplay, Mousewheel } from 'swiper/modules';

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
    this.$navLeft = this.$carousel.find('.swiper-button-prev');
    this.$navRight = this.$carousel.find('.swiper-button-next');

    this.swiper = new Swiper(this.$carousel.find('.swiper')[0], {
      modules: [Navigation, Autoplay, Mousewheel],
      navigation: {
        nextEl: this.$navRight[0],
        prevEl: this.$navLeft[0],
      },
      autoplay: {
        delay: 4000,
        pauseOnMouseEnter: true,
      },
      mousewheel: {
        enabled: true,
      },
      slidesPerView: 'auto',
      rewind: true,
    });

    this.swiper.on('slideChange', (event) => {
      if (event.isBeginning) {
        this.$navLeft.addClass('swiper-button-prev--disabled');
      } else {
        this.$navLeft.removeClass('swiper-button-prev--disabled');
      }

      if (event.isEnd) {
        this.$navRight.addClass('swiper-button-next--disabled');
      } else {
        this.$navRight.removeClass('swiper-button-next--disabled');
      }
    });
  }

  onReady() {

  }

  onResize() {

  }
}
