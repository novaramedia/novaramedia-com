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
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the support page', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/support');

    await test.step('loads successfully', async () => {
      expect(page.url()).toContain('/support');
      await expect(page).toHaveTitle(/Support/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays support page content', async () => {
      await expect(page.getByTestId('support-page')).toBeAttached();
    });

    await test.step('displays support/donation form elements', async () => {
      // Should have some form of donation interface
      expect(
        await page
          .getByTestId('support-page')
          .locator('form, button, a[href*="donate"]')
          .count()
      ).toBeGreaterThan(0);
    });

    await test.step('displays main content', async () => {
      const supportPage = page.getByTestId('support-page');

      // Should have headings
      expect(
        await supportPage.locator('h1, h2, h3, h4').count()
      ).toBeGreaterThan(0);

      // Should have paragraphs or content blocks
      expect(await supportPage.locator('p, div').count()).toBeGreaterThan(0);
    });

    await test.step('has support form submit button', async () => {
      // Check specifically for the support form submit button
      await expect(
        page
          .getByTestId('support-page')
          .locator('form button[type="submit"], form input[type="submit"]')
          .first()
      ).toBeAttached();
    });

    await test.step('renders support forms with unique IDs', async () => {
      // Each form renders a mobile and a desktop set of controls, one hidden by CSS. Their
      // IDs must still be unique, or aria-labelledby and label[for] resolve to the first match.
      const ids = await page
        .locator('.support-form [id]')
        .evaluateAll((elements) => elements.map((el) => el.id));
      const duplicates = ids.filter((id, index) => ids.indexOf(id) !== index);

      expect(ids.length).toBeGreaterThan(0);
      expect(duplicates).toEqual([]);
    });

    await test.step('labels each donation radiogroup from within its own set', async () => {
      const unresolved = await page
        .locator('.support-form [role="radiogroup"][aria-labelledby]')
        .evaluateAll((groups) =>
          groups
            .filter((group) => {
              const label = document.getElementById(
                group.getAttribute('aria-labelledby')
              );
              return !label || label.parentElement !== group.parentElement;
            })
            .map((group) => group.getAttribute('aria-labelledby'))
        );

      expect(unresolved).toEqual([]);
    });

    await test.step('has appropriate heading structure', async () => {
      const headings = page.locator(
        '[data-testid="support-page"] h3, [data-testid="support-page"] h4'
      );

      expect(await headings.count()).toBeGreaterThan(0);
      await expect(headings.first()).toBeVisible();
      await expect(headings.first()).not.toHaveText('');
    });

    await test.step('displays support information text', async () => {
      await expect(
        page
          .getByTestId('support-page')
          .getByText(/support|donate|contribute|help/i)
          .first()
      ).toBeAttached();
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page, async () => {
        await expect(page.getByTestId('support-page')).toBeAttached();
      });
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
