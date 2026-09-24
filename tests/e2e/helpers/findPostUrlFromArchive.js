const gotoFresh = require('./gotoFresh');

/**
 * Serial podcast categories redirect to show pages instead of single post
 * views, so their cards are excluded. WordPress post_class() adds the
 * category-{slug} classes these selectors match. Keep in sync with
 * $serial_categories in lib/functions-hooks.php.
 */
const SERIAL_EXCLUSIONS =
  ':not(.category-foreign-agent):not(.category-committed)';

/**
 * Find a single post URL from a category archive page.
 *
 * Visits the given archive URL and returns the first post card link matching a
 * WordPress year/month/day permalink pattern. Targets article.type-post
 * elements rendered by flex-post.php; no data-testid scoping, so it works
 * whether or not testid attrs are deployed.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string} archiveUrl - The category archive path to visit (e.g. '/category/audio')
 * @returns {Promise<string|null>} The post URL, or null if none found
 */
const findPostUrlFromArchive = async (page, archiveUrl) => {
  await gotoFresh(page, archiveUrl);

  return page.locator(`article${SERIAL_EXCLUSIONS} a`).evaluateAll((links) => {
    const postUrlPattern = /\/\d{4}\/\d{2}\/\d{2}\//;
    const match = links.find((link) =>
      postUrlPattern.test(link.getAttribute('href') || '')
    );

    return match ? match.getAttribute('href') : null;
  });
};

module.exports = findPostUrlFromArchive;
