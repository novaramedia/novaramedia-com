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
    // No cache-bust query: the removed redirect compared the whole REQUEST_URI, query
    // included, so a query string would hide a regression.
    const response = await request.get('/committed/', { maxRedirects: 0 });

    expect(response.status()).toBe(200);
  });
});
