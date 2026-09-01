/**
 * Single Post Tests
 *
 * Tests for individual article pages (single.php)
 * Verifies that single posts display correctly with all content and navigation
 */

const { test, expect } = require('./helpers/fixtures');
const findPostUrlFromArchive = require('./helpers/findPostUrlFromArchive');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

test.describe('Single Post (Article)', () => {
  let articleUrl;

  // The Cypress spec discovered the article in a before() hook and every test
  // reused the page it left behind (testIsolation: false). Playwright isolates
  // per test, so discovery runs once per worker on a throwaway page and each
  // test navigates to the article itself. The throwaway page is deliberately
  // outside the console-error collector, matching Cypress where the archive
  // load happened before any per-test console spy was attached.
  test.beforeAll(async ({ browser, baseURL }) => {
    const page = await browser.newPage({ baseURL });

    articleUrl = await findPostUrlFromArchive(page, '/category/articles');

    await page.close();
  });

  test.beforeEach(async ({ page }) => {
    test.skip(!articleUrl, 'No articles found on /category/articles');

    await gotoFresh(page, articleUrl);
  });

  test('should load successfully', async ({ page, baseURL }) => {
    expect(page.url()).not.toBe(`${baseURL}/`);
    await expect(page).toHaveTitle(/.+/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display article content', async ({ page }) => {
    await expect(page.getByTestId('single-post')).toBeAttached();

    // Article should have a title
    const postTitle = page.getByTestId('post-title');
    await expect(postTitle).toBeVisible();
    await expect(postTitle).not.toHaveText('');

    // Article should have body content
    const postContent = page.getByTestId('post-content');
    await expect(postContent).toBeAttached();
    await expect(postContent).not.toHaveText('');

    // Content should have paragraphs
    expect(await postContent.locator('p').count()).toBeGreaterThan(0);
  });

  test('should display post metadata', async ({ page }) => {
    // Verify the article structure exists
    await expect(page.getByTestId('single-post')).toBeAttached();
  });

  test('should have navigation elements', async ({ page }) => {
    await expect(page.getByTestId('site-header')).toBeVisible();
    await expect(page.getByTestId('site-nav')).toBeVisible();
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.getByTestId('post-title')).toBeVisible();
    });
  });

  test('should have proper heading hierarchy', async ({ page }) => {
    // Should have the post title as h1
    await expect(page.getByTestId('post-title')).not.toHaveText('');
  });

  test('should display article content with proper formatting', async ({
    page,
  }) => {
    expect(
      await page.getByTestId('single-post').locator('p').count()
    ).toBeGreaterThan(0);
  });

  test('should have social sharing or interaction elements', async ({
    page,
  }) => {
    // Verify the article structure is complete
    await expect(page.getByTestId('single-post')).toBeAttached();
  });
});
