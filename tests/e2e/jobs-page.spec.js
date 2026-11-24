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
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the jobs page', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/jobs');

    await test.step('loads successfully', async () => {
      expect(page.url()).toContain('/jobs');
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays jobs page content', async () => {
      await expect(page.getByTestId('jobs-page')).toBeAttached();
    });

    await test.step('has main heading', async () => {
      expect(await page.locator(HEADINGS).count()).toBeGreaterThan(0);

      // Verify page has substantial content
      const bodyText = await page.locator('body').textContent();
      expect(bodyText.length).toBeGreaterThan(200);
    });

    await test.step('displays jobs content or listings', async () => {
      const jobsPage = page.getByTestId('jobs-page');

      // Should have headings
      expect(
        await jobsPage.locator('h1, h2, h3, h4, h5').count()
      ).toBeGreaterThan(0);

      // Should have content - either job listings or informational text
      expect(await jobsPage.locator('p, li, div').count()).toBeGreaterThan(0);
    });

    await test.step('displays job-related information', async () => {
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

    await test.step('handles empty job listings gracefully', async () => {
      // Even if no jobs available, page should display properly
      await expect(page.getByTestId('jobs-page')).toBeVisible();

      // Should have some message or content
      const bodyText = await page.locator('body').textContent();
      expect(bodyText.length).toBeGreaterThan(50);
    });

    await test.step('has navigation elements', async () => {
      await expect(page.getByTestId('site-header')).toBeVisible();
      await expect(page.getByTestId('site-nav')).toBeVisible();
    });

    await test.step('has proper heading structure', async () => {
      // Should have some heading structure
      const headings = page.locator(HEADINGS);

      expect(await headings.count()).toBeGreaterThan(0);
      await expect(headings.first()).not.toHaveText('');
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page, async () => {
        await expect(page.locator(HEADINGS).first()).toBeVisible();
      });
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
