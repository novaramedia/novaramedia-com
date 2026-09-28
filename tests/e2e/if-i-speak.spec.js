/**
 * If I Speak Tests
 *
 * The If I Speak category archive, served in place at /if-i-speak/ (vanity
 * path, lib/functions-rewrites.php): the boxed hero and the grid of pure-text
 * episode cards (partials/post-layouts/archive-post-pure-text.php).
 *
 * The subscribe button only renders when the category's podcast URL term meta
 * is set, so that test skips when the button is absent rather than failing on
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
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/if-i-speak/');
  });

  test('should load successfully', async ({ page }) => {
    expect(new URL(page.url()).pathname).toBe('/if-i-speak/');
    await expect(page).toHaveTitle(/.+/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display the hero with the wordmark and presenters', async ({
    page,
  }) => {
    const hero = page.getByTestId('if-i-speak-hero');

    await expect(hero).toBeVisible();
    // The wordmark is an inline SVG inside the h1; its <title> names it.
    await expect(
      hero.getByRole('heading', { level: 1, name: 'If I Speak' })
    ).toBeVisible();
    // The presenters photo is the hero's only <img>; the wordmark SVG also
    // exposes the img role, so the element type is what disambiguates.
    await expect(hero.locator('img')).toBeVisible();
    await checkImages(page, { scope: hero });
  });

  test('should show the hero blurb', async ({ page }) => {
    // Formatted description if set, else the core category description.
    const blurb = page
      .getByTestId('if-i-speak-hero')
      .getByTestId('if-i-speak-blurb');

    await expect(blurb).toBeVisible();
    await expect(blurb).not.toHaveText(/^\s*$/);
  });

  test('should link the subscribe button out to the podcast', async ({
    page,
  }) => {
    const button = page
      .getByTestId('if-i-speak-hero')
      .getByRole('link', { name: /subscribe/i });

    test.skip(
      (await button.count()) === 0,
      'No podcast URL set on the If I Speak category'
    );

    await expect(button).toHaveAttribute('href', /^https?:\/\//);
    await expect(button).toHaveAttribute('target', '_blank');
  });

  test('should list episodes as pure-text cards', async ({ page }) => {
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

  test('should date episodes in the house format', async ({ page }) => {
    const date = page
      .getByTestId('if-i-speak-episodes')
      .getByTestId('archive-post-pure-text')
      .first()
      .locator('time');

    await expect(date).toHaveText(HOUSE_DATE_PATTERN);
    await expect(date).toHaveAttribute('datetime', /^\d{4}-\d{2}-\d{2}T/);
  });

  test('should have no broken images in the main content', async ({ page }) => {
    await checkImages(page, { scope: page.getByTestId('main-content') });
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should render the hero and episodes at each viewport', async ({
    page,
  }) => {
    await testResponsive(page, async () => {
      await expect(page.getByTestId('if-i-speak-hero')).toBeVisible();
      await expect(page.getByTestId('if-i-speak-episodes')).toBeVisible();
    });
  });
});
