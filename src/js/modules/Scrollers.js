/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import debounce from 'lodash/debounce';

// Sub-pixel scroll positions (zoom, fractional layout) can leave scrollTop a fraction
// short of the true end, so "at the edge" allows this much slack.
const EDGE_TOLERANCE_PX = 1;

export class Scrollers {
  constructor() {
    this.scrollers = [];

    $('.ux-scroller').each((index, scroller) => {
      $(scroller).attr('id', `ux-scroller-${index}`);
      this.scrollers.push(new Scroller(scroller));
    });
  }

  onReady() {
    this.scrollers.forEach((scroller) => {
      scroller.onReady();
    });
  }
}

/**
 * Toggles the fade overlays on a .ux-scroller from its live scroll position:
 * --at-top hides the top fade, --at-bottom hides the bottom fade. A list that
 * fits without scrolling is at both edges, so it shows neither.
 *
 * Measurements are read on every update rather than cached, because the
 * scroller's height can change after load (images above or beside it
 * finishing, a flex layout settling) without a window resize.
 */
class Scroller {
  constructor(scroller) {
    this.$scroller = $(scroller);
    this.inner = this.$scroller.find('.ux-scroller__inner')[0];
  }

  onReady() {
    if (!this.inner) {
      return;
    }

    this.update();
    this.bind();
  }

  bind() {
    const update = debounce(this.update.bind(this), 50);

    this.inner.addEventListener('scroll', update, { passive: true });

    if ('ResizeObserver' in window) {
      // Watches the viewport box and the list itself, so a change to either
      // re-evaluates whether there is anything to scroll.
      const observer = new ResizeObserver(update);
      observer.observe(this.inner);
      Array.from(this.inner.children).forEach((child) => observer.observe(child));
    } else {
      $(window).on('resize', update);
    }
  }

  update() {
    const { scrollTop, scrollHeight, clientHeight } = this.inner;
    const atTop = scrollTop <= EDGE_TOLERANCE_PX;
    const atBottom = scrollTop + clientHeight >= scrollHeight - EDGE_TOLERANCE_PX;

    this.$scroller
      .toggleClass('ux-scroller--at-top', atTop)
      .toggleClass('ux-scroller--at-bottom', atBottom);
  }
}
