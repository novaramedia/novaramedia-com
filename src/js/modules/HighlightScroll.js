import $ from 'jquery';

/**
 * Highlighters - Scroll-based text highlighting component
 *
 * Creates a scroll-triggered highlighting effect where the text element closest
 * to the center of the viewport gets highlighted as the user scrolls. Perfect
 * for progressive content reveals like funding breakdowns, feature lists, or testimonials.
 *
 * How it works:
 * 1. Finds all elements matching the selector
 * 2. Initially highlights the first element
 * 3. On scroll, calculates which element is closest to viewport center
 * 4. Removes highlight from all elements, adds it to the closest one
 *
 * Usage:
 * Basic usage with defaults (black highlight, gray reset)
 * new Highlighters('.ux-highlighter__line');
 *
 * Custom colors for different themes
 * new Highlighters('.feature__item', {
 *   highlight: 'font-color-red',
 *   reset: 'font-color-gray-light'
 * });
 *
 * Complex styling with multiple classes
 * new Highlighters('.stat__number', {
 *   highlight: 'font-color-white background-primary font-weight-bold',
 *   reset: 'font-color-gray background-transparent'
 * });
 */
export class Highlighters {
  constructor(selector = '.ux-highlighter__line', colors = {}) {
    this.$lines = $(selector);
    this.$previous = null;

    // Allow configurable color classes
    this.highlightClass = colors.highlight || 'font-color-black';
    this.resetClass = colors.reset || 'font-color-gray';

    this.init();
  }

  init() {
    this.resetAll();
    this.highlight(this.$lines.eq(0));

    $(window).on('scroll', () => this.updateHighlighting());
  }

  resetAll() {
    this.$lines.removeClass(this.highlightClass).addClass(this.resetClass);
  }

  highlight($el) {
    $el.removeClass(this.resetClass).addClass(this.highlightClass);
  }

  updateHighlighting() {
    const scrollTop = $(window).scrollTop();
    const viewportHeight = $(window).height();
    const centerY = scrollTop + viewportHeight / 2;

    let closest = null;
    let closestDistance = Infinity;

    this.$lines.each((index, element) => {
      const $el = $(element);
      const elCenter = $el.offset().top + $el.outerHeight() / 2;
      const distance = Math.abs(centerY - elCenter);

      if (distance < closestDistance) {
        closest = $el;
        closestDistance = distance;
      }
    });

    if (closest) {
      this.resetAll();
      this.highlight(closest);
    }
  }
}
