/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import Swiper from 'swiper';
import { Navigation, Autoplay, Mousewheel, FreeMode, Pagination } from 'swiper/modules';

export class Carousels {
  constructor() {
    this.carousels = [];

    $('.ux-carousel').each((index, carousel) => {
      $(carousel).attr('id', `ux-carousel-${index}`);

      if ($(carousel).hasClass('alt')) {
        this.carousels.push(new CarouselAlt(carousel));
      } else {
        this.carousels.push(new Carousel(carousel));
      }
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
      modules: [Navigation, Autoplay, Mousewheel, FreeMode],
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
        thresholdDelta: 4,
      },
      freemode: {
        enabled: true,
        sticky: true,
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

  onReady() {}

  onResize() {}
}

class CarouselAlt {
  constructor(carousel) {
    this.$carousel = $(carousel);
    this.$navLeft = this.$carousel.find('.swiper-button-prev');
    this.$navRight = this.$carousel.find('.swiper-button-next');


    this.swiper = new Swiper(this.$carousel.find('.swiper')[0], {
      modules: [Navigation, Mousewheel, Pagination],
      navigation: {
        nextEl: this.$navRight[0],
        prevEl: this.$navLeft[0],
      },
      pagination: {
        el: this.$carousel.find('.swiper-pagination')[0],
        clickable: true,
        bulletClass: 'swiper-pagination-bullet',
        bulletActiveClass: 'swiper-pagination-bullet-active',
      },
      autoplay: false,
        mousewheel: {
        enabled: true,
        thresholdDelta: 4,
        forceToAxis: true,
      },
      freeMode: false,
      slidesPerView: 2,
      loop: true,
      centeredSlides: true,
      breakpoints: {
        480: {
          slidesPerView: 1,
        },
      },
    });
     console.log(this.$carousel.find('.swiper-pagination')[0]);

  }

  onReady() {}
}
