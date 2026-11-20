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
 * Throws on a non-2xx response, matching the former cy.visit() default.
 * page.goto() itself resolves on a 404 or 500, which would let a broken
 * deployment skip the single-post specs (archive lookup returns null) or run
 * page specs against an error page. Pass { failOnStatusCode: false } to visit
 * an error page deliberately.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string} path - Path or URL to visit (e.g. '/', '/category/audio')
 * @param {object} [options] - Extra page.goto() options, plus failOnStatusCode
 * @param {boolean} [options.failOnStatusCode=true]
 * @returns {Promise<import('@playwright/test').Response|null>}
 */
const gotoFresh = async (
  page,
  path,
  { failOnStatusCode = true, ...options } = {}
) => {
  const response = await page.goto(appendCacheBust(path), {
    waitUntil: 'domcontentloaded',
    ...options,
  });

  if (failOnStatusCode && response && !response.ok()) {
    throw new Error(
      `gotoFresh: HTTP ${response.status()} for ${response.url()}`
    );
  }

  return response;
};

module.exports = gotoFresh;
