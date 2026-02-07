# Cypress Testing - Consolidated Documentation

## Branch: `copilot/add-cypress-testing-ci`

This document consolidates all documentation for the Cypress testing implementation on the novaramedia-com theme.

## What Was Built

### Infrastructure

- **Cypress 13.17.0** added as dev dependency
- **8 test suites (89 tests)** covering homepage, support page, about page, jobs page, single post (article/video/audio), and Novara Live archive
- **Custom Cypress commands** in `cypress/support/commands.js` for page load checking, console error filtering, image validation, responsive testing, and WordPress-specific waits
- **GitHub Actions workflow** (`.github/workflows/cypress.yml`) that deploys the PR branch to Kinsta staging via SFTP, runs tests, then resets staging to development
- **`data-testid` attributes** added to PHP templates for stable test selectors

### Files Added

| File                                    | Purpose                                            |
| --------------------------------------- | -------------------------------------------------- |
| `cypress.config.js`                     | Cypress configuration (baseUrl, timeouts, retries) |
| `cypress/e2e/homepage.cy.js`            | Homepage tests (9 tests)                           |
| `cypress/e2e/support-page.cy.js`        | Support page tests (11 tests)                      |
| `cypress/e2e/single-post.cy.js`         | Article single post tests (11 tests)               |
| `cypress/e2e/single-post-video.cy.js`   | Video post tests (11 tests)                        |
| `cypress/e2e/single-post-audio.cy.js`   | Audio post tests (11 tests)                        |
| `cypress/e2e/about-page.cy.js`          | About page tests (11 tests)                        |
| `cypress/e2e/jobs-page.cy.js`           | Jobs page tests (12 tests)                         |
| `cypress/e2e/novara-live-archive.cy.js` | Novara Live category archive tests (13 tests)      |
| `cypress/support/commands.js`           | Custom Cypress commands                            |
| `cypress/support/e2e.js`                | Global test setup, uncaught exception handling     |
| `.github/workflows/cypress.yml`         | CI workflow with staging deployment                |
| `cypress.env.json.example`              | Example local config                               |
| `TESTING.md`                            | Developer-facing testing docs                      |

### PHP Templates Modified (data-testid additions)

| Template                                                         | data-testid values added                          |
| ---------------------------------------------------------------- | ------------------------------------------------- |
| `header.php`                                                     | `site-header`, `site-nav`                         |
| `footer.php`                                                     | `site-footer`                                     |
| `front-page.php`                                                 | `main-content`                                    |
| `index.php`                                                      | `main-content`, `post-list`                       |
| `page.php`                                                       | `main-content`                                    |
| `page-about.php`                                                 | `main-content`, `about-page`                      |
| `page-jobs.php`                                                  | `main-content`, `jobs-page`                       |
| `page-support.php`                                               | `main-content`, `support-page`                    |
| `single.php`                                                     | `main-content`, `single-post`                     |
| `category.php`                                                   | `main-content`, `post-list`                       |
| `category-novara-live.php`                                       | `main-content`, `post-list`                       |
| `partials/front-page/above-the-fold.php`                         | `post-list`                                       |
| `partials/front-page/above-the-fold/latest-article--default.php` | `post-title`                                      |
| `partials/singles/single-post-articles.php`                      | `post-content`                                    |
| `partials/singles/single-post-audio.php`                         | `audio-post-header`, `post-title`, `audio-player` |
| `partials/singles/single-post-video.php`                         | `video-post-header`, `post-title`, `video-player` |
| `partials/singles/articles/articles-header-*.php`                | `post-title` (3 layout variants)                  |

### CI Workflow Architecture

```
PR opened → SFTP deploy to staging → WP-CLI activate theme → Clear cache →
  → Verify staging accessible → npm ci → Cypress tests → Upload artifacts →
  → SFTP reset staging to development
```

**CI performance breakdown** (observed):

- SFTP deploy: ~10-12 min
- WP-CLI + cache: ~30 sec
- npm ci: ~25 sec
- Cypress tests: ~2 min
- SFTP cleanup: ~5 min
- **Total: ~18 min**

---

## Current Status: All Tests Passing

All 8 test suites passing in CI.

---

## Fixes Applied

### Fix 1: Homepage nav test — click toggle, verify panel links

Added `data-testid="site-nav-panel"` to the hidden nav panel in `header.php:62`.
Updated test to click the hamburger toggle, assert the panel is visible, and verify it contains links:

```js
cy.get('[data-testid="site-nav"] .site-header__nav-toggle').click();
cy.get('[data-testid="site-nav-panel"]').should('be.visible');
cy.get('[data-testid="site-nav-panel"] a')
  .should('have.length.greaterThan', 0)
  .first()
  .should('have.attr', 'href')
  .and('not.be.empty');
```

### Fix 2: Single post article content — added testid to all post types

Added `data-testid="post-content"` to the `the_content()` div in:

- `partials/singles/single-post-video.php:15`
- `partials/singles/single-post-audio.php:29`

Now all three post type templates (articles, video, audio) have a consistent `post-content` testid.

### Fix 3: Single post URL discovery — `findPostUrlFromArchive` command

The original tests found post URLs using broad selectors (`a[href*="/20"]`) from the homepage. This picked up links to show pages (serial podcast category templates like `category-foreign-agent.php` and `category-committed.php`) that are heavy and don't use the single post template.

**Changes:**

- Each single post test now navigates via its own top-level category archive (`/category/articles`, `/category/audio`, `/category/video`)
- Added `cy.findPostUrlFromArchive(archiveUrl)` custom command in `cypress/support/commands.js` that:
  - Visits the given category archive
  - Finds links inside `<article>` elements (from `flex-post.php` post cards)
  - Excludes posts from serial podcast categories (`.category-foreign-agent`, `.category-committed`) that redirect to show pages — keep in sync with `$serial_categories` in `lib/functions-hooks.php`
  - Filters to WordPress year/month/day permalink patterns (`/YYYY/MM/DD/`)
- Increased `cy.visit()` timeout to `120000ms` for audio posts due to SoundCloud embed loading
- Updated audio player test to check for the hydrated `iframe` rather than the `.soundcloud-lazy` placeholder

---

## Future Considerations

### Workflow Optimizations (from CYPRESS_WORKFLOW_NOTES.md)

- SFTP transfers are the bottleneck (~15 min of 18 min total)
- Consider rsync over SSH or diff-based uploads
- Remove temporary push trigger for feature branch after merge
- Consider restricting to PRs to master/main only

### Test Expansion (Phase 3)

- Visual regression testing
- Accessibility testing (pa11y, axe)
- Performance benchmarks
- User interaction flows
