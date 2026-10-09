/**
 * Support form attribution
 *
 * On submit, support forms carry the landing page's attribution context to
 * the donation app as nm_* query params. The donation host is intercepted so
 * nothing leaves the test; assertions are on the URL the form navigates to.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

test.describe('Support form attribution', () => {
  test('passes UTM tags and the Mailchimp campaign ID, never the subscriber ID', async ({
    page,
  }) => {
    let donationUrl = null;

    await page.route('https://donate.novaramedia.com/**', (route) => {
      donationUrl = new URL(route.request().url());
      return route.fulfill({
        status: 200,
        contentType: 'text/html',
        body: 'ok',
      });
    });

    await gotoFresh(
      page,
      '/support/?utm_source=mailchimp&utm_campaign=autumn&mc_cid=abc123&mc_eid=subscriber999'
    );

    await page
      .locator('form.support-form .support-form__submit:visible')
      .first()
      .click();
    await expect.poll(() => donationUrl).not.toBeNull();

    const params = donationUrl.searchParams;

    expect(params.get('nm_page')).toBe('/support/');
    expect(params.get('nm_utm_source')).toBe('mailchimp');
    expect(params.get('nm_utm_campaign')).toBe('autumn');
    expect(params.get('nm_mc_cid')).toBe('abc123');
    expect(donationUrl.search).not.toContain('subscriber999');
  });
});
