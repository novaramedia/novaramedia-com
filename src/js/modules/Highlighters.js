import $ from 'jquery';
import debounce from 'lodash/debounce';

/**
 * Highlighters - Scroll-based text highlighting component
 *
 * Creates a scroll-triggered highlighting effect where the text element closest
 * to the center of the viewport gets an --active BEM modifier class as the user scrolls.
 * Perfect for progressive content reveals like funding breakdowns, feature lists, or testimonials.
 *
 * How it works:
 * 1. Finds all elements matching the selector
 * 2. Initially activates the first element
 * 3. On scroll, calculates which element is closest to viewport center
 * 4. Removes --active class from all elements, adds it to the closest one
 *
 * Usage:
 * new Highlighters('.ux-highlighter__line');
 * new Highlighters('.feature__item');
 * new Highlighters('.stat__number');
 *
 * The component will add/remove the --active BEM modifier:
 * .ux-highlighter__line--active
 * .feature__item--active
 * .stat__number--active
 */
export class Highlighters {
  constructor(selector = '.ux-highlighter__line') {
    this.$lines = $(selector);
    this.activeClass = `${selector.replace(/^\./, '')}--active`;

    this.init();
  }

  init() {
    this.activate(this.$lines.eq(0));
  }

  onReady() {
    $(window).on(
      'scroll',
      debounce(() => this.updateHighlighting(), 16)
    );
  }

  resetAll() {
    this.$lines.removeClass(this.activeClass);
  }

  activate($element) {
    $element.addClass(this.activeClass);
  }

  updateHighlighting() {
    const scrollTop = $(window).scrollTop();
    const viewportHeight = $(window).height();
    const centerY = scrollTop + viewportHeight / 2;

    let closest = null;
    let closestDistance = Infinity;

    this.$lines.each((index, element) => {
      const $element = $(element);
      const elementCenter = $element.offset().top + $element.outerHeight() / 2;
      const distance = Math.abs(centerY - elementCenter);

      if (distance < closestDistance) {
        closest = $element;
        closestDistance = distance;
      }
    });

    if (closest) {
      this.resetAll();
      this.activate(closest);
    }
  }
}
