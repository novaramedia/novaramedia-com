/**
 * Pre-set the `cookie-approval` cookie that the theme's cookie bar writes on
 * Accept.
 *
 * The embed consent gate (src/js/modules/EmbedConsent.js) and the cookie bar
 * (src/js/modules/Utilities.js) both read this cookie on DOMContentLoaded:
 * with it set, gated third-party embeds hydrate immediately and the bar stays
 * hidden. Call before the first navigation so the page loads as a returning,
 * consented visitor.
 *
 * @param {import('@playwright/test').BrowserContext} context
 * @param {string} baseURL - Scopes the cookie to the site under test
 */
const grantCookieConsent = async (context, baseURL) => {
  await context.addCookies([
    { name: 'cookie-approval', value: 'true', url: baseURL },
  ]);
};

module.exports = grantCookieConsent;
