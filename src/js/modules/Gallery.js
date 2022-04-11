/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from "jquery";
import Swiper from "swiper";

export class Gallery {
  constructor() {
    this.galleryInstances = [];
  }

  onReady() {
    var _this = this;

    if ($(".swiper-container").length) {
      _this.initSwiperInstances();
    }
  }

  initSwiperInstances() {
    var _this = this;

    $(".swiper-container").each(function (index, item) {
      _this.galleryInstances[index] = new Swiper(item, {
        loop: true,
        pagination: "#gallery-pagination",
        paginationType: "fraction",
        paginationFractionRender: function (
          swiper,
          currentClassName,
          totalClassName
        ) {
          return (
            '<span class="' +
            currentClassName +
            '"></span>/<span class="' +
            totalClassName +
            '"></span>'
          );
        },
        onTap: function (swiper) {
          swiper.slideNext();
        },
      });
    });
  }
}
