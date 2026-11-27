/**
 * If I Speak front-page block
 *
 * Optional block: skipped when it isn't in the front-page layout. Structural
 * checks only — episode titles and the blurb are editorial and change weekly.
 */

const { test, expect } = require('./helpers/fixtures');
const checkImages = require('./helpers/checkImages');
const gotoFresh = require('./helpers/gotoFresh');

test.describe('If I Speak front-page block', () => {
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the front-page block', async ({ page }) => {
    await gotoFresh(page, '/');

    const present =
      (await page.getByTestId('front-page-if-i-speak').count()) > 0;
    test.skip(!present, 'The If I Speak block is not in the front-page layout');

    const block = page.getByTestId('front-page-if-i-speak');

    await test.step('shows the wordmark and presenters', async () => {
      await expect(block.getByRole('img', { name: 'If I Speak' })).toBeVisible();
      await checkImages(page, { scope: block });
    });

    await test.step('links straight to the podcast platforms', async () => {
      // Direct platform links, never an intermediate like Podfollow.
      const links = block
        .getByTestId('front-page-if-i-speak-follow-links')
        .getByRole('link');

      await expect(links).toHaveCount(3);

      for (const link of await links.all()) {
        await expect(link).toHaveAttribute('href', /^https:\/\//);
        await expect(link).not.toHaveAttribute('href', /podfollow\.com/);
      }
    });

    await test.step('lists recent episodes', async () => {
      const episodes = block.getByTestId('front-page-if-i-speak-episodes');

      await expect(
        episodes.getByRole('heading', { name: 'Recent Episodes' })
      ).toBeVisible();
      expect(
        await episodes.getByRole('heading', { level: 5 }).count()
      ).toBeGreaterThan(0);
    });
  });
});
