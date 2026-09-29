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

    // The button hides once loaded; keyboard focus moves to the map, not <body>.
    expect(await page.evaluate(() => document.activeElement && document.activeElement.tagName)).toBe('IFRAME');
  });

  test('support box between seasons keeps its own copy across donation modes', async ({ page }) => {
    await gotoFresh(page, ARCHIVE);

    test.skip((await page.getByTestId('dyor-season').count()) < 2, 'needs two or more seasons with posts');

    const seasonForm = page.locator('.dyor-archive__season-support form.support-form').first();

    const heading = seasonForm.locator('.support-form__text-desktop .support-form__dynamic-heading');
    const contextHeading = (await heading.textContent()).trim();
    const siteHeading = (await page.locator('form.support-form').last().locator('.support-form__text-desktop .support-form__dynamic-heading').textContent()).trim();

    await expect(seasonForm).toHaveAttribute('data-support-copy', /heading/);
    expect(contextHeading).not.toBe(siteHeading);

    await seasonForm.locator('.support-form__schedule-desktop [data-value="oneoff"]').click();
    await expect(heading).toHaveText(contextHeading);
  });

  test('paged archive URLs redirect to the archive', async ({ page }) => {
    await gotoFresh(page, `${ARCHIVE}page/2/`, { failOnStatusCode: false });

    expect(new URL(page.url()).pathname).toBe(ARCHIVE);
  });
});
