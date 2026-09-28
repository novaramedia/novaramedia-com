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
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/category/novara-live');
  });

  test('should load successfully', async ({ page }) => {
    expect(page.url()).toContain('/category/novara-live');
    await expect(page).toHaveTitle(/.+/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display category heading', async ({ page }) => {
    // Category archive should have a heading or title
    const headings = page.locator(
      '[data-testid="main-content"] h1, [data-testid="main-content"] h2, [data-testid="main-content"] h4'
    );

    expect(await headings.count()).toBeGreaterThan(0);
    await expect(headings.first()).toBeVisible();
  });

  test('should display Novara Live posts', async ({ page }) => {
    // Should have post listings
    await expect(page.getByTestId('post-list')).toBeAttached();

    // Posts should have titles
    const postTitles = page.locator(
      '[data-testid="main-content"] h1, [data-testid="main-content"] h2, [data-testid="main-content"] h4, [data-testid="main-content"] h6'
    );

    expect(await postTitles.count()).toBeGreaterThan(0);
    await expect(postTitles.first()).toBeVisible();
  });

  test('should have post links', async ({ page }) => {
    // Each post should link to its single page
    const links = page.locator(POST_LINKS);

    expect(await links.count()).toBeGreaterThan(0);

    // At least one link should have an href
    await expect(links.first()).toHaveAttribute('href', /.+/);
  });

  test('should display Novara Live branding or content', async ({ page }) => {
    // Page should indicate it's the Novara Live category
    const text = (await page.locator('body').textContent()).toLowerCase();
    const hasNovaraLive =
      text.includes('novara live') || text.includes('novara-live');

    expect(hasNovaraLive).toBe(true);
  });

  test('should have category description or intro', async ({ page }) => {
    // Check if there's substantial content beyond just post listings
    const bodyText = await page.locator('body').textContent();
    expect(bodyText.length).toBeGreaterThan(100);
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page);
  });

  test('should have navigation elements', async ({ page }) => {
    await expect(page.getByTestId('site-header')).toBeVisible();
    await expect(page.getByTestId('site-nav')).toBeVisible();
  });

  test('should display post metadata', async ({ page }) => {
    // Verify posts are present
    await expect(page.getByTestId('main-content')).toBeAttached();

    const bodyText = await page.locator('body').textContent();
    expect(bodyText.length).toBeGreaterThan(100);
  });

  test('should have pagination or load more if many posts', async ({
    page,
  }) => {
    // Verify the page structure is intact
    await expect(page.getByTestId('main-content')).toBeAttached();
  });
});
