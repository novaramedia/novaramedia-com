const { expect } = require('@playwright/test');

const VIEWPORTS = [
  { name: 'mobile', width: 375, height: 667 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'desktop', width: 1280, height: 720 },
];

/**
 * Test responsive behaviour at different viewports.
 *
 * Asserts critical page structure (header, main-content, footer) at each
 * breakpoint. Pass an optional callback for page-specific assertions.
 *
 * @param {import('@playwright/test').Page} page
 * @param {(viewport: { name: string, width: number, height: number }) => Promise<void>} [callback]
 */
const testResponsive = async (page, callback) => {
  for (const viewport of VIEWPORTS) {
    await page.setViewportSize({
      width: viewport.width,
      height: viewport.height,
    });

    await expect(page.getByTestId('site-header')).toBeVisible();
    await expect(page.getByTestId('main-content')).toBeVisible();
    await expect(page.getByTestId('site-footer')).toBeVisible();

    if (callback) await callback(viewport);
  }
};

module.exports = testResponsive;
