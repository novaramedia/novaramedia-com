# Cypress Testing - Consolidated Documentation

## Branch: `copilot/add-cypress-testing-ci`

This document consolidates all documentation for the Cypress testing implementation on the novaramedia-com theme.

## What Was Built

### Infrastructure

- **Cypress 13.17.0** added as dev dependency
- **8 test suites** covering homepage, support page, about page, jobs page, single post (article/video/audio), and Novara Live archive
- **Custom Cypress commands** in `cypress/support/commands.js` for page load checking, console error filtering, image validation, responsive testing, and WordPress-specific waits
- **GitHub Actions workflow** (`.github/workflows/cypress.yml`) that deploys the PR branch to Kinsta staging via SFTP, runs tests, then resets staging to development
- **`data-testid` attributes** added to PHP templates for stable test selectors

### Files Added

| File | Purpose |
|------|---------|
| `cypress.config.js` | Cypress configuration (baseUrl, timeouts, retries) |
| `cypress/e2e/homepage.cy.js` | Homepage tests (8 tests) |
| `cypress/e2e/support-page.cy.js` | Support page tests (10 tests) |
| `cypress/e2e/single-post.cy.js` | Article single post tests (10 tests) |
| `cypress/e2e/single-post-video.cy.js` | Video post tests (10 tests) |
| `cypress/e2e/single-post-audio.cy.js` | Audio post tests (10 tests) |
| `cypress/e2e/about-page.cy.js` | About page tests (10 tests) |
| `cypress/e2e/jobs-page.cy.js` | Jobs page tests (11 tests) |
| `cypress/e2e/novara-live-archive.cy.js` | Novara Live category archive tests (12 tests) |
| `cypress/support/commands.js` | Custom Cypress commands |
| `cypress/support/e2e.js` | Global test setup, uncaught exception handling |
| `.github/workflows/cypress.yml` | CI workflow with staging deployment |
| `cypress.env.json.example` | Example local config |
| `TESTING.md` | Developer-facing testing docs |

### PHP Templates Modified (data-testid additions)

| Template | data-testid values added |
|----------|--------------------------|
| `header.php` | `site-header`, `site-nav` |
| `footer.php` | `site-footer` |
| `front-page.php` | `main-content` |
| `index.php` | `main-content`, `post-list` |
| `page.php` | `main-content` |
| `page-about.php` | `main-content`, `about-page` |
| `page-jobs.php` | `main-content`, `jobs-page` |
| `page-support.php` | `main-content`, `support-page` |
| `single.php` | `main-content`, `single-post` |
| `category.php` | `main-content`, `post-list` |
| `category-novara-live.php` | `main-content`, `post-list` |
| `partials/front-page/above-the-fold.php` | `post-list` |
| `partials/front-page/above-the-fold/latest-article--default.php` | `post-title` |
| `partials/singles/single-post-articles.php` | `post-content` |
| `partials/singles/single-post-audio.php` | `audio-post-header`, `post-title`, `audio-player` |
| `partials/singles/single-post-video.php` | `video-post-header`, `post-title`, `video-player` |
| `partials/singles/articles/articles-header-*.php` | `post-title` (3 layout variants) |

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

## Current Status: 3 Failing Tests

Last CI run (2026-01-30, run #21527304743): **69 passing, 3 failing**

### Failure 1: Homepage — "should have working navigation links"

**Error:** `Timed out retrying after 10000ms: Expected to find element: [data-testid="site-nav"] a, but never found it.`

**Root cause:** The `data-testid="site-nav"` is on the `<nav>` at `header.php:34`. This nav only contains icon buttons (hamburger menu toggle, search toggle) wrapped in `<li>` elements — no `<a>` tags. The actual navigation links live in a separate `<nav class="site-header-nav">` element that is hidden by default (revealed by clicking the hamburger).

**Fix:** The test looks for `[data-testid="site-nav"] a` but should instead verify the nav toggle elements exist, or check for links in the header more broadly. The simplest fix is to check for `[data-testid="site-header"] a` instead, since the header does contain links (logo link, "Support Us" button).

### Failure 2: Single Post (Article) — "should display article content"

**Error:** `Timed out retrying after 10000ms: Expected to find element: [data-testid="post-content"], but never found it.`

**Root cause:** The `data-testid="post-content"` attribute only exists in `partials/singles/single-post-articles.php:57`. But `single.php` routes to different partials based on the post's top-level category:
- `articles` → `single-post-articles.php` (has `post-content`)
- `audio` → `single-post-audio.php` (does NOT have `post-content`)
- `video` → `single-post-video.php` (does NOT have `post-content`)

The `before()` hook finds a post URL from the homepage using `a[href*="/20"]`. If the first link found is a video or audio post, the test navigates to a post that doesn't use the articles template, so `[data-testid="post-content"]` won't exist.

**Fix options:**
1. Make the test more resilient by accepting either `post-content` or the general article body content
2. Add `data-testid="post-content"` to the video and audio single post templates too (in their content sections)
3. Filter the homepage links to only grab article posts (harder to do reliably)

**Recommended:** Option 2 — add `data-testid="post-content"` to the content sections in `single-post-video.php` and `single-post-audio.php`. This makes all single post types have a consistent testid for their body content.

### Failure 3: Single Post (Audio) — page load timeout

**Error:** `CypressError: Timed out after waiting 30000ms for your remote page to load.`

**Root cause:** Audio posts embed SoundCloud iframes. The `load` event won't fire until all resources (including third-party embeds) finish loading. SoundCloud embeds can be slow or unreliable, especially in CI environments.

**Fix:** The `before()` hook visits `/category/audio` to find an audio post URL. That page load is fine. The timeout happens in `beforeEach()` when visiting the actual audio post. Options:
1. Increase `pageLoadTimeout` for this spec
2. Add `{ timeout: 60000 }` to the visit call
3. Use `cy.visit(url, { failOnStatusCode: false, timeout: 60000 })` to be more tolerant

**Recommended:** Increase the timeout for the audio (and video) post specs since they embed third-party media players that slow the `load` event. A 60-second timeout is more reasonable for these pages.

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
  .first().should('have.attr', 'href').and('not.be.empty');
```

### Fix 2: Single post article content — added testid to all post types

Added `data-testid="post-content"` to the `the_content()` div in:
- `partials/singles/single-post-video.php:15`
- `partials/singles/single-post-audio.php:29`

Now all three post type templates (articles, video, audio) have a consistent `post-content` testid.

### Fix 3: Audio/video post timeout — increased visit timeout

Added `{ timeout: 60000 }` to `cy.visit()` in both `single-post-audio.cy.js` and `single-post-video.cy.js` to handle slow third-party embed loading (SoundCloud/YouTube).

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
