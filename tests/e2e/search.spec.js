/**
 * Search Results Tests
 *
 * Verifies the search page: a pre-filled search form, Suggested cards
 * (category archives, newsletters, site sections) above the post results,
 * and redirect-aware de-duplication. Assertions are structural: which kinds
 * of suggestion appear and where they point, never exact post content.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const CARDS =
  '[data-testid="search-destinations"] [data-testid="search-destination"]';

test.describe('Search Results', () => {
  test('should pre-fill the results form with the query', async ({ page }) => {
    await gotoFresh(page, '/?s=novara+live');

    await expect(
      page.getByTestId('search-results-form').locator('input[name="s"]')
    ).toHaveValue('novara live');
  });

  test('should escape markup in the query', async ({ page }) => {
    await gotoFresh(page, '/?s=%22%3E%3Cb%3Ex');

    await expect(
      page.getByTestId('search-results-form').locator('input[name="s"]')
    ).toHaveValue('"><b>x');
    await expect(page.getByTestId('main-content').locator('b')).toHaveCount(0);
  });

  test('should show suggestions before the results', async ({ page }) => {
    await gotoFresh(page, '/?s=novara+live');

    const isBefore = await page.evaluate(() => {
      const suggested = document.querySelector(
        '[data-testid="search-destinations"]'
      );
      const heading = document.querySelector(
        '[data-testid="search-results-heading"]'
      );

      return Boolean(
        suggested &&
          heading &&
          suggested.compareDocumentPosition(heading) &
            Node.DOCUMENT_POSITION_FOLLOWING
      );
    });

    expect(isBefore).toBe(true);
  });

  test('should render suggestions as cards with an image or tile', async ({
    page,
  }) => {
    await gotoFresh(page, '/?s=novara+live');

    const cards = page.locator(CARDS);
    await expect(cards.first()).toBeVisible();
    // Same grid classes as the post cards below
    await expect(cards.first()).toHaveClass(/is-xxl-8/);

    const count = await cards.count();
    for (let i = 0; i < count; i++) {
      await expect(
        cards.nth(i).getByTestId('search-destination-image')
      ).toHaveCount(1);
    }
  });

  test('should link to a category archive when searching its name', async ({
    page,
  }) => {
    await gotoFresh(page, '/?s=novara+live');

    await expect(
      page.locator(
        `${CARDS}[data-destination-type="category"] a[href*="/novara-live/"]`
      )
    ).toHaveCount(1);
  });

  test('should collapse a redirected newsletter into its category', async ({
    page,
  }) => {
    await gotoFresh(page, '/?s=the+cortado');

    await expect(page.locator(`${CARDS} a[href*="the-cortado"]`)).toHaveCount(
      1
    );
    await expect(
      page.locator(
        `${CARDS}[data-destination-type="category"] a[href*="/the-cortado/"]`
      )
    ).toHaveCount(1);
  });

  test('should match a category by its short name', async ({ page }) => {
    await gotoFresh(page, '/?s=dyor');

    await expect(
      page.locator(
        `${CARDS}[data-destination-type="category"] a[href*="do-your-own-research"]`
      )
    ).toHaveCount(1);
  });

  test('should link to the shop when searching for merch', async ({ page }) => {
    await gotoFresh(page, '/?s=merch');

    await expect(
      page.locator(
        `${CARDS}[data-destination-type="destination"] a[href^="https://shop.novaramedia.com"]`
      )
    ).toHaveCount(1);
  });

  test('should not show suggestions on later results pages', async ({
    page,
  }) => {
    await gotoFresh(page, '/page/2/?s=novara+live');

    await expect(page.getByTestId('search-destinations')).toHaveCount(0);
  });
});
