/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import Swiper from 'swiper';
import { Navigation, Autoplay, Mousewheel, FreeMode } from 'swiper/modules';

export class Carousels {
  constructor() {
    this.swipers = [];
  }

  onReady() {
    $('.ux-carousel').each((index, carousel) => {
      const $carousel = $(carousel);
      $carousel.attr('id', `ux-carousel-${index}`);

      const $navLeft = $carousel.find('.swiper-button-prev');
      const $navRight = $carousel.find('.swiper-button-next');

      const swiper = new Swiper($carousel.find('.swiper')[0], {
        modules: [Navigation, Autoplay, Mousewheel, FreeMode],
        navigation: {
          nextEl: $navRight[0],
          prevEl: $navLeft[0],
        },
        autoplay: {
          delay: 4000,
          pauseOnMouseEnter: true,
        },
        mousewheel: {
          enabled: true,
          thresholdDelta: 4,
        },
        freemode: {
          enabled: true,
          sticky: true,
        },
        slidesPerView: 'auto',
        rewind: true,
      });

      swiper.on('slideChange', (event) => {
        if (event.isBeginning) {
          $navLeft.addClass('swiper-button-prev--disabled');
        } else {
          $navLeft.removeClass('swiper-button-prev--disabled');
        }

        if (event.isEnd) {
          $navRight.addClass('swiper-button-next--disabled');
        } else {
          $navRight.removeClass('swiper-button-next--disabled');
        }
      });

      this.swipers.push(swiper);
    });
  }

  onResize() {
    this.swipers.forEach((swiper) => {
      swiper.update();
    });
  }
}
