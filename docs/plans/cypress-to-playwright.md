# Cypress → Playwright Migration Plan

## Context

The theme's only automated testing is a Cypress e2e suite: 8 specs (~81 tests) in `cypress/e2e/`, run in CI via `.github/workflows/cypress.yml` against Kinsta staging (git-checkout deploy, whole job ~4 min). The suite is a smoke-test layer — page loads, critical `data-testid` elements, responsive viewports, image integrity, console errors.

Known pain points, documented in `docs/testing/`:

- **Third-party embed flake** — SoundCloud/YouTube iframes stall page load; Cypress `pageLoadTimeout` gates on full load, so single-post specs time out on slow embed days. Currently papered over with `retries: 2` in run mode.
- **`testIsolation: false`** — specs share state as a performance workaround, which makes test order matter within a spec.
- **No parallelism** — Cypress runs specs serially without the paid Dashboard; the test phase is single-threaded.
- **Debugging CI failures** means downloading video artifacts; there is no step-level trace.

Playwright addresses each directly: navigation waits are configurable (`domcontentloaded` instead of full load), `page.route()` can block third-party embed hosts entirely, workers parallelise for free, tests are isolated by default without a per-spec cost, and the trace viewer gives step-level CI debugging. `page.getByTestId()` matches the theme's existing `data-testid` convention natively.

This plan replaces the Cypress runner with Playwright at 1:1 coverage first, then expands the e2e suite. It supersedes the unit testing plan (see appendix).

## Goals

- 1:1 coverage parity with the current 8 specs before anything is deleted
- Kill the third-party embed flake class structurally (route-blocking), not via retries
- Keep the staging deploy pipeline unchanged — only the test runner swaps
- A ranked backlog of new e2e tests to add once migrated

## Non-goals

- Visual regression testing (possible later with Playwright screenshots, out of scope here)
- Unit tests (deferred — see appendix)
- Any change to the deploy/activate/cache-clear steps in CI

## Migration mapping

### Config: `cypress.config.js` → `playwright.config.js`

| Cypress | Playwright |
| --- | --- |
| `baseUrl` + `CYPRESS_BASE_URL` env override | `use.baseURL` + `PLAYWRIGHT_BASE_URL` env override (CI passes the same `STAGING_URL` secret) |
| `viewportWidth/Height: 1280×720` | `use.viewport: { width: 1280, height: 720 }` |
| `retries: { runMode: 2 }` | `retries: process.env.CI ? 2 : 0` |
| `video: true`, `screenshotOnRunFailure` | `use.video: 'retain-on-failure'`, `use.screenshot: 'only-on-failure'`, plus `use.trace: 'on-first-retry'` (new capability) |
| `defaultCommandTimeout: 10000` | `expect.timeout: 10000` |
| `pageLoadTimeout: 30000` | `use.navigationTimeout: 30000`, but navigations wait on `domcontentloaded` — embeds no longer gate page readiness |
| `testIsolation: false` | Dropped. Playwright isolates per test by default; with parallel workers the isolation cost that motivated the workaround disappears. Any test relying on in-spec state order must be untangled during conversion |
| — | `use.testIdAttribute` defaults to `data-testid` — matches theme convention with zero config |

### Custom commands (`cypress/support/commands.js`, 152 lines) → helpers/fixtures

| Cypress command | Playwright equivalent |
| --- | --- |
| `cy.verifyNoConsoleErrors()` (+ global `beforeEach` monitor in `e2e.js`) | Custom fixture: `page.on('console')` / `page.on('pageerror')` collector with the same third-party filter list; asserted explicitly or in fixture teardown |
| `cy.checkImages()` | Helper: `locator('img').evaluateAll(...)` asserting `naturalWidth > 0`, skipping lazy placeholders |
| `cy.testResponsive(callback)` | Helper looping `page.setViewportSize()` over the same three viewports (or per-project viewports later if wanted) |
| `cy.waitForWordPress()` | Delete — `expect(locator)` auto-waiting covers it |
| `cy.getByTestId()` | Built-in `page.getByTestId()` |
| `cy.verifyCriticalPageStructure()` | Shared helper in `tests/e2e/helpers/` |
| `cy.findPostUrlFromArchive(url)` | Async helper returning the first post href from an archive page, same serial-podcast exclusions |
| Cache-bust `cy.visit()` override (`e2e.js`) | `gotoFresh(page, path)` helper appending the same cache-bust query param — keep it; the Kinsta cache-clear CI step remains best-effort |

### Embed flake: structural fix

Default fixture blocks third-party embed hosts (`youtube.com`, `soundcloud.com`, `player.vimeo.com`, etc.) via `page.route()` — pages render their embed placeholders without loading the iframes, so no test waits on a third party. When the embed consent gate ships (PR #523), add one opt-in test that unblocks and exercises the gate flow explicitly.

## CI changes

`.github/workflows/cypress.yml` → `.github/workflows/playwright.yml`:

- Deploy / activate / cache-clear / verify-staging steps: **unchanged**, including the `kinsta-staging` concurrency group and fork-PR skip
- Test steps become: `actions/setup-node` → `npm ci` → `npx playwright install --with-deps chromium` → `npx playwright test`
- Artifacts: upload `playwright-report/` and `test-results/` (traces) on failure — replaces video-only artifacts
- Browser: Chromium only in CI (matches current Chrome-only CI; the `test:firefox` script convenience can carry over locally via `--project`)
- Workers: tests are read-only against staging, so parallel workers are safe; start with the default and tune down only if staging shows strain
- Expected runtime: deploy still dominates (~3 min); the test phase should shrink with parallelism

## Phased rollout

### Phase 0 — harness + first spec (one PR)

1. Add `@playwright/test` devDependency, `playwright.config.js`, `tests/e2e/` layout
2. Convert `homepage.cy.js` → `tests/e2e/homepage.spec.js`, porting the helpers it needs
3. Add `playwright.yml` running alongside `cypress.yml` (both suites green on every PR)
4. npm scripts: `test:pw` → `playwright test` (leave `test` as Cypress until cutover)

### Phase 1 — convert remaining 7 specs

One PR per spec or small batches; port remaining helpers as needed. Both suites stay green throughout. Untangle any test-order dependencies inherited from `testIsolation: false` as they surface.

### Phase 2 — cutover

1. Make the Playwright check required on `development`; remove Cypress from required checks (branch-protection settings, flag for repo admin)
2. Delete `cypress/`, `cypress.config.js`, the `cypress` devDependency, and `.github/workflows/cypress.yml`
3. `package.json`: `test` → `playwright test`; drop `test:headed`/`test:chrome`/`test:firefox`/`cy:open` in favour of Playwright equivalents (`--headed`, `--ui`)
4. Rewrite `docs/testing/` for Playwright (structure, helpers, debugging via trace viewer)

### Phase 3 — e2e expansion

New tests ranked by regression risk:

1. **Front page layout editor rendering** — recent feature, seed-then-deprecate migration behind it; assert section order matches saved layout
2. **Category archives** — articles/audio/video archives (only Novara Live is covered today)
3. **Embed consent gate** (once PR #523 ships) — placeholder renders, consent click loads iframe
4. **Newsletter signup block** — presence + validation states (multi-newsletter plan pending)
5. **Support page donation amount selection** — value select interactions, no payment submit
6. **Nav interactions** — hamburger open/close across viewports, links present when open (`site-nav` testid currently only contains toggles)
7. **Article page details** — share links have correctly-encoded hrefs, related posts render when set
8. **Search results + 404 + pagination** — cheap smoke coverage of secondary routes
9. **Accessibility smoke** — `@axe-core/playwright` on homepage + one single post (optional, Phase 3 tail)

## Files summary

**New:** `playwright.config.js`, `tests/e2e/*.spec.js`, `tests/e2e/helpers/`, `.github/workflows/playwright.yml`

**Deleted (Phase 2):** `cypress/`, `cypress.config.js`, `cypress` devDependency, `.github/workflows/cypress.yml`

**Modified:** `package.json` (scripts, devDependencies), `docs/testing/*`, `.github/copilot-instructions.md` Testing section

## Open questions

1. **Dual-suite window length** — running both suites doubles the staging-bound CI phase per PR during Phase 1. Acceptable short-term, but is a hard deadline wanted (e.g. cut over within one release cycle)?
2. **Worker count against staging** — default parallelism vs a capped `workers: 4` to be gentle on the staging box. Recommend default until it misbehaves.
3. **Required-check switch timing** — flip to Playwright-required at Phase 2 start, or after it has run green alongside Cypress for a full release cycle?

## Appendix: unit testing (deferred)

A broad unit-test programme (Jest + PHPUnit harnesses, CI job, conventions) was drafted and judged not worth the overhead for this codebase: the theme's JS is mostly DOM wiring and its PHP mostly templates — historical regressions are integration-shaped, which is e2e territory.

**Trigger to revisit:** the first pure-logic regression that the e2e suite misses ships. Then add the minimal harness for that language only and write the regression test.

Candidates identified for that day (verified paths as of 2026-07):

- JS (Jest, `babel-jest` on existing `.babelrc`): `src/js/functions/numberWithCommas.js`, `isNonEmptyString.js`, `localStorage.js`, `swipeDetect.js`
- PHP (PHPUnit + Brain Monkey, no WP install): `render_share_link()` in `lib/renderers.php`; the latest-articles image-slot algorithm inline in `partials/front-page/above-the-fold/latest-articles.php` (needs extraction into `lib/` first); thin wrappers in `lib/functions-utility.php` as harness smoke tests
