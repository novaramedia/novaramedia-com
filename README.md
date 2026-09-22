# no future utopia now

novaramedia.com

## We are Dev[0]

### How to: Testing

End-to-end smoke tests run on [Playwright](https://playwright.dev/). Full guide: [docs/testing/testing.md](docs/testing/testing.md).

#### Running tests locally

Tests need a site running this theme's templates, so point them at your DevKinsta site (or staging):

```bash
npm install
npx playwright install chromium                                    # first run only
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm test           # headless, same as CI
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm run test:headed
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm run test:ui    # interactive UI mode
```

Without `PLAYWRIGHT_BASE_URL` the suite runs against production.

#### CI

Pull requests to `development`, `master` or `main` deploy their commit to Kinsta staging and run the suite there (`.github/workflows/playwright.yml`). Fork PRs are skipped, as are PRs that only touch Markdown or `.github/`. On failure the HTML report and traces are attached to the run as an artifact: download it and open with `npx playwright show-report <dir>`.

#### Coverage

Eight specs: homepage, support, about and jobs pages, the Novara Live archive, and single article, audio and video posts. Each checks that the page loads, the critical `data-testid` landmarks render, the layout holds at mobile, tablet and desktop widths, and no theme-owned console errors fire. Third-party embeds are blocked during tests so no run waits on SoundCloud or YouTube.

### Howto: release

- Pull `development`
- `npm run release`
- Don't commit, tag or push in release-it process
- After post release-it scripts are run commit in format `Build: x.x.x`
- Create PR to master branch in format `Version x.x.x` with changelog entries as description

### Semver

- Patches for bugfixes, copy updates, minor changes
- Minor version for any significant new functionality
- Major for breaking changes and significant design system iterations
