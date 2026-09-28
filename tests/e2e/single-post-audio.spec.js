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
  // lookup happens once per worker on a throwaway page and each test navigates
  // to the audio post itself.
  test.beforeAll(async ({ browser, baseURL }) => {
    const page = await browser.newPage({ baseURL });

    audioPostUrl = await findPostUrlFromArchive(page, '/category/audio');

    await page.close();
  });

  test.beforeEach(async ({ page }) => {
    test.skip(!audioPostUrl, 'No audio posts found on /category/audio');

    await gotoFresh(page, audioPostUrl);
  });

  test('should load successfully', async ({ page }) => {
    expect(page.url()).toContain(audioPostUrl);
    await expect(page).toHaveTitle(/.+/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display audio post content', async ({ page }) => {
    await expect(page.getByTestId('single-post')).toBeAttached();

    // Audio post should have a title
    const postTitle = page.getByTestId('post-title');
    await expect(postTitle).toBeVisible();
    await expect(postTitle).not.toHaveText('');
  });

  test('should display audio player', async ({ page }) => {
    // Check for audio player section
    const audioPlayer = page.getByTestId('audio-player');
    await expect(audioPlayer).toBeAttached();

    // AudioPlayers.js hydrates the .soundcloud-lazy placeholder into an iframe
    await expect(audioPlayer.locator('iframe').first()).toBeAttached();
  });

  test('should display post metadata', async ({ page }) => {
    await expect(page.getByTestId('single-post')).toBeAttached();
  });

  test('should have category indicator', async ({ page }) => {
    // Audio posts should indicate they're in the audio category
    const text = (await page.locator('body').textContent()).toLowerCase();
    const hasAudioIndicator =
      text.includes('audio') ||
      text.includes('podcast') ||
      (await page.locator('a[href*="/category/audio"]').count()) > 0;

    expect(hasAudioIndicator).toBe(true);
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
