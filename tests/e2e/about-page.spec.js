/**
 * About Page Tests
 *
 * Tests for the About page (page-about.php)
 * Verifies that the About page loads and displays organizational information
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

const HEADINGS = '[data-testid="about-page"] h1, [data-testid="about-page"] h4';

test.describe('About Page', () => {
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/about');
  });

  test('should load successfully', async ({ page }) => {
    expect(page.url()).toContain('/about');
    await expect(page).toHaveTitle(/about/i);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display about page content', async ({ page }) => {
    await expect(page.getByTestId('about-page')).toBeAttached();
  });

  test('should have main heading', async ({ page }) => {
    expect(await page.locator(HEADINGS).count()).toBeGreaterThan(0);

    // Verify page has substantial content
    const bodyText = await page.locator('body').textContent();
    expect(bodyText.length).toBeGreaterThan(200);
  });

  test('should display about content sections', async ({ page }) => {
    const aboutPage = page.getByTestId('about-page');

    // Should have headings for different sections
    expect(await aboutPage.locator('h1, h2, h3, h4').count()).toBeGreaterThan(
      0
    );

    // Should have substantial content
    expect(await aboutPage.locator('p, div').count()).toBeGreaterThan(0);
  });

  test('should display organizational information', async ({ page }) => {
    // About page typically contains mission, team, or history information
    const text = (await page.locator('body').textContent()).toLowerCase();
    const hasRelevantContent =
      text.includes('novara') ||
      text.includes('media') ||
      text.includes('team') ||
      text.includes('about');

    expect(hasRelevantContent).toBe(true);
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.locator(HEADINGS).first()).toBeVisible();
    });
  });

  test('should have navigation links', async ({ page }) => {
    await expect(page.getByTestId('site-header')).toBeVisible();
    await expect(page.getByTestId('site-nav')).toBeVisible();
    expect(
      await page
        .locator('[data-testid="site-nav"] a, [data-testid="site-header"] a')
        .count()
    ).toBeGreaterThan(0);
  });

  test('should have appropriate meta tags', async ({ page }) => {
    await expect(
      page.locator('head meta[name="description"]').first()
    ).toBeAttached();
    await expect(
      page.locator('head meta[property="og:title"]').first()
    ).toBeAttached();
  });
});
