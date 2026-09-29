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

    await map.locator('[data-click-to-load-button]').click();

    await expect(map.locator('iframe')).toHaveAttribute('src', /^https:\/\/embed\.figma\.com\/board\//);
  });

  test('paged archive URLs redirect to the archive', async ({ page }) => {
    await gotoFresh(page, `${ARCHIVE}page/2/`, { failOnStatusCode: false });

    expect(new URL(page.url()).pathname).toBe(ARCHIVE);
  });
});
