/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import Swiper from 'swiper';
import { Navigation, Autoplay, Mousewheel, Pagination } from 'swiper/modules';

/**
 * GalleryCarousels - User-controlled carousel component for content showcases
 *
 * Optimized for galleries, testimonials, and featured content where user control
 * and pagination are more important than automatic progression.
 *
 * Key features:
 * - No autoplay by default (user-controlled navigation)
 * - Optional autoplay via data-autoplay attribute
 * - Pagination dots for direct slide access
 * - Infinite loop navigation
 * - Centered slides for better visual presentation
 * - Discrete slide-by-slide movement (no free mode)
 * - Mousewheel support with axis constraint
 *
 * Usage examples:
 * - Quote/testimonial carousels (like support page quotes)
 * - Featured content showcases
 * - Image galleries where user control is preferred
 * - Any carousel where pagination dots add value
 *
 * HTML setup: Use .ux-gallery-carousel class
 * Example: <section class="ux-gallery-carousel">
 * With autoplay: <section class="ux-gallery-carousel" data-autoplay="true">
 */
export class GalleryCarousels {
  constructor() {
    this.swipers = [];
    this.autoplayDelay = 4000; // Default autoplay delay in milliseconds
  }

  onReady() {
    $('.ux-gallery-carousel').each((index, carousel) => {
      const $carousel = $(carousel);
      $carousel.attr('id', `ux-gallery-carousel-${index}`);

      const $navLeft = $carousel.find('.swiper-button-prev');
      const $navRight = $carousel.find('.swiper-button-next');

      // Check for autoplay data attribute (boolean)
      const hasAutoplay =
        $carousel.data('autoplay') === true ||
        $carousel.data('autoplay') === '';

      const swiper = new Swiper($carousel.find('.swiper')[0], {
        modules: hasAutoplay
          ? [Navigation, Autoplay, Mousewheel, Pagination]
          : [Navigation, Mousewheel, Pagination],

        // Navigation arrows (optional - carousel works without them)
        navigation: {
          nextEl: $navRight[0],
          prevEl: $navLeft[0],
        },

        // Pagination dots for direct slide access
        pagination: {
          el: $carousel.find('.swiper-pagination')[0],
          clickable: true,
          bulletClass: 'swiper-pagination-bullet',
          bulletActiveClass: 'swiper-pagination-bullet-active',
        },

        // Autoplay - configurable via data-autoplay boolean attribute
        autoplay: hasAutoplay
          ? {
              delay: this.autoplayDelay,
              pauseOnMouseEnter: true,
              disableOnInteraction: false,
            }
          : false,

        // Mousewheel support with axis constraint
        mousewheel: {
          enabled: true,
          thresholdDelta: 4,
          forceToAxis: true, // Prevents diagonal scrolling interference
        },

        // Discrete slide movement (no free mode)
        freeMode: false,

        // Auto width - slides sized by CSS (e.g., 500px quote cards)
        slidesPerView: 'auto',

        // Infinite loop navigation
        loop: true,

        // Center active slide for better visual hierarchy
        centeredSlides: true,

        // Responsive behavior
        breakpoints: {
          480: {
            slidesPerView: 'auto',
          },
        },
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
