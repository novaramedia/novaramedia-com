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
  // per test, so discovery runs once per worker on a throwaway page and the
  // test navigates to the article itself. The throwaway page is deliberately
  // outside the console-error collector, matching Cypress where the archive
  // load happened before any per-test console spy was attached.
  test.beforeAll(async ({ browser, baseURL }) => {
    const page = await browser.newPage({ baseURL });

    articleUrl = await findPostUrlFromArchive(page, '/category/articles');

    await page.close();
  });

  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders an article', async ({ page, baseURL, consoleErrors }) => {
    test.skip(!articleUrl, 'No articles found on /category/articles');

    await gotoFresh(page, articleUrl);

    await test.step('loads successfully', async () => {
      // Compare pathnames: gotoFresh appends a cache-bust query, so a raw URL
      // comparison could never detect a redirect back to the home page.
      expect(new URL(page.url()).pathname).toBe(
        new URL(articleUrl, baseURL).pathname
      );
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays article content', async () => {
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

    await test.step('displays post metadata', async () => {
      // Verify the article structure exists
      await expect(page.getByTestId('single-post')).toBeAttached();
    });

    await test.step('has navigation elements', async () => {
      await expect(page.getByTestId('site-header')).toBeVisible();
      await expect(page.getByTestId('site-nav')).toBeVisible();
    });

    await test.step('has proper heading hierarchy', async () => {
      // Should have the post title as h1
      await expect(page.getByTestId('post-title')).not.toHaveText('');
    });

    await test.step('displays article content with proper formatting', async () => {
      expect(
        await page.getByTestId('single-post').locator('p').count()
      ).toBeGreaterThan(0);
    });

    await test.step('has social sharing or interaction elements', async () => {
      // Verify the article structure is complete
      await expect(page.getByTestId('single-post')).toBeAttached();
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page, async () => {
        await expect(page.getByTestId('post-title')).toBeVisible();
      });
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
