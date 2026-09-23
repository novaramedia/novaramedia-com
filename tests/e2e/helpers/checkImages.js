const { expect } = require('@playwright/test');

/**
 * Assert images have loaded successfully.
 *
 * Skips lazysizes placeholders (base64 data URIs, see add_lazysize_on_srcset()
 * in lib/functions-filters.php) and placeholder filenames, then checks the
 * remaining images decoded to non-zero dimensions. Polled because navigations
 * only wait for `domcontentloaded`, so images may still be in flight.
 *
 * @param {import('@playwright/test').Page} page
 * @param {object} [options]
 * @param {import('@playwright/test').Locator} [options.scope] - Locator to search within, defaults to the whole page
 * @param {number} [options.limit] - Only check the first N images
 */
const checkImages = async (page, { scope, limit } = {}) => {
  const images = (scope || page).locator('img');

  await expect(async () => {
    const results = await images.evaluateAll(
      (elements, max) =>
        (max ? elements.slice(0, max) : elements).map((element) => ({
          src: element.getAttribute('src'),
          naturalWidth: element.naturalWidth,
        })),
      limit
    );

    const broken = results.filter(
      ({ src, naturalWidth }) =>
        src &&
        !src.startsWith('data:') &&
        !src.includes('placeholder') &&
        naturalWidth === 0
    );

    expect(broken).toEqual([]);
  }).toPass({ timeout: 10000 });
};

module.exports = checkImages;
