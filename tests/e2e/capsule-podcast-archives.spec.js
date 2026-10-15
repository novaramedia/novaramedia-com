/**
 * Capsule podcast archives
 *
 * Episode labels come from season/episode meta (_nm_episode,
 * _nm_episode_label), not the standfirst. Assertions are structural, not
 * content-exact: editors may add or remove episodes, and staging data lags
 * production. Requires the season/episode backfill (and the Death in
 * Westminster redate) on the target environment.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const ARCHIVES = [
  '/category/committed/',
  '/category/foreign-agent/',
  '/category/death-in-westminster/',
];

for (const path of ARCHIVES) {
  test(`${path} labels episodes from meta, oldest first`, async ({ page }) => {
    await gotoFresh(page, path);

    const labels = (await page.getByTestId('episode-label').allTextContents()).map((text) => text.trim());

    expect(labels.length).toBeGreaterThan(0);
    expect(labels).not.toContain('');

    // Numbered labels read "Episode N" and run in ascending order; override
    // labels (bonuses, trailers) may sit between them.
    const numbers = labels
      .map((text) => text.match(/^Episode (\d+)$/i))
      .filter(Boolean)
      .map((match) => Number(match[1]));

    expect(numbers.length).toBeGreaterThan(0);
    expect(numbers).toEqual([...numbers].sort((a, b) => a - b));
  });
}
