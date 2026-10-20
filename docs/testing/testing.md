# Testing

End-to-end smoke tests for the theme, written with [Playwright](https://playwright.dev/). They run in CI against Kinsta staging on pull requests that touch theme code (Markdown-only and `.github/`-only changes are skipped, and fork PRs have no secrets) and can be run locally against any deployment of the theme.

## Philosophy

A smoke-test layer, not a behaviour suite. Each spec checks that a page type:

- loads and has a title
- renders the critical `data-testid` landmarks (`site-header`, `main-content`, `site-footer`)
- holds up across mobile, tablet and desktop viewports
- has no broken images in the main content (where checked)
- logs no console errors the theme is responsible for

Deliberately out of scope: visual regression, complex interaction flows and the third-party services themselves. PHP logic has its own unit suite; see [PHP unit tests](#php-unit-tests) below.

## Layout

```
playwright.config.js           # runner config
tests/e2e/
├── homepage.spec.js
├── about-page.spec.js
├── jobs-page.spec.js
├── support-page.spec.js
├── novara-live-archive.spec.js
├── single-post.spec.js         # articles
├── single-post-audio.spec.js   # audio / podcast
├── single-post-video.spec.js
├── the-cortado.spec.js         # Cortado archive, newsletter 301, front-page block
└── helpers/
    ├── fixtures.js             # test/expect with embed blocking + console-error collector
    ├── gotoFresh.js            # cache-busting navigation
    ├── findPostUrlFromArchive.js
    ├── checkImages.js
    ├── testResponsive.js
    └── verifyCriticalPageStructure.js
```

Failure artefacts land in `test-results/` (traces, videos, screenshots) and `playwright-report/`. Both are git-ignored.

## Configuration

`playwright.config.js`:

| Setting             | Value                                                 | Notes                                                                       |
| ------------------- | ----------------------------------------------------- | --------------------------------------------------------------------------- |
| `baseURL`           | `PLAYWRIGHT_BASE_URL`, else `https://novaramedia.com` | CI passes the `STAGING_URL` secret                                          |
| viewport            | 1280×720                                              | `testResponsive` overrides per breakpoint                                   |
| `fullyParallel`     | on                                                    | tests are read-only, so parallel workers are safe                           |
| `retries`           | 2 in CI, 0 locally                                    |                                                                             |
| `expect.timeout`    | 10s                                                   |                                                                             |
| `navigationTimeout` | 30s                                                   | navigations wait on `domcontentloaded`, so embeds never gate page readiness |
| artefacts           | video and screenshot on failure, trace on first retry |                                                                             |
| browser             | Chromium only                                         | matches CI; add projects if cross-browser coverage is wanted                |

`data-testid` is Playwright's default test-id attribute, so `page.getByTestId('site-header')` needs no configuration.

## Running locally

Tests need a site running the branch's templates (the `data-testid` attributes live in PHP). Point them at DevKinsta or at staging:

```bash
npx playwright install chromium                                          # first run on a machine
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm test                 # headless, same as CI
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm run test:headed      # watch the browser
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm run test:ui          # Playwright UI mode
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npx playwright test single-post-audio   # one spec
```

Without `PLAYWRIGHT_BASE_URL` the suite runs against production.

Playwright's bundled Chromium refuses to start on older macOS releases. Workaround: a local-only config (outside the repo) that spreads `playwright.config.js` and sets `channel: 'chrome'` on the project's `use`, passed with `--config`.

## Helpers and fixtures

### `fixtures.js`

Import `test` and `expect` from here, not from `@playwright/test`. It adds:

- **Embed blocking.** Requests to third-party embed hosts (YouTube, SoundCloud, Vimeo, X, Instagram, TikTok, Spotify and their CDNs) are aborted at the browser context, so pages render their embed markup without waiting on a third party. This is the structural fix for the page-load flake that the Cypress suite papered over with long timeouts and retries. Opt out per test or describe block with `test.use({ blockEmbeds: false })`.
- **`consoleErrors`.** An auto fixture collecting `console.error` calls and uncaught page exceptions, filtered to errors the theme owns: analytics, social widgets and blocked embed hosts are ignored, matched on the message text and on the source URL's hostname. Assert with `expect(consoleErrors).toEqual([])`.

### `gotoFresh(page, path)`

Use instead of `page.goto`. Appends a unique `playwright_cache_bust` query string so Kinsta's full-page cache is bypassed even when the CI cache clear fails, and waits on `domcontentloaded`. Throws on a non-2xx response, as `cy.visit` did, so a broken deployment fails instead of skipping; pass `{ failOnStatusCode: false }` to visit an error page on purpose.

### `findPostUrlFromArchive(page, archiveUrl)`

Returns the first single-post permalink (`/YYYY/MM/DD/…`) from a category archive, or `null`. Excludes the serial-podcast categories (`foreign-agent`, `committed`) whose cards link to show pages rather than single posts. Keep that exclusion list in sync with `$serial_categories` in `lib/functions-hooks.php`.

The single-post specs call it once per worker in `beforeAll` on a throwaway page, then the test navigates to the post itself with `gotoFresh`. If it returns `null` the spec skips.

### `checkImages(page, { scope, limit })`

Asserts rendered images decoded to a non-zero width, skipping lazysizes data-URI placeholders. Polls, because navigation only waits for `domcontentloaded`.

### `testResponsive(page, callback)`

Sets mobile (375×667), tablet (768×1024) and desktop (1280×720) viewports in turn, asserts the three landmarks at each, then runs the optional callback.

### `verifyCriticalPageStructure(page)`

Header visible, main content attached, footer visible.

## Writing a spec

```js
const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');
const testResponsive = require('./helpers/testResponsive');
const verifyCriticalPageStructure = require('./helpers/verifyCriticalPageStructure');

test.describe('Page name', () => {
  test('renders the page', async ({ page, consoleErrors }) => {
    await gotoFresh(page, '/page-url/');

    await test.step('loads successfully', async () => {
      expect(new URL(page.url()).pathname).toBe('/page-url/');
      await expect(page).toHaveTitle(/.+/);
    });

    await test.step('displays critical page elements', async () => {
      await verifyCriticalPageStructure(page);
    });

    await test.step('is responsive at different viewports', async () => {
      await testResponsive(page);
    });

    await test.step('loads without console errors', async () => {
      expect(consoleErrors).toEqual([]);
    });
  });
});
```

### One load per page

Load each page once and run every check against it as a `test.step()`. Don't `gotoFresh` in `beforeEach` with one check per test.

Each `gotoFresh` is an uncached WordPress render on staging and costs 3–4s; the checks themselves cost almost nothing. One test per check multiplied page loads by ten for the same coverage (audit in `docs/plans/playwright-speed.md`). Steps keep the report specific: a failure names the step that failed.

- One `test()` per distinct URL. A spec covering several URLs (an archive plus a redirect, several categories) has one test each.
- Read-only steps first, then interactions (clicks, toggles), then `testResponsive` (it changes the viewport), then the console-error step last so it covers the whole visit.
- A failing step stops the rest of that test. That's accepted: fix the first failure, re-run.
- A step that only applies when optional content is present calls `step.skip(condition, reason)` (the step callback's argument), not `test.skip()`, which would skip the whole page.
- A check that needs its own setup before navigation (`test.use(...)`, `page.route(...)`) stays a separate test.

Guidelines:

- Tests are independent. Playwright runs them in parallel and in any order, so no test may rely on state another test left behind. Steps within one test share the page, in order.
- Select with `data-testid` attributes added to the templates, not CSS classes. List the ids currently in the templates with `git grep -oh 'data-testid="[^"]*"' -- '*.php' | sort -u`.
- Content changes daily. Assert shape (present, non-empty, matches a pattern) rather than specific text.
- Let `expect` auto-wait. No `waitForTimeout`.
- A bare `getByTestId('x')` is strict and fails if two elements match. Use `.first()` only where multiple matches are legitimate.

## CI

`.github/workflows/playwright.yml` runs on pull requests to `development`, `master` or `main`, and on `workflow_dispatch`. Its `paths-ignore` skips PRs that only change Markdown, `.github/`, `.editorconfig` or `.gitignore`. Each run:

1. Deploy the PR commit to Kinsta staging via SSH and `git checkout`
2. Activate the theme with WP-CLI and clear the Kinsta cache (best-effort; `gotoFresh` covers a failed clear)
3. Verify staging responds and print the `data-testid` values found on the homepage
4. `npm ci`, install Chromium, `npx playwright test` with `PLAYWRIGHT_BASE_URL` set from the `STAGING_URL` secret
5. Upload `playwright-report/` and `test-results/` as an artifact on failure
6. Reset staging to `development`

The `kinsta-staging` concurrency group serialises runs so only one workflow touches staging at a time. Fork PRs are skipped because the secrets are unavailable. Deploy dominates the runtime (about three minutes); the test phase is parallelised.

## Debugging a failure

**Locally:** `npm run test:ui`, or `npx playwright test --debug` to step through. `npx playwright show-report` opens the last HTML report; `npx playwright show-trace test-results/<test>/trace.zip` replays a trace step by step.

**In CI:** download the `playwright-artifacts-<run id>` artifact from the failed run, unzip it, then `npx playwright show-report playwright-report` or open a trace with `show-trace`. Traces record on the first retry, so any test that failed twice has one.

Common causes:

- A `data-testid` was removed or renamed in a template. The verify-staging step prints the ids present on the homepage.
- A template branch did not render on staging (for example no featured posts configured), so the id never appeared.
- Staging data a spec depends on is missing. `capsule-podcast-archives.spec.js` needs the season/episode backfill and the Death in Westminster redate on the staging DB (`scripts/one-off/2026-09-season-episode-backfill.php`). A staging refresh from production before production has had both wipes them; re-run the script on staging.
- A new third-party script logs errors. Add its host to the ignore list in `fixtures.js` only if the theme genuinely cannot control it.

## Expansion backlog

Ranked by regression risk, carried over from `docs/plans/archive/cypress-to-playwright.md`:

1. Front page layout editor rendering: assert section order matches the saved layout
2. Category archives for articles, audio and video (Novara Live and The Cortado are covered today)
3. Embed consent gate, once #523 ships: placeholder renders, consent click loads the iframe (needs `test.use({ blockEmbeds: false })`)
4. Newsletter signup block: presence and validation states
5. Support page donation amount selection, without submitting a payment
6. Nav interactions: hamburger open and close across viewports
7. Article page details: share link hrefs correctly encoded, related posts render when set
8. Search results, 404 and pagination smoke coverage
9. Accessibility smoke with `@axe-core/playwright` on the homepage and one single post

## PHP unit tests

A PHPUnit suite for the theme's PHP logic, mainly the helpers in `lib/`. No WordPress install or database: [Brain Monkey](https://giuseppe-mazzapica.gitbook.io/brain-monkey/) stubs WordPress functions and hooks per test, so a test runs in well under a second and in CI without secrets.

```
tests/unit/
├── composer.json        # test tooling: phpunit, brain/monkey (separate project, see below)
├── phpunit.xml          # bootstrap + suite config
├── bootstrap.php        # defines NM_THEME_ROOT, loads the tooling autoloader
├── TestCase.php         # base class: Brain Monkey setUp/tearDown, requireThemeFile()
└── lib/
    └── FunctionsUtilityTest.php
```

Run locally:

```bash
npm run test:php
```

That installs the tooling into `tests/unit/vendor/` (ignored) on first run and then runs the suite. CI runs the same on PHP 8.2 (production) and 8.4 (local DevKinsta, next PHP cycle) in `.github/workflows/php-unit.yml`; drop 8.2 from the matrix once production moves off it.

**Why a separate Composer project.** The theme's root `vendor/` and `composer.lock` are committed and deployed by `git pull`; a root `composer install` rewrites the committed autoloader files, and dev dependencies there would ship to Kinsta. `tests/unit/composer.json` keeps PHPUnit and Brain Monkey out of that tree entirely. Never add test tooling to the root `composer.json` while `vendor/` is committed.

This is a stopgap for the git-pull deploy, not the end state. When production deploys from CI with a `composer install --no-dev` step ([plan](../plans/production-ci-deploy.md)), `vendor/` stops being committed, PHPUnit and Brain Monkey move to `require-dev` in the root `composer.json`, and `tests/unit/` keeps only the suite.

**Writing a test.** Extend `NM\Tests\TestCase`, load the file under test once in `setUpBeforeClass()` with `self::requireThemeFile( 'lib/whatever.php' )`, and stub what the code calls:

```php
Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );   // stub a return
Functions\expect( 'get_template_directory' )->never();                     // assert not called
Actions\expectAdded( 'init' );                                             // hook registered at load
```

Good targets: pure helpers, option/meta formatting, rewrite and query logic, anything with branches. Not targets: template files and markup (Playwright covers those against staging), and code whose only job is to call WordPress.

Mirror `lib/<file>.php` as `tests/unit/lib/<File>Test.php`. The repo's phpcs standard applies to tests; where a test must touch the filesystem, disable `WordPress.WP.AlternativeFunctions` at the top of that file with a one-line reason, as `FunctionsUtilityTest.php` does.
