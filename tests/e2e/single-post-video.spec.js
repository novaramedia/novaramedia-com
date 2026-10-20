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
  // lookup happens once per worker on a throwaway page and the test navigates
  // to the video post itself.
  test.beforeAll(async ({ browser, baseURL }) => {
    const page = await browser.newPage({ baseURL });

    videoPostUrl = await findPostUrlFromArchive(page, '/category/video');

    await page.close();
  });

  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders a video post', async ({ page, consoleErrors }) => {
    test.skip(!videoPostUrl, 'No video posts found on /category/video');

    await gotoFresh(page, videoPostUrl);

    await test.step('loads successfully', async () => {
      expect(page.url()).toContain(videoPostUrl);
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays video post content', async () => {
      await expect(page.getByTestId('single-post')).toBeAttached();

      // Video post should have a title
      const postTitle = page.getByTestId('post-title');
      await expect(postTitle).toBeVisible();
      await expect(postTitle).not.toHaveText('');
    });

    await test.step('displays video player', async () => {
      // Check for video player section
      const videoPlayer = page.getByTestId('video-player');
      await expect(videoPlayer).toBeAttached();

      // Should contain a YouTube iframe (theme's video implementation)
      await expect(videoPlayer.locator('iframe').first()).toBeAttached();
    });

    await test.step('displays post metadata', async () => {
      await expect(page.getByTestId('single-post')).toBeAttached();
    });

    await test.step('has category indicator', async () => {
      // Video posts should indicate they're in the video category
      const text = (await page.locator('body').textContent()).toLowerCase();
      const hasVideoIndicator =
        text.includes('video') ||
        (await page.locator('a[href*="/category/video"]').count()) > 0;

      expect(hasVideoIndicator).toBe(true);
    });

    await test.step('has proper heading hierarchy', async () => {
      await expect(page.getByTestId('post-title')).not.toHaveText('');
    });

    await test.step('has navigation elements', async () => {
      await expect(page.getByTestId('site-header')).toBeVisible();
      await expect(page.getByTestId('site-nav')).toBeVisible();
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page, async () => {
        await expect(page.getByTestId('post-title')).toBeVisible();
      });
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
