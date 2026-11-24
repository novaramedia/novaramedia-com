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
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the about page', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/about');

    await test.step('loads successfully', async () => {
      expect(page.url()).toContain('/about');
      await expect(page).toHaveTitle(/about/i);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays about page content', async () => {
      await expect(page.getByTestId('about-page')).toBeAttached();
    });

    await test.step('has main heading', async () => {
      expect(await page.locator(HEADINGS).count()).toBeGreaterThan(0);

      // Verify page has substantial content
      const bodyText = await page.locator('body').textContent();
      expect(bodyText.length).toBeGreaterThan(200);
    });

    await test.step('displays about content sections', async () => {
      const aboutPage = page.getByTestId('about-page');

      // Should have headings for different sections
      expect(await aboutPage.locator('h1, h2, h3, h4').count()).toBeGreaterThan(
        0
      );

      // Should have substantial content
      expect(await aboutPage.locator('p, div').count()).toBeGreaterThan(0);
    });

    await test.step('displays organizational information', async () => {
      // About page typically contains mission, team, or history information
      const text = (await page.locator('body').textContent()).toLowerCase();
      const hasRelevantContent =
        text.includes('novara') ||
        text.includes('media') ||
        text.includes('team') ||
        text.includes('about');

      expect(hasRelevantContent).toBe(true);
    });

    await test.step('has navigation links', async () => {
      await expect(page.getByTestId('site-header')).toBeVisible();
      await expect(page.getByTestId('site-nav')).toBeVisible();
      expect(
        await page
          .locator('[data-testid="site-nav"] a, [data-testid="site-header"] a')
          .count()
      ).toBeGreaterThan(0);
    });

    await test.step('has appropriate meta tags', async () => {
      await expect(
        page.locator('head meta[name="description"]').first()
      ).toBeAttached();
      await expect(
        page.locator('head meta[property="og:title"]').first()
      ).toBeAttached();
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
