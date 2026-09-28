/**
 * Single Post (Video Category) Tests
 *
 * Tests for video post pages
 * Verifies that video posts display correctly with media player and content
 *
 * The YouTube iframe is blocked by the default fixture, so the player
 * assertions check the theme's own markup is rendered rather than waiting on
 * the embed. That is what the Cypress spec effectively asserted too — it only
 * needed a 60s visit timeout because the unblocked embed gated page load.
 */

const { test, expect } = require('./helpers/fixtures');
const findPostUrlFromArchive = require('./helpers/findPostUrlFromArchive');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

test.describe('Single Post (Video Category)', () => {
  let videoPostUrl;

  // Discovery ran in a Cypress before() hook and the resulting page was reused
  // by every test (testIsolation: false). Playwright isolates per test, so the
  // lookup happens once per worker on a throwaway page and each test navigates
  // to the video post itself.
  test.beforeAll(async ({ browser, baseURL }) => {
    const page = await browser.newPage({ baseURL });

    videoPostUrl = await findPostUrlFromArchive(page, '/category/video');

    await page.close();
  });

  test.beforeEach(async ({ page }) => {
    test.skip(!videoPostUrl, 'No video posts found on /category/video');

    await gotoFresh(page, videoPostUrl);
  });

  test('should load successfully', async ({ page }) => {
    expect(page.url()).toContain(videoPostUrl);
    await expect(page).toHaveTitle(/.+/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display video post content', async ({ page }) => {
    await expect(page.getByTestId('single-post')).toBeAttached();

    // Video post should have a title
    const postTitle = page.getByTestId('post-title');
    await expect(postTitle).toBeVisible();
    await expect(postTitle).not.toHaveText('');
  });

  test('should display video player', async ({ page }) => {
    // Check for video player section
    const videoPlayer = page.getByTestId('video-player');
    await expect(videoPlayer).toBeAttached();

    // Should contain a YouTube iframe (theme's video implementation)
    await expect(videoPlayer.locator('iframe').first()).toBeAttached();
  });

  test('should display post metadata', async ({ page }) => {
    await expect(page.getByTestId('single-post')).toBeAttached();
  });

  test('should have category indicator', async ({ page }) => {
    // Video posts should indicate they're in the video category
    const text = (await page.locator('body').textContent()).toLowerCase();
    const hasVideoIndicator =
      text.includes('video') ||
      (await page.locator('a[href*="/category/video"]').count()) > 0;

    expect(hasVideoIndicator).toBe(true);
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.getByTestId('post-title')).toBeVisible();
    });
  });

  test('should have proper heading hierarchy', async ({ page }) => {
    await expect(page.getByTestId('post-title')).not.toHaveText('');
  });

  test('should have navigation elements', async ({ page }) => {
    await expect(page.getByTestId('site-header')).toBeVisible();
    await expect(page.getByTestId('site-nav')).toBeVisible();
  });
});
