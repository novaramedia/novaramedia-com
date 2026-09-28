/**
 * The Cortado Tests
 *
 * The Cortado newsletter's website surfaces: the category archive served in
 * place at /the-cortado/ (vanity path, lib/functions-rewrites.php), the 301
 * from the newsletter record's own permalink, and the front-page product
 * block. The inline Gutenberg signup variant depends on which posts carry the
 * block, so it is not covered here.
 *
 * The front-page block only renders when an editor has placed it in Front
 * Page > Layout, so that describe skips when the block is absent rather than
 * failing on configuration.
 */

const { test, expect } = require('./helpers/fixtures');
const checkImages = require('./helpers/checkImages');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

const POST_URL_PATTERN = /\/\d{4}\/\d{2}\/\d{2}\//;

test.describe('The Cortado archive', () => {
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/the-cortado/');
  });

  test('should load successfully', async ({ page }) => {
    expect(new URL(page.url()).pathname).toBe('/the-cortado/');
    await expect(page).toHaveTitle(/.+/);
  });

  test('should display critical page elements', async ({ page }) => {
    await verifyCriticalPageStructure(page);
  });

  test('should display the hero with the wordmark and presenters', async ({
    page,
  }) => {
    const hero = page.getByTestId('cortado-hero');

    await expect(hero).toBeVisible();
    // The wordmark is an inline SVG inside the h1; the aria-label names it.
    await expect(
      hero.getByRole('heading', { level: 1, name: 'The Cortado' })
    ).toBeVisible();
    // The presenters photo is the hero's only <img>; the wordmark SVG also
    // exposes the img role, so the element type is what disambiguates.
    await expect(hero.locator('img')).toBeVisible();
    await checkImages(page, { scope: hero });
  });

  test('should display the signup band', async ({ page }) => {
    const signup = page.getByTestId('cortado-signup');

    await expect(signup).toBeVisible();
    await expect(signup.locator('input[type="email"]')).toBeVisible();
    await expect(signup.locator('input[type="submit"]')).toBeVisible();
  });

  test('should feature the latest Cortado', async ({ page }) => {
    const latest = page.getByTestId('cortado-latest');

    await expect(latest).toBeVisible();
    await expect(latest.getByRole('heading', { level: 2 })).not.toHaveText('');
    await expect(latest.locator('a').first()).toHaveAttribute(
      'href',
      POST_URL_PATTERN
    );
  });

  test('should list past Cortados', async ({ page }) => {
    const past = page.getByTestId('cortado-past-issues');

    await expect(past).toBeVisible();
    await expect(
      past.getByRole('heading', { level: 2, name: /past cortados/i })
    ).toBeVisible();

    const cards = past.getByTestId('archive-post-no-thumbnail');
    expect(await cards.count()).toBeGreaterThan(0);
    await expect(cards.first().locator('a').first()).toHaveAttribute(
      'href',
      POST_URL_PATTERN
    );
  });

  test('should link to all newsletters from the footer row', async ({
    page,
  }) => {
    const footerRow = page.getByTestId('cortado-footer-row');

    await expect(
      footerRow.getByRole('link', { name: /discover all our newsletters/i })
    ).toHaveAttribute('href', /\/newsletters\/?$/);
  });

  test('should redirect the newsletter permalink to the archive', async ({
    request,
  }) => {
    // handle_newsletter_category_redirects() 301s the record's own page to the
    // category. The category's canonical path depends on its parent term, so
    // only the trailing slug is asserted.
    const response = await request.get(
      `/newsletters/the-cortado/?playwright_cache_bust=${Date.now()}`,
      { maxRedirects: 0 }
    );

    expect(response.status()).toBe(301);
    expect(response.headers()['location']).toMatch(/\/the-cortado\/$/);
  });

  test('should have no broken images in the main content', async ({ page }) => {
    await checkImages(page, { scope: page.getByTestId('main-content') });
  });

  test('should load without console errors', async ({ consoleErrors }) => {
    expect(consoleErrors).toEqual([]);
  });

  test('should be responsive at different viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.getByTestId('cortado-hero')).toBeVisible();
      await expect(page.getByTestId('cortado-past-issues')).toBeVisible();
    });
  });
});

test.describe('The Cortado front-page block', () => {
  test.beforeEach(async ({ page }) => {
    await gotoFresh(page, '/');

    const present = (await page.getByTestId('front-page-cortado').count()) > 0;
    test.skip(!present, 'The Cortado block is not in the front-page layout');
  });

  test('should link the wordmark to the archive', async ({ page }) => {
    const block = page.getByTestId('front-page-cortado');
    // The role="img" span and the inline SVG inside it both expose the name,
    // so the lookup is a filter on the anchor rather than a strict single match.
    // The `has` locator is page-rooted: it is re-applied inside each anchor.
    const wordmark = page.getByRole('img', { name: 'The Cortado' });

    await expect(block.locator(wordmark).first()).toBeVisible();
    await expect(block.locator('a', { has: wordmark })).toHaveAttribute(
      'href',
      /\/the-cortado\/$/
    );
  });

  test('should show the signup form', async ({ page }) => {
    const block = page.getByTestId('front-page-cortado');

    await expect(block.locator('input[type="email"]')).toBeVisible();
    await expect(block.locator('input[type="submit"]')).toBeVisible();
  });

  test('should feature the latest Cortado and list past Cortados', async ({
    page,
  }) => {
    const block = page.getByTestId('front-page-cortado');

    await expect(
      block.getByText('Latest Cortado', { exact: true })
    ).toBeVisible();
    await expect(
      block.getByRole('heading', { level: 3 }).first()
    ).not.toHaveText('');
    await expect(
      block.getByRole('heading', { level: 3, name: /past cortados/i })
    ).toBeVisible();

    const cards = block.getByTestId('archive-post-no-thumbnail');
    expect(await cards.count()).toBeGreaterThan(0);
    await expect(cards.first().locator('a').first()).toHaveAttribute(
      'href',
      POST_URL_PATTERN
    );
  });

  test('should stay visible across viewports', async ({ page }) => {
    await testResponsive(page, async () => {
      await expect(page.getByTestId('front-page-cortado')).toBeVisible();
    });
  });
});
