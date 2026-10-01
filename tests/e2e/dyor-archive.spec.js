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

  test('support box between seasons keeps its own copy across donation modes', async ({ page }) => {
    await gotoFresh(page, ARCHIVE);

    test.skip((await page.getByTestId('dyor-season').count()) < 2, 'needs two or more seasons with posts');

    const seasonForm = page.locator('.dyor-archive__season-support form.support-form').first();

    await expect(seasonForm).toBeVisible();
    // The copy fields are optional; blank means the site-wide copy, with no attribute.
    test.skip(!(await seasonForm.getAttribute('data-support-copy')), 'no DYOR support copy saved');

    // Each field is optional per mode; check whichever are saved.
    const copy = JSON.parse(await seasonForm.getAttribute('data-support-copy'));
    const heading = seasonForm.locator('.support-form__text-desktop .support-form__dynamic-heading');
    const text = seasonForm.locator('.support-form__text-desktop .support-form__dynamic-text');

    const expectModeCopy = async (mode) => {
      if (copy[mode] && copy[mode].heading) {
        await expect(heading).toHaveText(copy[mode].heading);
      }
      if (copy[mode] && copy[mode].text) {
        await expect(text).toHaveText(copy[mode].text);
      }
    };

    // Rendered state first, then each mode after the toggle rewrites the copy.
    const activeMode = await seasonForm.locator('.support-form__schedule-desktop .ui-button--active').getAttribute('data-value');
    await expectModeCopy(activeMode);

    for (const mode of ['oneoff', 'regular']) {
      await seasonForm.locator(`.support-form__schedule-desktop [data-value="${mode}"]`).click();
      await expectModeCopy(mode);
    }
  });

  // The category URL and the /dyor/ vanity rewrite resolve through different
  // query vars (category_name vs cat), so each needs its own check.
  for (const archivePath of [ARCHIVE, '/dyor/']) {
    test(`paged archive URLs redirect to the archive: ${archivePath}`, async ({ page }) => {
      await gotoFresh(page, `${archivePath}page/2/`, { failOnStatusCode: false });

      expect(new URL(page.url()).pathname).toBe(archivePath);
    });
  }
});
