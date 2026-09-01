// Handles paths with an existing query string and/or a hash fragment.
const appendCacheBust = (url) => {
  const hashIndex = url.indexOf('#');
  const base = hashIndex === -1 ? url : url.slice(0, hashIndex);
  const fragment = hashIndex === -1 ? '' : url.slice(hashIndex);
  const separator = base.includes('?') ? '&' : '?';

  return `${base}${separator}playwright_cache_bust=${Date.now()}${fragment}`;
};

/**
 * Navigate to a path, bypassing the Kinsta full-page cache.
 *
 * Kinsta skips the page cache when a query string is present, so appending a
 * unique one guarantees the test fetches freshly-deployed HTML even if the
 * API-based cache clear in CI fails or the environment IDs drift.
 *
 * Waits on `domcontentloaded` rather than full load so third-party embeds
 * never gate page readiness.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string} path - Path or URL to visit (e.g. '/', '/category/audio')
 * @param {object} [options] - Extra page.goto() options
 * @returns {Promise<import('@playwright/test').Response|null>}
 */
const gotoFresh = (page, path, options = {}) =>
  page.goto(appendCacheBust(path), {
    waitUntil: 'domcontentloaded',
    ...options,
  });

module.exports = gotoFresh;
