/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import debounce from 'lodash/debounce';

export class Header {
  constructor() {
    const _this = this;

    _this.scrollPosition = 0;
    _this.$navToggle = $('.site-header__nav-toggle');
    _this.$headerNav = $('.site-header-nav');
    _this.$searchToggle = $('.site-header__search-toggle');
    _this.$headerSearch = $('.site-header-search');
    _this.$searchInput = $('.site-header-search__input');
  }

  onReady() {
    const _this = this;

    _this.initScrollReveal();
    _this.bind();
  }

  bind() {
    const _this = this;

    _this.$navToggle.on('click', () => {
      _this.$headerNav.toggle();

      if (_this.$headerNav.is(':visible')) {
        _this.$headerNav.find('a').first().trigger('focus'); // focus on first link for accessibility
      }

      $(this).attr('aria-pressed', function (index, attr) {
        return attr === 'true' ? false : true;
      });

      if (_this.$headerSearch.is(':visible')) {
        _this.$headerSearch.hide();
        _this.$searchToggle.attr('aria-pressed', false);
      }
    });

    _this.$searchToggle.on('click', () => {
      _this.$headerSearch.toggle();

      $(this).attr('aria-pressed', function (index, attr) {
        return attr === 'true' ? false : true;
      });

      _this.$searchInput.trigger('focus');

      if (_this.$headerNav.is(':visible')) {
        _this.$headerNav.hide();
        _this.$navToggle.attr('aria-pressed', false);
      }
    });

    $(window).on({
      scroll: debounce(_this.handleScroll.bind(_this), 250),
      resize: debounce(_this.handleResize.bind(_this), 250),
    });
  }

  initScrollReveal() {
    this.$scrollRevealWrapper = $('.site-header__scroll-reveal');
    this.$headerLogomark = $('.site-header__logomark');
    this.isSingleArticle = $('.single-articles').length > 0;

    this.setScrollThreshold();
  }

  handleScroll() {
    const _this = this;

    const scrollTop = $(window).scrollTop();

    if (scrollTop > _this.scrollPosition && scrollTop > _this.scrollThreshold) {
      // scroll is down
      _this.$scrollRevealWrapper.css('opacity', 1);
      _this.$headerLogomark.css('opacity', 0);
    } else {
      // scroll is up
      _this.$scrollRevealWrapper.css('opacity', 0);
      _this.$headerLogomark.css('opacity', 1);
    }

    _this.scrollPosition = scrollTop;
  }

  handleResize() {
    this.setScrollThreshold();
  }

  setScrollThreshold() {
    if (this.isSingleArticle) {
      this.scrollThreshold =
        $('#single-articles-title').offset().top +
        $('#single-articles-title').height();
    } else {
      this.scrollThreshold = 150;
    }
  }
}
