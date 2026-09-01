// ***********************************************
// Playwright fixtures for Novara Media theme tests
//
// Replaces the global setup that lived in
// cypress/support/e2e.js: a console error collector
// and third-party noise handling.
// ***********************************************

const { test: base, expect } = require('@playwright/test');

/**
 * Third-party embed hosts blocked by default.
 *
 * Embedded players (SoundCloud, YouTube) can stall page load for tens of
 * seconds, which used to time out the Cypress suite. Aborting their requests
 * means pages render their embed placeholders without loading the iframes, so
 * no test ever waits on a third party. Tests that need a real embed can
 * override the `blockEmbeds` fixture.
 */
const EMBED_HOSTS = [
  'youtube.com',
  'youtube-nocookie.com',
  'ytimg.com',
  'soundcloud.com',
  'sndcdn.com',
  'vimeo.com',
  'vimeocdn.com',
  'twitter.com',
  'x.com',
  'instagram.com',
  'tiktok.com',
  'spotify.com',
];

/**
 * Console messages we cannot control, filtered out of the collector.
 * Union of the filter lists in cypress/support/commands.js
 * (verifyNoConsoleErrors) and cypress/support/e2e.js (uncaught:exception).
 */
const IGNORED_ERROR_PATTERNS = [
  'ResizeObserver',
  'google-analytics',
  'gtag',
  'fbq',
  'twitter',
  'facebook',
  'soundcloud',
  'youtube',
];

const isEmbedHost = (hostname) =>
  EMBED_HOSTS.some(
    (host) => hostname === host || hostname.endsWith(`.${host}`)
  );

/**
 * Unlike the Cypress console.error spy, page.on('console') also receives
 * browser-generated errors such as "Failed to load resource: net::ERR_FAILED",
 * whose text names no host — including the ones our own embed blocking causes.
 * So the message's source URL is checked too, matching on its hostname rather
 * than as a substring so theme filenames can never be mistaken for a host.
 */
const isIgnoredError = (message, sourceUrl = '') => {
  if (IGNORED_ERROR_PATTERNS.some((pattern) => message.includes(pattern))) {
    return true;
  }

  if (!sourceUrl) return false;

  try {
    const { hostname } = new URL(sourceUrl);

    return (
      isEmbedHost(hostname) ||
      IGNORED_ERROR_PATTERNS.some((pattern) => hostname.includes(pattern))
    );
  } catch {
    return false;
  }
};

const test = base.extend({
  // Whether third-party embed hosts are blocked. Override per test/describe
  // with test.use({ blockEmbeds: false }) to exercise a real embed.
  blockEmbeds: [true, { option: true }],

  context: async ({ context, blockEmbeds }, use) => {
    if (blockEmbeds) {
      await context.route(
        (url) => isEmbedHost(url.hostname),
        (route) => route.abort()
      );
    }

    await use(context);
  },

  // Collects console errors and uncaught page exceptions for the whole test,
  // filtered down to errors the theme is responsible for. Assert on it with
  // expect(consoleErrors).toEqual([]).
  consoleErrors: [
    async ({ page }, use) => {
      const errors = [];

      page.on('console', (message) => {
        if (message.type() !== 'error') return;

        const text = message.text();
        const sourceUrl = message.location().url;

        if (!isIgnoredError(text, sourceUrl)) {
          errors.push(sourceUrl ? `${text} (${sourceUrl})` : text);
        }
      });

      page.on('pageerror', (error) => {
        if (!isIgnoredError(error.message)) errors.push(error.message);
      });

      await use(errors);
    },
    { auto: true },
  ],
});

module.exports = { test, expect };
