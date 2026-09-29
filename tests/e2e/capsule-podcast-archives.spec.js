/**
 * Capsule podcast archives
 *
 * Episode labels come from season/episode meta (_nm_episode,
 * _nm_episode_label), not the standfirst. Expected strings are the labels
 * live on 2026-09-29, so the switch must be invisible to readers.
 * Requires the season/episode backfill on the target environment.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const ARCHIVES = [
  {
    path: '/category/committed/',
    labels: ['Episode 1', 'Episode 2', 'Episode 3', 'Episode 4'],
  },
  {
    path: '/category/foreign-agent/',
    labels: [
      'Episode 1', 'Episode 2', 'Episode 3', 'Bonus 1', 'Episode 4',
      'Episode 5', 'Bonus 2', 'Episode 6', 'The producers', 'Credits',
    ],
  },
  {
    path: '/category/death-in-westminster/',
    labels: [
      'Episode 1', 'Episode 2', 'Episode 3',
      'Episode 4', 'Episode 5', 'Episode 6',
    ],
  },
];

for (const archive of ARCHIVES) {
  test.describe(`Capsule archive ${archive.path}`, () => {
    test('renders episode labels from meta in date order', async ({ page }) => {
      await gotoFresh(page, archive.path);

      await expect(page.getByTestId('episode-label')).toHaveText(archive.labels);
    });

    test('renders no empty episode label', async ({ page }) => {
      await gotoFresh(page, archive.path);

      const labels = await page.getByTestId('episode-label').allTextContents();

      expect(labels.every((text) => text.trim() !== '')).toBe(true);
    });
  });
}
