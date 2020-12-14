/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';

export class Header {
	constructor() {
    const _this = this;

    _this.$menuToggle = $('#menu-toggle');
    _this.$headerSub = $('#header-sub');
    _this.$searchToggle = $('#search-toggle');
    _this.$headerSearch = $('#header-search');
    _this.$searchInput = $('#search-input');
	}
	
	onReady() {
    const _this = this;

    _this.bind();

    if ($('body').hasClass('single')) {
      _this.showSinglePostTitle();
    }	
  }

	bind() {
    const _this = this;

    _this.$menuToggle.click(function() {
      _this.$headerSub.toggle();
    });

    _this.$searchToggle.click(function() {
      _this.$headerSearch.toggle();
       _this.$searchInput.focus();
    });
	}

  showSinglePostTitle() {
    const _this = this;

    _this.headerHeight = $('#header-main-wrapper').height();
    _this.$headerSinglePostTitle = $('#header-page-title');

    _this.setSinglePostTitleWidth();

    $(window).scroll(function() {
      if ($(window).scrollTop() > _this.headerHeight) {

        _this.$headerSinglePostTitle.css('opacity', 1);

      } else {

        _this.$headerSinglePostTitle.css('opacity', 0);

      }
    });

    $(window).resize(function() {
      _this.setSinglePostTitleWidth();
    });
  }

  setSinglePostTitleWidth() {
    const _this = this;

    const totalWidth = $('.col18').innerWidth();
    const navsWidth = $('#header-navs').innerWidth();

    _this.$headerSinglePostTitle.css('max-width', (totalWidth - navsWidth - 10) + 'px');
  }
}