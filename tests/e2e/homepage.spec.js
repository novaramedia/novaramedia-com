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
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/');
  });

  test('should load successfully', async ({ page }) => {
    expect(new URL(page.url()).pathname).toBe('/');
    await expect(page).toHaveTitle(/Novara Media/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
    await expect(page.getByTestId('site-nav')).toBeVisible();
  });

  test('should have working navigation links', async ({ page }) => {
    await page
      .getByTestId('site-nav')
      .locator('.site-header__nav-toggle')
      .click();

    await expect(page.getByTestId('site-nav-panel')).toBeVisible();
    await expect(
      page.getByTestId('site-nav-panel').locator('a').first()
    ).toHaveAttribute('href', /.+/);
  });

  test('should display post content', async ({ page }) => {
    await expect(page.getByTestId('post-list').first()).toBeAttached();

    const firstPostTitle = page.getByTestId('post-title').first();
    await expect(firstPostTitle).toBeVisible();
    await expect(firstPostTitle).not.toHaveText('');
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should not have broken images in critical content', async ({
    page,
  }) => {
    await checkImages(page, {
      scope: page.getByTestId('main-content'),
      limit: 5,
    });
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page);
  });

  test('should have meta tags for SEO', async ({ page }) => {
    await expect(
      page.locator('head meta[name="description"]').first()
    ).toBeAttached();
    await expect(
      page.locator('head meta[property="og:title"]').first()
    ).toBeAttached();
  });
});
