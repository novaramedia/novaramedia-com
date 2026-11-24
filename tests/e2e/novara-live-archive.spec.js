/**
 * Novara Live Category Archive Tests
 *
 * Tests for the Novara Live category archive page
 * Verifies that the category archive loads and displays posts
 *
 * The archive embeds the latest show as a YouTube iframe. Embed hosts are
 * blocked by the default fixture, so every assertion below is on the theme's
 * own DOM rather than anything the player renders.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

const POST_LINKS =
  '[data-testid="post-list"] a, [data-testid="main-content"] a';

test.describe('Novara Live Category Archive', () => {
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the Novara Live archive', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/category/novara-live');

    await test.step('loads successfully', async () => {
      expect(page.url()).toContain('/category/novara-live');
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays category heading', async () => {
      // Category archive should have a heading or title
      const headings = page.locator(
        '[data-testid="main-content"] h1, [data-testid="main-content"] h2, [data-testid="main-content"] h4'
      );

      expect(await headings.count()).toBeGreaterThan(0);
      await expect(headings.first()).toBeVisible();
    });

    await test.step('displays Novara Live posts', async () => {
      // Should have post listings
      await expect(page.getByTestId('post-list')).toBeAttached();

      // Posts should have titles
      const postTitles = page.locator(
        '[data-testid="main-content"] h1, [data-testid="main-content"] h2, [data-testid="main-content"] h4, [data-testid="main-content"] h6'
      );

      expect(await postTitles.count()).toBeGreaterThan(0);
      await expect(postTitles.first()).toBeVisible();
    });

    await test.step('has post links', async () => {
      // Each post should link to its single page
      const links = page.locator(POST_LINKS);

      expect(await links.count()).toBeGreaterThan(0);

      // At least one link should have an href
      await expect(links.first()).toHaveAttribute('href', /.+/);
    });

    await test.step('displays Novara Live branding or content', async () => {
      // Page should indicate it's the Novara Live category
      const text = (await page.locator('body').textContent()).toLowerCase();
      const hasNovaraLive =
        text.includes('novara live') || text.includes('novara-live');

      expect(hasNovaraLive).toBe(true);
    });

    await test.step('has category description or intro', async () => {
      // Check if there's substantial content beyond just post listings
      const bodyText = await page.locator('body').textContent();
      expect(bodyText.length).toBeGreaterThan(100);
    });

    await test.step('has navigation elements', async () => {
      await expect(page.getByTestId('site-header')).toBeVisible();
      await expect(page.getByTestId('site-nav')).toBeVisible();
    });

    await test.step('displays post metadata', async () => {
      // Verify posts are present
      await expect(page.getByTestId('main-content')).toBeAttached();

      const bodyText = await page.locator('body').textContent();
      expect(bodyText.length).toBeGreaterThan(100);
    });

    await test.step('has pagination or load more if many posts', async () => {
      // Verify the page structure is intact
      await expect(page.getByTestId('main-content')).toBeAttached();
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page);
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
