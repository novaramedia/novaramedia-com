/**
 * Search Results Tests
 *
 * Verifies the search page: a pre-filled search form, Suggested cards
 * (category archives, newsletters, site sections) above the post results,
 * and redirect-aware de-duplication. Assertions are structural: which kinds
 * of suggestion appear and where they point, never exact post content.
 *
 * One page load per URL, checks as steps: see "One load per page" in
 * docs/testing/testing.md.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const CARDS =
  '[data-testid="search-destinations"] [data-testid="search-destination"]';

test.describe('Search Results', () => {
  test('searching a show name', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/?s=novara+live');

    await test.step('pre-fills the results form with the query', async () => {
      await expect(
        page.getByTestId('search-results-form').locator('input[name="s"]')
      ).toHaveValue('novara live');
    });

    await test.step('shows suggestions before the results', async () => {
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

    await test.step('renders suggestions as cards with an image or tile', async () => {
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

    await test.step('links to the category archive', async () => {
      await expect(
        page.locator(
          `${CARDS}[data-destination-type="category"] a[href*="/novara-live/"]`
        )
      ).toHaveCount(1);
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });

  test('escapes markup in the query', async ({ page }) => {
    await gotoFresh(page, '/?s=%22%3E%3Cb%3Ex');

    await expect(
      page.getByTestId('search-results-form').locator('input[name="s"]')
    ).toHaveValue('"><b>x');
    await expect(page.getByTestId('main-content').locator('b')).toHaveCount(0);
  });

  test('collapses a redirected newsletter into its category', async ({
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

  test('searching a show by its short name', async ({ page }) => {
    await gotoFresh(page, '/?s=dyor');

    await test.step('links to the category archive', async () => {
      await expect(
        page.locator(
          `${CARDS}[data-destination-type="category"] a[href*="do-your-own-research"]`
        )
      ).toHaveCount(1);
    });

    await test.step('does not say nothing matched', async () => {
      await expect(page.locator(CARDS).first()).toBeVisible();
      await expect(page.getByTestId('no-results')).toHaveCount(0);
    });

    await test.step('tags the card with its top-level category', async () => {
      // Do Your Own Research sits under Video
      await expect(
        page
          .locator(`${CARDS}[data-destination-type="category"]`)
          .first()
          .locator('.ui-tag')
      ).toHaveText(/video/i);
    });
  });

  test('links to the shop when searching for merch', async ({ page }) => {
    await gotoFresh(page, '/?s=merch');

    await expect(
      page.locator(
        `${CARDS}[data-destination-type="destination"] a[href^="https://shop.novaramedia.com"]`
      )
    ).toHaveCount(1);
  });

  test('ignores a keyword inside a longer word', async ({ page }) => {
    // "prevent" contains "event", "workshop" contains "shop"
    await gotoFresh(page, '/?s=prevent+workshop');

    await expect(
      page.locator(`${CARDS}[data-destination-type="destination"]`)
    ).toHaveCount(0);
  });

  test('does not show suggestions on later results pages', async ({ page }) => {
    await gotoFresh(page, '/page/2/?s=novara+live');

    await expect(page.getByTestId('search-destinations')).toHaveCount(0);
  });
});
