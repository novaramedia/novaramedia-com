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
 * Page > Layout, so that test skips when the block is absent rather than
 * failing on configuration.
 */

const { test, expect } = require('./helpers/fixtures');
const checkImages = require('./helpers/checkImages');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

const POST_URL_PATTERN = /\/\d{4}\/\d{2}\/\d{2}\//;

test.describe('The Cortado archive', () => {
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the archive', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/the-cortado/');

    await test.step('loads successfully', async () => {
      expect(new URL(page.url()).pathname).toBe('/the-cortado/');
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('displays the hero with the wordmark and presenters', async () => {
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

    await test.step('displays the signup band', async () => {
      const signup = page.getByTestId('cortado-signup');

      await expect(signup).toBeVisible();
      await expect(signup.locator('input[type="email"]')).toBeVisible();
      await expect(signup.locator('input[type="submit"]')).toBeVisible();
    });

    await test.step('features the latest Cortado', async () => {
      const latest = page.getByTestId('cortado-latest');

      await expect(latest).toBeVisible();
      await expect(latest.getByRole('heading', { level: 2 })).not.toHaveText(
        ''
      );
      await expect(latest.locator('a').first()).toHaveAttribute(
        'href',
        POST_URL_PATTERN
      );
    });

    await test.step('lists past Cortados', async () => {
      const past = page.getByTestId('cortado-past-issues');

      await expect(past).toBeVisible();
      await expect(
        past.getByRole('heading', { level: 2, name: /past cortados/i })
      ).toBeVisible();

      const cards = past.getByTestId('archive-post-opinion');
      expect(await cards.count()).toBeGreaterThan(0);
      await expect(cards.first().locator('a').first()).toHaveAttribute(
        'href',
        POST_URL_PATTERN
      );
    });

    await test.step('links to all newsletters from the footer row', async () => {
      const footerRow = page.getByTestId('cortado-footer-row');

      await expect(
        footerRow.getByRole('link', { name: /discover all our newsletters/i })
      ).toHaveAttribute('href', /\/newsletters\/?$/);
    });

    await test.step('has no broken images in the main content', async () => {
      await checkImages(page, { scope: page.getByTestId('main-content') });
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page, async () => {
        await expect(page.getByTestId('cortado-hero')).toBeVisible();
        await expect(page.getByTestId('cortado-past-issues')).toBeVisible();
      });
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });

  // Request-only check, no page load: kept as its own test.
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
});

test.describe('The Cortado front-page block', () => {
  // One page load, checks as steps: see "One load per page" in
  // docs/testing/testing.md.
  test('renders the front-page block', async ({ page }) => {
    await gotoFresh(page, '/');

    const present = (await page.getByTestId('front-page-cortado').count()) > 0;
    test.skip(!present, 'The Cortado block is not in the front-page layout');

    const block = page.getByTestId('front-page-cortado');

    await test.step('links the wordmark to the archive', async () => {
      // The role="img" span and the inline SVG inside it both expose the name,
      // so the lookup is a filter on the anchor rather than a strict single
      // match. The `has` locator is page-rooted: it is re-applied inside each
      // anchor.
      const wordmark = page.getByRole('img', { name: 'The Cortado' });

      await expect(block.locator(wordmark).first()).toBeVisible();
      await expect(block.locator('a', { has: wordmark })).toHaveAttribute(
        'href',
        /\/the-cortado\/$/
      );
    });

    await test.step('shows the signup form', async () => {
      await expect(block.locator('input[type="email"]')).toBeVisible();
      await expect(block.locator('input[type="submit"]')).toBeVisible();
    });

    await test.step('features the latest Cortado and lists past Cortados', async () => {
      await expect(
        block.getByText('Latest Cortado', { exact: true })
      ).toBeVisible();
      await expect(
        block.getByRole('heading', { level: 3 }).first()
      ).not.toHaveText('');
      await expect(
        block.getByRole('heading', { level: 3, name: /past cortados/i })
      ).toBeVisible();

      const cards = block.getByTestId('archive-post-opinion');
      expect(await cards.count()).toBeGreaterThan(0);
      await expect(cards.first().locator('a').first()).toHaveAttribute(
        'href',
        POST_URL_PATTERN
      );
    });

    await test.step('stays visible across viewports', async () => {
      await testResponsive(page, async () => {
        await expect(page.getByTestId('front-page-cortado')).toBeVisible();
      });
    });
  });
});
