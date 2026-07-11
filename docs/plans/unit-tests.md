# Unit Testing Plan

## Context

The theme currently has **no unit test framework**. The only automated testing is the Cypress e2e suite (`cypress/`, run via `npm test` against Kinsta staging through `.github/workflows/cypress.yml`). Cypress is good at catching integration and rendering regressions, but it is slow (~4 min per CI run including staging deploy), depends on staging content, and cannot exercise edge cases in pure logic (e.g. "what does the image slot algorithm do with 2 non-news posts and 5 total posts?").

This plan adds a unit testing layer that **complements** Cypress — it does not replace it. Unit tests cover pure logic in isolation (fast, deterministic, no staging); Cypress continues to cover real-browser behaviour end to end.

Verified current state:

- `package.json` — `test` script is `cypress run`; no Jest, no test runner of any kind
- `composer.json` — no `require-dev`, no PHPUnit
- Webpack 5 + Babel (`.babelrc` with `@babel/preset-env`) already in place, so Jest slots in with near-zero transpilation config

## Goals

- Fast feedback on pure logic (milliseconds, not minutes) locally and in CI
- Tests that run without a WordPress install, staging environment, or network
- A place to put regression tests when fixing logic bugs (e.g. share URL encoding, image slot edge cases)
- No changes to the existing build system beyond adding test scripts (per repo rules, build config changes need team approval — test tooling sits alongside, not inside, the Webpack config)

## Non-goals

- Replacing or reducing the Cypress suite
- Coverage targets on day one (see Phase 3)
- Testing WordPress core behaviour, template rendering, or CSS

## JavaScript: Jest

**Decision: Jest.** It is the de facto standard for Webpack/Babel projects, needs no bundler integration (it uses `babel-jest` with the existing `.babelrc`), and `@wordpress/scripts` (already a devDependency) ships Jest-based tooling, so the ecosystem fit is native.

### Setup

- Add `jest` (+ `babel-jest`, `jest-environment-jsdom` if/when DOM-adjacent tests are needed) as devDependencies
- `jest.config.js` at repo root:
  - `testMatch: ['**/tests/js/**/*.test.js']` — keep tests in `tests/js/`, mirroring source layout, rather than co-located, to keep `src/` clean for the Webpack build
  - `testEnvironment: 'node'` by default; opt into `jsdom` per-file with a docblock pragma when a test needs DOM
- npm scripts (existing `test` stays Cypress to avoid breaking anyone's muscle memory / CI):
  - `test:unit` → `jest`
  - `test:unit:watch` → `jest --watch`

### First candidate modules (verified paths)

Pure functions in `src/js/functions/` are the natural first targets — standalone exports, no DOM, no jQuery:

| Target | Path | Why |
| --- | --- | --- |
| `numberWithCommas` | `src/js/functions/numberWithCommas.js` | Pure string/regex formatting; edge cases: decimals, negatives, non-numeric input |
| `isNonEmptyString` | `src/js/functions/isNonEmptyString.js` | Pure predicate (lodash `isString` + trim); trivially table-testable |
| `localStorage` helpers | `src/js/functions/localStorage.js` | Storage wrapper; testable with jsdom or a mock storage object |
| `swipeDetect` | `src/js/functions/swipeDetect.js` | Touch-delta maths; testable by feeding synthetic event coordinates |

Second wave: extractable logic inside `src/js/modules/` classes (e.g. donation value selection in `src/js/modules/Support.js`). These classes are DOM-coupled today; where a module has meaty logic, extract it into `src/js/functions/` first (the pattern `.github/copilot-instructions.md` already prescribes for reusable functions), then test the extracted function. Do not force tests onto thin DOM-wiring modules like `Analytics.js`.

## PHP: OPEN QUESTION — framework choice pending team decision

Two viable approaches. **A decision is needed before Phase 2 starts.**

### Option A: PHPUnit + Brain Monkey (unit tests, mocked WP)

WP functions (`get_post_type`, `wp_get_environment_type`, `esc_attr`, …) are mocked via [Brain Monkey](https://brain-wp.github.io/BrainMonkey/); tests run against plain PHP with no WordPress install.

- **Pros:** fast (whole suite in seconds), runs anywhere PHP + Composer runs (local, CI, no MySQL), forces functions to declare their WP dependencies explicitly, trivial GitHub Actions job
- **Cons:** mocks can drift from real WP behaviour; anything touching the database or hooks deeply is awkward; adds `require-dev` deps (phpunit, brain/monkey, mockery)

### Option B: wp-env integration tests (real WordPress)

`@wordpress/env` spins up a Dockerised WP + MySQL; tests run via the WP PHPUnit test suite against a real install.

- **Pros:** real WP behaviour, can test hooks/filters/queries/template output faithfully; official WP tooling
- **Cons:** much slower (Docker boot + WP install per CI run), heavier local requirement (Docker), more CI complexity and flakiness surface, overkill for the pure-logic functions this plan targets first

### Recommendation (non-binding)

**Start with Option A.** The first PHP targets below are pure or near-pure logic where mocked WP functions are a feature, not a compromise. Option B can be added later *in addition* if we ever need to test hook/query behaviour — the two are not mutually exclusive. But this is the team's call; the trade-off that matters is "mock drift risk" vs "CI weight", and reasonable people land on either side.

### First candidate PHP targets (verified paths)

| Target | Path | Notes |
| --- | --- | --- |
| `render_share_link()` | `lib/renderers.php` (defined ~line 633) | Builds platform-specific share URLs (`rawurlencode`, `sprintf`); ideal for URL-encoding and platform-matrix regression tests. Needs WP escaping functions mocked (Brain Monkey stubs `esc_*` as pass-through by default) |
| Latest-articles image slot algorithm | `partials/front-page/above-the-fold/latest-articles.php` (inline, ~lines 21–99) | Pure array logic (slot assignment, gap enforcement, large/small sizing) currently inline in a template partial. **Prerequisite:** extract into a function in `lib/` (e.g. `nm_get_latest_articles_image_map( $posts_are_news, $count )`) so it can be required and tested without rendering the partial. The extraction is a pure refactor and should be its own reviewed PR |
| `nm_is_production()` / `is_single_type()` | `lib/functions-utility.php` | Thin WP wrappers — low value alone, but cheap smoke tests that prove the harness works |

## CI integration

Repo already uses GitHub Actions (`.github/workflows/cypress.yml`, `deploy-staging.yml`, `release-notification.yml`). Unit tests need **no staging deploy**, so they should run as a fast, independent workflow:

- New workflow `.github/workflows/unit-tests.yml`:
  - Trigger: `pull_request` targeting `development` (and `push` to `development`)
  - Job 1 (Phase 1): `actions/setup-node` → `npm ci` → `npm run test:unit`
  - Job 2 (Phase 2, if Option A): `shivammathur/setup-php` → `composer install` → `vendor/bin/phpunit`
- Expected runtime: well under a minute — it gives PR feedback long before the ~4 min Cypress run finishes
- Keep it separate from `cypress.yml` rather than a job inside it: Cypress needs staging secrets and deploy sequencing; unit tests should stay runnable on any fork/branch with zero secrets
- Once stable, mark the unit-test check as required on `development` (repo settings change, flag for whoever administers branch protection)

## Phased rollout

### Phase 1 — Jest setup + first JS tests

1. Add Jest devDependencies, `jest.config.js`, `test:unit` scripts
2. Tests for `numberWithCommas` and `isNonEmptyString` (proves the harness)
3. Add `unit-tests.yml` workflow running the JS job
4. Follow-ups: `localStorage.js`, `swipeDetect.js`

### Phase 2 — PHP decision + setup

1. Team decides Option A vs Option B (see open question above)
2. Set up chosen harness; smoke tests against `lib/functions-utility.php`
3. Extract the latest-articles image slot algorithm into `lib/` (pure refactor PR, Cypress guards against regressions)
4. Tests for `render_share_link()` and the extracted image slot function
5. Add the PHP job to `unit-tests.yml`

### Phase 3 — expectations and habit

1. Convention: logic bug fixes ship with a regression unit test where the logic is unit-testable
2. Convention: new `src/js/functions/` files ship with tests
3. Revisit coverage reporting (`jest --coverage`) once there is enough surface for the number to mean something — no hard threshold before then
4. Update `.github/copilot-instructions.md` Testing section and `docs/testing/` overview to reference the unit suite

## Files summary

### New (Phase 1)

- `jest.config.js`
- `tests/js/functions/numberWithCommas.test.js`
- `tests/js/functions/isNonEmptyString.test.js`
- `.github/workflows/unit-tests.yml`

### Modified (Phase 1)

- `package.json` — devDependencies + `test:unit` scripts (no changes to `dev`/`build`/`release`)

### New/Modified (Phase 2, depends on decision)

- Option A: `phpunit.xml.dist`, `tests/php/`, `composer.json` `require-dev`
- Option B: `.wp-env.json`, WP test suite scaffolding
- `lib/` — extracted image slot function (+ `partials/front-page/above-the-fold/latest-articles.php` slimmed to call it)

## Open questions

1. **PHP framework: Option A (PHPUnit + Brain Monkey) vs Option B (wp-env)?** Recommendation is A, decision pending — see trade-offs above.
2. Should the unit-test CI check become a required status check on `development` immediately after Phase 1, or only once the suite has run green for a few weeks?
3. Is anyone attached to `npm test` meaning Cypress? If not, a later rename (`test` → run both suites, `test:e2e` → Cypress) would be tidier, but is deliberately out of scope here.
