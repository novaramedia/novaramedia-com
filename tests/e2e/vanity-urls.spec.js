/**
 * Brand Vanity URL Tests
 *
 * Vanity paths serve their category archive in place (handle_internal_rewrites(),
 * lib/functions-rewrites.php) rather than redirecting to the category URL.
 * The Cortado's vanity path is covered in the-cortado.spec.js.
 */

const { test, expect } = require('./helpers/fixtures');

test.describe('Brand vanity URLs', () => {
  test('should serve /committed/ in place', async ({ request }) => {
    const response = await request.get(
      `/committed/?playwright_cache_bust=${Date.now()}`,
      { maxRedirects: 0 }
    );

    expect(response.status()).toBe(200);
  });
});
