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

    await test.step('joins every pair of front-page sections by their frames', async () => {
      // Structure only: the layout is editable, so which sections render is
      // not asserted — only that each gap matches the two sections around it.
      const report = await page.getByTestId('main-content').evaluate((main) => {
        const isSection = (el) =>
          Boolean(el) && el.dataset.testid === 'front-page-section';
        const expectedJoin = (previous, next) => {
          if (previous === 'open' && next === 'open') return 'divided';
          if (previous === 'contained' && next === 'contained') return 'tight';
          return 'loose';
        };
        const sections = [
          ...main.querySelectorAll('[data-testid="front-page-section"]'),
        ];
        const joins = [
          ...main.querySelectorAll('[data-testid="front-page-join"]'),
        ];

        return {
          sections: sections.length,
          joins: joins.length,
          emptySections: sections.filter(
            (section) =>
              section.textContent.trim() === '' &&
              !section.querySelector('img, svg, iframe')
          ).length,
          badJoins: joins.filter((join) => {
            const previous = join.previousElementSibling;
            const next = join.nextElementSibling;

            if (!isSection(previous) || !isSection(next)) {
              return true;
            }

            const kind = expectedJoin(
              previous.dataset.frame,
              next.dataset.frame
            );
            const rules = join.querySelectorAll('hr').length;

            return (
              join.dataset.join !== kind ||
              rules !== (kind === 'divided' ? 1 : 0)
            );
          }).length,
        };
      });

      expect(report.sections).toBeGreaterThan(1);
      expect(report.joins).toBe(report.sections - 1);
      expect(report.emptySections).toBe(0);
      expect(report.badJoins).toBe(0);
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
