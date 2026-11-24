/**
 * If I Speak Tests
 *
 * The If I Speak category archive, served in place at /if-i-speak/ (vanity
 * path, lib/functions-rewrites.php): the boxed hero and the grid of pure-text
 * episode cards (partials/post-layouts/archive-post-pure-text.php).
 *
 * The subscribe button only renders when the category's podcast URL term meta
 * is set, so that step skips when the button is absent rather than failing on
 * configuration.
 */

const { test, expect } = require('./helpers/fixtures');
const checkImages = require('./helpers/checkImages');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

const POST_URL_PATTERN = /\/\d{4}\/\d{2}\/\d{2}\//;

// NM_DATE_FORMAT_LONG ('j F Y'), e.g. "27 January 2026".
const HOUSE_DATE_PATTERN = /^\d{1,2} [A-Z][a-z]+ \d{4}$/;

test.describe('If I Speak archive', () => {
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the If I Speak archive', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/if-i-speak/');

    await test.step('loads successfully', async () => {
      expect(new URL(page.url()).pathname).toBe('/if-i-speak/');
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays the hero with the wordmark and presenters', async () => {
      const hero = page.getByTestId('if-i-speak-hero');

      await expect(hero).toBeVisible();
      // The wordmark is an inline SVG inside the h1; its <title> names it.
      await expect(
        hero.getByRole('heading', { level: 1, name: 'If I Speak' })
      ).toBeVisible();
      // The presenters photo is the hero's only <img> element; the wordmark is
      // an inline <svg>, so an element locator picks the photo alone.
      await expect(hero.locator('img')).toBeVisible();
      await checkImages(page, { scope: hero });
    });

    await test.step('shows the hero blurb', async () => {
      // Formatted description if set, else the core category description.
      const blurb = page
        .getByTestId('if-i-speak-hero')
        .getByTestId('if-i-speak-blurb');

      await expect(blurb).toBeVisible();
      await expect(blurb).not.toHaveText(/^\s*$/);
    });

    await test.step('links the subscribe button out to the podcast', async (step) => {
      // Located by testid, not label: the button text is editable per category
      // (_nm_podcast_text), so a name match would skip on custom copy.
      const button = page
        .getByTestId('if-i-speak-hero')
        .getByTestId('if-i-speak-subscribe');

      step.skip(
        (await button.count()) === 0,
        'No podcast URL set on the If I Speak category'
      );

      await expect(button).toHaveAttribute('href', /^https?:\/\//);
      await expect(button).toHaveAttribute('target', '_blank');
    });

    await test.step('lists episodes as pure-text cards', async () => {
      const episodes = page.getByTestId('if-i-speak-episodes');

      await expect(episodes).toBeVisible();

      const cards = episodes.getByTestId('archive-post-pure-text');
      expect(await cards.count()).toBeGreaterThan(0);

      const first = cards.first();
      await expect(first.locator('a').first()).toHaveAttribute(
        'href',
        POST_URL_PATTERN
      );
      await expect(first.getByRole('heading', { level: 5 })).not.toHaveText('');
      // Cards carry no image, avatar or byline.
      await expect(first.locator('img')).toHaveCount(0);
    });

    await test.step('dates episodes in the house format', async () => {
      const date = page
        .getByTestId('if-i-speak-episodes')
        .getByTestId('archive-post-pure-text')
        .first()
        .locator('time');

      await expect(date).toHaveText(HOUSE_DATE_PATTERN);
      await expect(date).toHaveAttribute('datetime', /^\d{4}-\d{2}-\d{2}T/);
    });

    await test.step('has no broken images in the main content', async () => {
      await checkImages(page, { scope: page.getByTestId('main-content') });
    });

    await test.step('renders the hero and episodes at each viewport', async () => {
      await testResponsive(page, async () => {
        await expect(page.getByTestId('if-i-speak-hero')).toBeVisible();
        await expect(page.getByTestId('if-i-speak-episodes')).toBeVisible();
      });
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
