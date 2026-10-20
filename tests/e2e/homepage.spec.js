/**
 * Homepage Tests
 *
 * Tests for the main landing page (front-page.php)
 * Verifies that the homepage loads successfully and displays key content
 */

const { test, expect } = require('./helpers/fixtures');
const checkImages = require('./helpers/checkImages');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

test.describe('Homepage', () => {
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the homepage', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/');

    await test.step('loads successfully', async () => {
      expect(new URL(page.url()).pathname).toBe('/');
      await expect(page).toHaveTitle(/Novara Media/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
      await expect(page.getByTestId('site-nav')).toBeVisible();
    });

    await test.step('displays post content', async () => {
      await expect(page.getByTestId('post-list').first()).toBeAttached();

      const firstPostTitle = page.getByTestId('post-title').first();
      await expect(firstPostTitle).toBeVisible();
      await expect(firstPostTitle).not.toHaveText('');
    });

    await test.step('has no broken images in critical content', async () => {
      await checkImages(page, {
        scope: page.getByTestId('main-content'),
        limit: 5,
      });
    });

    await test.step('has meta tags for SEO', async () => {
      await expect(
        page.locator('head meta[name="description"]').first()
      ).toBeAttached();
      await expect(
        page.locator('head meta[property="og:title"]').first()
      ).toBeAttached();
    });

    await test.step('has working navigation links', async () => {
      await page
        .getByTestId('site-nav')
        .locator('.site-header__nav-toggle')
        .click();

      await expect(page.getByTestId('site-nav-panel')).toBeVisible();
      await expect(
        page.getByTestId('site-nav-panel').locator('a').first()
      ).toHaveAttribute('href', /.+/);
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page);
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
