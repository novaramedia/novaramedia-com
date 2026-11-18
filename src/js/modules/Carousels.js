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
          addIcons: false,
        },
        autoplay: {
          delay: 4000,
          pauseOnMouseEnter: true,
        },
        mousewheel: {
          enabled: true,
          thresholdDelta: 4,
        },
        freeMode: {
          enabled: true,
          sticky: true,
        },
        slidesPerView: 'auto',
        rewind: true,
      });

      // The --disabled classes hide an arrow at either end of the carousel. A hidden
      // arrow also leaves the tab order so keyboard users never land on an
      // invisible button (rewind mode means Swiper never disables them itself).
      swiper.on('slideChange', (event) => {
        $navLeft
          .toggleClass('swiper-button-prev--disabled', event.isBeginning)
          .attr('tabindex', event.isBeginning ? '-1' : null);

        $navRight
          .toggleClass('swiper-button-next--disabled', event.isEnd)
          .attr('tabindex', event.isEnd ? '-1' : null);
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
