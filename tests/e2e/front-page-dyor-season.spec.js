/**
 * Do Your Own Research front-page block (current season)
 *
 * Optional block: skipped when it isn't in the front-page layout. Structural
 * checks only — the season, episode and copy change with every release of the
 * show. The block's job is to drive readers to the archive, so every link
 * must go there rather than to single posts. Checks for optional parts of the
 * block (season label, map button, the more-episodes row) skip just their own
 * step when that part is absent.
 */

const { test, expect } = require('./helpers/fixtures');
const checkImages = require('./helpers/checkImages');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');

// The category URL, optionally with a season anchor.
const ARCHIVE_URL_PATTERN = /\/do-your-own-research\/(#season-\d+)?$/;

test.describe('Do Your Own Research front-page block', () => {
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the front-page block', async ({ page }) => {
    await gotoFresh(page, '/');

    const present =
      (await page.getByTestId('front-page-dyor-season').count()) > 0;
    test.skip(
      !present,
      'The DYOR season block is not in the front-page layout'
    );

    const block = page.getByTestId('front-page-dyor-season');

    await test.step('shows the hero and the newest episode', async () => {
      await expect(
        block.getByRole('img', { name: 'Do Your Own Research' })
      ).toBeVisible();
      await expect(
        block.getByRole('heading', { level: 3 }).first()
      ).not.toHaveText('');
      await checkImages(page, { scope: block });
    });

    await test.step('labels the newest episode with its season', async (step) => {
      const label = block.getByTestId('episode-label');

      // The label is optional; the block falls back to dyor.php without a season.
      step.skip((await label.count()) === 0, 'newest episode has no label');

      await expect(label).toHaveText(/^Season \d+/);
    });

    await test.step('links only to the archive', async () => {
      const hrefs = await block
        .locator('a[href]')
        .evaluateAll((links) => links.map((link) => link.getAttribute('href')));

      expect(hrefs.length).toBeGreaterThan(0);
      for (const href of hrefs) {
        expect(href).toMatch(ARCHIVE_URL_PATTERN);
      }
    });

    await test.step('points the map button at its own season', async (step) => {
      const button = block.getByRole('link', {
        name: /^Explore the Season \d+ map$/,
      });

      // Only shown when the season has a Figma map.
      step.skip((await button.count()) === 0, 'season has no map');

      const season = (await button.textContent()).match(/Season (\d+)/)[1];
      await expect(button).toHaveAttribute(
        'href',
        new RegExp(`#season-${season}$`)
      );
    });

    await test.step('shows four more episodes only as a full row', async (step) => {
      const moreHeading = block.getByRole('heading', {
        level: 4,
        name: /^More from /,
      });

      // The row needs five episodes in the season; until then, the newest only.
      step.skip(
        (await moreHeading.count()) === 0,
        'season has under five episodes'
      );

      // Each card in the row is a quarter-width grid item.
      const cards = block.locator('.grid-item.is-xxl-6');
      await expect(cards).toHaveCount(4);
    });

    await test.step('stays visible across viewports', async () => {
      await testResponsive(page, async () => {
        await expect(page.getByTestId('front-page-dyor-season')).toBeVisible();
      });
    });
  });
});
