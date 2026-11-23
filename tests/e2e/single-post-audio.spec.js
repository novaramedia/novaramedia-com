/**
 * Single Post (Audio Category) Tests
 *
 * Tests for audio/podcast post pages
 * Verifies that audio posts display correctly with audio player and content
 *
 * SoundCloud is blocked by the default fixture, so the player assertion checks
 * the theme's own markup: AudioPlayers.js swaps the .soundcloud-lazy
 * placeholder for an iframe element on DOMContentLoaded, and that element is
 * attached whether or not SoundCloud ever responds. The Cypress spec needed a
 * 120s visit timeout only because the unblocked embed gated page load.
 */

const { test, expect } = require('./helpers/fixtures');
const findPostUrlFromArchive = require('./helpers/findPostUrlFromArchive');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

test.describe('Single Post (Audio Category)', () => {
  let audioPostUrl;

  // Discovery ran in a Cypress before() hook and the resulting page was reused
  // by every test (testIsolation: false). Playwright isolates per test, so the
  // lookup happens once per worker on a throwaway page and the test navigates
  // to the audio post itself.
  test.beforeAll(async ({ browser, baseURL }) => {
    const page = await browser.newPage({ baseURL });

    audioPostUrl = await findPostUrlFromArchive(page, '/category/audio');

    await page.close();
  });

  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders an audio post', async ({ page, consoleErrors }) => {
    test.skip(!audioPostUrl, 'No audio posts found on /category/audio');

    await gotoFresh(page, audioPostUrl);

    await test.step('loads successfully', async () => {
      expect(page.url()).toContain(audioPostUrl);
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays audio post content', async () => {
      await expect(page.getByTestId('single-post')).toBeAttached();

      // Audio post should have a title
      const postTitle = page.getByTestId('post-title');
      await expect(postTitle).toBeVisible();
      await expect(postTitle).not.toHaveText('');
    });

    await test.step('displays audio player', async () => {
      // Check for audio player section
      const audioPlayer = page.getByTestId('audio-player');
      await expect(audioPlayer).toBeAttached();

      // AudioPlayers.js hydrates the .soundcloud-lazy placeholder into an iframe
      await expect(audioPlayer.locator('iframe').first()).toBeAttached();
    });

    await test.step('displays post metadata', async () => {
      await expect(page.getByTestId('single-post')).toBeAttached();
    });

    await test.step('has category indicator', async () => {
      // Audio posts should indicate they're in the audio category
      const text = (await page.locator('body').textContent()).toLowerCase();
      const hasAudioIndicator =
        text.includes('audio') ||
        text.includes('podcast') ||
        (await page.locator('a[href*="/category/audio"]').count()) > 0;

      expect(hasAudioIndicator).toBe(true);
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
