const { expect } = require('@playwright/test');

/**
 * Verify critical page structure is present.
 * Checks for header, main content, and footer.
 *
 * @param {import('@playwright/test').Page} page
 */
const verifyCriticalPageStructure = async (page) => {
  await expect(page.getByTestId('site-header')).toBeVisible();
  await expect(page.getByTestId('main-content')).toBeAttached();
  await expect(page.getByTestId('site-footer')).toBeVisible();
};

module.exports = verifyCriticalPageStructure;
