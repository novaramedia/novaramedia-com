/**
 * Embed Consent Gate Tests
 *
 * The theme wraps third-party embeds (SoundCloud, Vimeo, X and other oEmbeds;
 * YouTube is exempt) in a consent gate: the real embed markup sits inside an
 * inert <template> and a placeholder with an accept button is shown until the
 * visitor accepts cookies. Consent is the `cookie-approval` cookie shared with
 * the cookie bar, so accepting on either control hydrates every gate on the
 * page. See nm_consent_gate_wrap() in lib/functions-filters.php and
 * src/js/modules/EmbedConsent.js.
 *
 * Audio posts are the fixed target: their SoundCloud player is always gated
 * (render_soundcloud_embed_iframe), whereas gated embeds in post content
 * depend on editorial choices. Third-party hosts stay blocked by the default
 * fixture. The assertions are about the theme's own markup and about which
 * requests the browser issues, and both are observable when the request is
 * aborted: the iframe element is attached and the request event fires either
 * way.
 *
 * Not covered: the absence of a placeholder flash for returning visitors
 * (consent is client-side, so the placeholder is in the server HTML until the
 * script runs — a timing property, not a stable assertion), and the
 * block-editor and classic-editor content variants, which need specific posts
 * on staging.
 */

const { test: base, expect } = require('./helpers/fixtures');
const findPostUrlFromArchive = require('./helpers/findPostUrlFromArchive');
const gotoFresh = require('./helpers/gotoFresh');
const grantCookieConsent = require('./helpers/grantCookieConsent');
const testResponsive = require('./helpers/testResponsive');

/**
 * Only SoundCloud hosts are asserted on. Posts can carry baked Twitter widget
 * markup that bypasses the gate (a known gap tracked in PR #523), so a blanket
 * "no third-party requests" assertion would fail on content rather than on the
 * gate under test.
 */
const SOUNDCLOUD_HOSTS = ['soundcloud.com', 'sndcdn.com'];

const isSoundCloudRequest = (request) => {
  try {
    const { hostname } = new URL(request.url());

    return SOUNDCLOUD_HOSTS.some(
      (host) => hostname === host || hostname.endsWith(`.${host}`)
    );
  } catch {
    return false;
  }
};

// Auto fixture so the listener is attached before beforeEach navigates.
// Aborted requests still emit `request`, so blocking does not hide them.
const test = base.extend({
  soundcloudRequests: [
    async ({ page }, use) => {
      const requests = [];

      page.on('request', (request) => {
        if (isSoundCloudRequest(request)) requests.push(request.url());
      });

      await use(requests);
    },
    { auto: true },
  ],
});

// A page can hold several gates (one per embed), so lookups are scoped to the
// audio player rather than relying on getByTestId's strictness.
const audioGate = (page) =>
  page.getByTestId('audio-player').getByTestId('embed-consent-gate');

const consentCookie = async (context) =>
  (await context.cookies()).find((cookie) => cookie.name === 'cookie-approval');

test.describe('Embed Consent Gate', () => {
  let audioPostUrl;

  test.beforeAll(async ({ browser, baseURL }) => {
    const page = await browser.newPage({ baseURL });

    audioPostUrl = await findPostUrlFromArchive(page, '/category/audio');

    await page.close();
  });

  test.describe('new visitor', () => {
    test.beforeEach(async ({ page }) => {
      test.skip(!audioPostUrl, 'No audio posts found on /category/audio');

      await gotoFresh(page, audioPostUrl);
    });

    test('should show a placeholder instead of the SoundCloud player', async ({
      page,
    }) => {
      const gate = audioGate(page);
      const placeholder = gate.getByTestId('embed-consent-placeholder');

      await expect(gate).toBeVisible();
      await expect(placeholder).toBeVisible();
      await expect(placeholder).toContainText('SoundCloud');
      await expect(gate.getByTestId('embed-consent-accept')).toBeVisible();

      // The embed markup is parked in an inert template, not in the live DOM,
      // so AudioPlayers.js has no placeholder to turn into an iframe.
      await expect(gate.locator('template')).toHaveCount(1);
      await expect(gate.locator('iframe')).toHaveCount(0);
    });

    test('should not request SoundCloud before consent', async ({
      page,
      soundcloudRequests,
    }) => {
      // The cookie bar is shown by the same DOMContentLoaded handler that runs
      // EmbedConsent and AudioPlayers, so once it is visible the theme script
      // has run and any leaked player would already have set its iframe src.
      await expect(page.getByTestId('cookie-bar')).toBeVisible();
      await expect(
        audioGate(page).getByTestId('embed-consent-accept')
      ).toBeVisible();

      expect(soundcloudRequests).toEqual([]);
    });

    test('should show the cookie bar', async ({ page }) => {
      await expect(page.getByTestId('cookie-bar')).toBeVisible();
      await expect(page.getByTestId('cookie-bar-accept')).toBeVisible();
    });

    test('should load the player, set the cookie and hide the cookie bar when accepting on the gate', async ({
      page,
      context,
      soundcloudRequests,
    }) => {
      const gate = audioGate(page);

      await expect(page.getByTestId('cookie-bar')).toBeVisible();

      await gate.getByTestId('embed-consent-accept').click();

      // Hydration swaps the template and placeholder for the embed markup,
      // which AudioPlayers.js then turns into the player iframe.
      await expect(gate.locator('iframe')).toHaveCount(1);
      await expect(gate.getByTestId('embed-consent-placeholder')).toHaveCount(
        0
      );
      await expect(gate.locator('template')).toHaveCount(0);

      // Every gate on the page hydrates, not only the one clicked.
      await expect(page.getByTestId('embed-consent-placeholder')).toHaveCount(
        0
      );

      // Consent lands in the shared cookie and the cookie bar stands down.
      await expect(page.getByTestId('cookie-bar')).toBeHidden();
      expect((await consentCookie(context))?.value).toBe('true');

      // The player request only happens after consent.
      await expect.poll(() => soundcloudRequests.length).toBeGreaterThan(0);
    });

    test('should load the player when accepting on the cookie bar', async ({
      page,
      context,
    }) => {
      const gate = audioGate(page);

      await expect(gate.getByTestId('embed-consent-placeholder')).toBeVisible();

      await page.getByTestId('cookie-bar-accept').click();

      await expect(gate.locator('iframe')).toHaveCount(1);
      await expect(page.getByTestId('embed-consent-placeholder')).toHaveCount(
        0
      );
      await expect(page.getByTestId('cookie-bar')).toBeHidden();
      expect((await consentCookie(context))?.value).toBe('true');
    });

    test('should keep the gate usable across viewports', async ({ page }) => {
      await testResponsive(page, async () => {
        await expect(
          audioGate(page).getByTestId('embed-consent-accept')
        ).toBeVisible();
      });
    });

    test('should load without console errors', async ({ consoleErrors }) => {
      expect(consoleErrors).toEqual([]);
    });
  });

  test.describe('returning visitor', () => {
    test.beforeEach(async ({ page, context, baseURL }) => {
      test.skip(!audioPostUrl, 'No audio posts found on /category/audio');

      await grantCookieConsent(context, baseURL);
      await gotoFresh(page, audioPostUrl);
    });

    test('should load the SoundCloud player without a placeholder', async ({
      page,
      soundcloudRequests,
    }) => {
      const gate = audioGate(page);

      await expect(gate.locator('iframe')).toHaveCount(1);
      await expect(gate.locator('template')).toHaveCount(0);
      await expect(page.getByTestId('embed-consent-placeholder')).toHaveCount(
        0
      );

      await expect.poll(() => soundcloudRequests.length).toBeGreaterThan(0);
    });

    test('should not show the cookie bar', async ({ page }) => {
      // The hydrated player proves the theme script has run, so a hidden bar
      // here is a decision rather than a race.
      await expect(audioGate(page).locator('iframe')).toHaveCount(1);
      await expect(page.getByTestId('cookie-bar')).toBeHidden();
    });

    test('should load without console errors', async ({ consoleErrors }) => {
      expect(consoleErrors).toEqual([]);
    });
  });

  test.describe('YouTube exemption', () => {
    let videoPostUrl;

    test.beforeAll(async ({ browser, baseURL }) => {
      const page = await browser.newPage({ baseURL });

      videoPostUrl = await findPostUrlFromArchive(page, '/category/video');

      await page.close();
    });

    test('should embed YouTube on a video post without a gate', async ({
      page,
    }) => {
      test.skip(!videoPostUrl, 'No video posts found on /category/video');

      await gotoFresh(page, videoPostUrl);

      const player = page.getByTestId('video-player');
      const iframe = player.locator('iframe').first();

      await expect(iframe).toBeAttached();
      await expect(iframe).toHaveAttribute(
        'src',
        /youtube-nocookie\.com\/embed\//
      );
      await expect(player.getByTestId('embed-consent-gate')).toHaveCount(0);
    });
  });

  test.describe('RSS feed', () => {
    test('should serve the audio category feed without gate markup', async ({
      request,
    }) => {
      // Feed readers get the raw embed HTML (nm_consent_gate_wrap returns it
      // untouched in feeds). The cache-bust query bypasses the page cache as
      // gotoFresh does.
      const response = await request.get(
        `/category/audio/feed/?playwright_cache_bust=${Date.now()}`
      );

      expect(response.ok()).toBeTruthy();
      expect(response.headers()['content-type']).toMatch(/xml/);
      expect(await response.text()).not.toContain('embed-consent-gate');
    });
  });
});
