/**
 * Do Your Own Research archive
 *
 * Structural checks only: one block per season with published episodes,
 * newest season first; maps wait for a click; the archive is unpaged.
 * Figma is in the fixture's blocked embed hosts, so a loaded map is
 * asserted by its iframe src, not by what Figma renders.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const ARCHIVE = '/category/do-your-own-research/';

test.describe('Do Your Own Research archive', () => {
  test('shows season blocks, newest season first', async ({ page }) => {
    await gotoFresh(page, ARCHIVE);

    const seasons = await page.getByTestId('dyor-season').evaluateAll((blocks) =>
      blocks.map((block) => Number(block.dataset.season))
    );

    expect(seasons.length).toBeGreaterThan(0);
    expect(seasons).toEqual([...seasons].sort((a, b) => b - a));
  });

  test('season maps load only on click', async ({ page }) => {
    await gotoFresh(page, ARCHIVE);

    const map = page.getByTestId('dyor-season-map').first();

    await expect(map).toBeVisible();
    await expect(page.locator('[data-testid="dyor-season-map"] iframe')).toHaveCount(0);

    // Consent contract: a listener that cancels nm:click-to-load stops the load.
    await page.evaluate(() => {
      document.addEventListener('nm:click-to-load', (event) => event.preventDefault(), { once: true });
    });
    await map.locator('[data-click-to-load-button]').click();

    await expect(map.locator('iframe')).toHaveCount(0);
    await expect(map).not.toHaveClass(/is-loaded/);

    await map.locator('[data-click-to-load-button]').click();

    await expect(map.locator('iframe')).toHaveAttribute('src', /^https:\/\/embed\.figma\.com\/board\//);

    // The button hides once loaded; keyboard focus moves to the map, not <body>.
    expect(await page.evaluate(() => document.activeElement && document.activeElement.tagName)).toBe('IFRAME');
  });

  // The category URL and the /dyor/ vanity rewrite resolve through different
  // query vars (category_name vs cat), so each needs its own check.
  for (const archivePath of [ARCHIVE, '/dyor/']) {
    test(`paged archive URLs redirect to the archive: ${archivePath}`, async ({ request }) => {
      const response = await request.get(`${archivePath}page/2/?playwright_cache_bust=${Date.now()}`, {
        maxRedirects: 0,
      });

      expect(response.status()).toBe(301);
      expect(new URL(response.headers()['location']).pathname).toBe(archivePath);
    });
  }
});
