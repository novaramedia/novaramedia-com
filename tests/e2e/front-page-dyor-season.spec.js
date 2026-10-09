/**
 * Do Your Own Research front-page block (current season)
 *
 * Optional block: skipped when it isn't in the front-page layout. Structural
 * checks only — the season, episode and copy change with every release of the
 * show. The block's job is to drive readers to the archive, so every link
 * must go there rather than to single posts.
 */

const { test, expect } = require('./helpers/fixtures');
const checkImages = require('./helpers/checkImages');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');

// The category URL, optionally with a season anchor.
const ARCHIVE_URL_PATTERN = /\/do-your-own-research\/(#season-\d+)?$/;

test.describe('Do Your Own Research front-page block', () => {
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/');

    const present =
      (await page.getByTestId('front-page-dyor-season').count()) > 0;
    test.skip(
      !present,
      'The DYOR season block is not in the front-page layout'
    );
  });

  test('should show the hero and the newest episode', async ({ page }) => {
    const block = page.getByTestId('front-page-dyor-season');

    await expect(
      block.getByRole('img', { name: 'Do Your Own Research' })
    ).toBeVisible();
    await expect(
      block.getByRole('heading', { level: 3 }).first()
    ).not.toHaveText('');
    await checkImages(page, { scope: block });
  });

  test('should label the newest episode with its season', async ({ page }) => {
    const label = page
      .getByTestId('front-page-dyor-season')
      .getByTestId('episode-label');

    // The label is optional; the block falls back to dyor.php without a season.
    test.skip((await label.count()) === 0, 'newest episode has no label');

    await expect(label).toHaveText(/^Season \d+/);
  });

  test('should link only to the archive', async ({ page }) => {
    const hrefs = await page
      .getByTestId('front-page-dyor-season')
      .locator('a[href]')
      .evaluateAll((links) => links.map((link) => link.getAttribute('href')));

    expect(hrefs.length).toBeGreaterThan(0);
    for (const href of hrefs) {
      expect(href).toMatch(ARCHIVE_URL_PATTERN);
    }
  });

  test('should point the map button at its own season', async ({ page }) => {
    const button = page
      .getByTestId('front-page-dyor-season')
      .getByRole('link', { name: /^Explore the Season \d+ map$/ });

    // Only shown when the season has a Figma map.
    test.skip((await button.count()) === 0, 'season has no map');

    const season = (await button.textContent()).match(/Season (\d+)/)[1];
    await expect(button).toHaveAttribute(
      'href',
      new RegExp(`#season-${season}$`)
    );
  });

  test('should show four more episodes only as a full row', async ({
    page,
  }) => {
    const block = page.getByTestId('front-page-dyor-season');
    const moreHeading = block.getByRole('heading', {
      level: 4,
      name: /^More from /,
    });

    // The row needs five episodes in the season; until then, the newest only.
    test.skip(
      (await moreHeading.count()) === 0,
      'season has under five episodes'
    );

    // Each card in the row is a quarter-width grid item.
    const cards = block.locator('.grid-item.is-xxl-6');
    await expect(cards).toHaveCount(4);
  });

  test('should stay visible across viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.getByTestId('front-page-dyor-season')).toBeVisible();
    });
  });
});
