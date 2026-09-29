/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

/**
 * Click-to-load embeds. A container marked data-click-to-load holds the
 * embed URL in data-click-to-load-src and a [data-click-to-load-button];
 * nothing is requested from the embed host until that button is clicked.
 *
 * Before loading, a cancelable, bubbling `nm:click-to-load` event fires on
 * the container. A listener that calls preventDefault() stops the load —
 * the seam for a cookie consent gate.
 */
export class ClickToLoad {
  onReady() {
    document.querySelectorAll('[data-click-to-load]').forEach((container) => {
      const button = container.querySelector('[data-click-to-load-button]');

      if (!button) {
        return;
      }

      button.addEventListener('click', () => this.load(container));
    });
  }

  load(container) {
    if (container.classList.contains('is-loaded') || !container.dataset.clickToLoadSrc) {
      return;
    }

    const allowed = container.dispatchEvent(
      new CustomEvent('nm:click-to-load', { bubbles: true, cancelable: true })
    );

    if (!allowed) {
      return;
    }

    const iframe = document.createElement('iframe');

    iframe.src = container.dataset.clickToLoadSrc;
    iframe.title = container.dataset.clickToLoadTitle || '';
    iframe.setAttribute('allowfullscreen', '');

    container.appendChild(iframe);
    container.classList.add('is-loaded');

    // The button hides once loaded; keep keyboard focus on the embed rather
    // than dropping it to <body>.
    iframe.focus();
  }
}
