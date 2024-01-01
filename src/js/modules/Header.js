/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import debounce from 'lodash/debounce';

export class Header {
  constructor() {
    const _this = this;

    _this.showSinglePostTitle = false;
    _this.scrollPosition = 0;
    _this.$menuToggle = $('#menu-toggle');
    _this.$headerSub = $('#header-sub');
    _this.$searchToggle = $('#search-toggle');
    _this.$headerSearch = $('#header-search');
    _this.$searchInput = $('#search-input');
  }

  onReady() {
    const _this = this;

    _this.initScrollReveal();
    _this.bind();
  }

  bind() {
    const _this = this;

    _this.$menuToggle.click(function () {
      _this.$headerSub.toggle();

      if (_this.$headerSub.is(':visible')) {
        _this.$headerSub.find('a').first().focus(); // focus on first link for accessibility
      }

      $(this).attr('aria-pressed', function (index, attr) {
        return attr === 'true' ? false : true;
      });
    });

    _this.$searchToggle.click(function () {
      _this.$headerSearch.toggle();

      $(this).attr('aria-pressed', function (index, attr) {
        return attr === 'true' ? false : true;
      });

      _this.$searchInput.focus();
    });

    if (_this.showSinglePostTitle) {
      $(window).on({
        scroll: debounce(_this.handleScroll.bind(_this), 35),
        resize: debounce(_this.handleResize.bind(_this), 25),
      });
    }
  }

  initScrollReveal() {
    this.showSinglePostTitle = true;

    this.$headerSinglePostTitle = $('.site-header__scroll-reveal');
    this.$headerLogomark = $('.site-header__logomark');

    this.setScrollThreshold();
    this.setSinglePostTitleWidth();
  }

  handleScroll() {
    const _this = this;

    const scrollTop = $(window).scrollTop();

    if (scrollTop > _this.scrollPosition && scrollTop > _this.scrollThreshold) {
      // scroll is down
      _this.$headerSinglePostTitle.css('opacity', 1);
      _this.$headerLogomark.css('opacity', 0);
    } else {
      // scroll is up
      _this.$headerSinglePostTitle.css('opacity', 0);
      _this.$headerLogomark.css('opacity', 1);
    }

    _this.scrollPosition = scrollTop;
  }

  handleResize() {
    this.setScrollThreshold();
    this.setSinglePostTitleWidth();
  }

  setSinglePostTitleWidth() {
    const totalWidth = $('.col18').innerWidth();
    const navsWidth = $('#header-navs').innerWidth();

    this.$headerSinglePostTitle.css('max-width', `${totalWidth - navsWidth - 10}px`);
  }

  setScrollThreshold() {
    const $singleArticlesTitle = $('#single-articles-title');

    if ($singleArticlesTitle.length) {
      this.scrollThreshold =
        $('#single-articles-title').offset().top +
        $('#single-articles-title').height();
    } else {
      this.scrollThreshold = 150;
    }
  }
}
