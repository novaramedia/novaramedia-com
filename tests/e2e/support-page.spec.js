/**
 * Support Page Tests
 *
 * Tests for the support/donation page (page-support.php)
 * Verifies that the support page loads and displays donation forms
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

test.describe('Support Page', () => {
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/support');
  });

  test('should load successfully', async ({ page }) => {
    expect(page.url()).toContain('/support');
    await expect(page).toHaveTitle(/Support/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display support page content', async ({ page }) => {
    await expect(page.getByTestId('support-page')).toBeAttached();
  });

  test('should display support/donation form elements', async ({ page }) => {
    // Should have some form of donation interface
    expect(
      await page
        .getByTestId('support-page')
        .locator('form, button, a[href*="donate"]')
        .count()
    ).toBeGreaterThan(0);
  });

  test('should display main content', async ({ page }) => {
    const supportPage = page.getByTestId('support-page');

    // Should have headings
    expect(await supportPage.locator('h1, h2, h3, h4').count()).toBeGreaterThan(
      0
    );

    // Should have paragraphs or content blocks
    expect(await supportPage.locator('p, div').count()).toBeGreaterThan(0);
  });

  test('should have support form submit button', async ({ page }) => {
    // Check specifically for the support form submit button
    await expect(
      page
        .getByTestId('support-page')
        .locator('form button[type="submit"], form input[type="submit"]')
        .first()
    ).toBeAttached();
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.getByTestId('support-page')).toBeAttached();
    });
  });

  test('should have appropriate heading structure', async ({ page }) => {
    const headings = page.locator(
      '[data-testid="support-page"] h3, [data-testid="support-page"] h4'
    );

    expect(await headings.count()).toBeGreaterThan(0);
    await expect(headings.first()).toBeVisible();
    await expect(headings.first()).not.toHaveText('');
  });

  test('should display support information text', async ({ page }) => {
    await expect(
      page
        .getByTestId('support-page')
        .getByText(/support|donate|contribute|help/i)
        .first()
    ).toBeAttached();
  });
});
