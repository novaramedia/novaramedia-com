/**
 * Jobs Page Tests
 *
 * Tests for the Jobs page (page-jobs.php)
 * Verifies that the Jobs page loads and displays job listings
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

const HEADINGS =
  '[data-testid="jobs-page"] h1, [data-testid="jobs-page"] h4, [data-testid="jobs-page"] h5';

test.describe('Jobs Page', () => {
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/jobs');
  });

  test('should load successfully', async ({ page }) => {
    expect(page.url()).toContain('/jobs');
    await expect(page).toHaveTitle(/.+/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display jobs page content', async ({ page }) => {
    await expect(page.getByTestId('jobs-page')).toBeAttached();
  });

  test('should have main heading', async ({ page }) => {
    expect(await page.locator(HEADINGS).count()).toBeGreaterThan(0);

    // Verify page has substantial content
    const bodyText = await page.locator('body').textContent();
    expect(bodyText.length).toBeGreaterThan(200);
  });

  test('should display jobs content or listings', async ({ page }) => {
    const jobsPage = page.getByTestId('jobs-page');

    // Should have headings
    expect(
      await jobsPage.locator('h1, h2, h3, h4, h5').count()
    ).toBeGreaterThan(0);

    // Should have content - either job listings or informational text
    expect(await jobsPage.locator('p, li, div').count()).toBeGreaterThan(0);
  });

  test('should display job-related information', async ({ page }) => {
    // Jobs page should contain relevant keywords
    const text = (await page.locator('body').textContent()).toLowerCase();
    const hasJobContent =
      text.includes('job') ||
      text.includes('position') ||
      text.includes('hiring') ||
      text.includes('work') ||
      text.includes('no available positions');

    expect(hasJobContent).toBe(true);
  });

  test('should handle empty job listings gracefully', async ({ page }) => {
    // Even if no jobs available, page should display properly
    await expect(page.getByTestId('jobs-page')).toBeVisible();

    // Should have some message or content
    const bodyText = await page.locator('body').textContent();
    expect(bodyText.length).toBeGreaterThan(50);
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.locator(HEADINGS).first()).toBeVisible();
    });
  });

  test('should have navigation elements', async ({ page }) => {
    await expect(page.getByTestId('site-header')).toBeVisible();
    await expect(page.getByTestId('site-nav')).toBeVisible();
  });

  test('should have proper heading structure', async ({ page }) => {
    // Should have some heading structure
    const headings = page.locator(HEADINGS);

    expect(await headings.count()).toBeGreaterThan(0);
    await expect(headings.first()).not.toHaveText('');
  });
});
