import $ from 'jquery';

export class Highlighters {
  constructor(selector = '.ux-highlighter__line') {
    this.$lines = $(selector);
    this.$previous = null;

    this.init();
  }

  init() {
    this.resetAll();
    this.highlight(this.$lines.eq(0));

    $(window).on('scroll', () => this.updateHighlighting());
  }

  resetAll() {
    this.$lines.removeClass('font-color-black').addClass('font-color-gray');
  }

  highlight($el) {
    $el.removeClass('font-color-gray').addClass('font-color-black');
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
