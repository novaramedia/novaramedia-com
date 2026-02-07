# Testing Documentation

This document provides detailed information about the Cypress testing setup for the Novara Media WordPress theme.

## Overview

We use [Cypress](https://www.cypress.io/) for automated end-to-end testing of critical theme functionality. Tests run automatically on Pull Requests via GitHub Actions and must pass before code can be merged.

## Test Philosophy

Our tests follow a **smoke testing** approach, focusing on:

- **Page loading** - Verify pages load without errors
- **Critical elements** - Ensure key DOM elements are present
- **Responsive design** - Test across mobile, tablet, and desktop viewports
- **Asset integrity** - Check for broken images in main content
- **JavaScript errors** - Catch console errors (excluding third-party scripts)

We **intentionally avoid**:

- Visual regression testing (planned for Phase 2)
- Complex user interaction flows (planned for Phase 2)
- Backend/PHP unit tests (separate concern)
- Testing third-party integrations (e.g., payment processors)

## Test Structure

### Directory Layout

```
cypress/
├── e2e/                          # Test files
│   ├── homepage.cy.js
│   ├── support-page.cy.js
│   ├── about-page.cy.js
│   ├── jobs-page.cy.js
│   ├── single-post.cy.js        # Article single posts
│   ├── single-post-audio.cy.js  # Audio/podcast single posts
│   ├── single-post-video.cy.js  # Video single posts
│   └── novara-live-archive.cy.js
├── support/                      # Helper files
│   ├── commands.js               # Custom Cypress commands
│   └── e2e.js                   # Global configuration
├── fixtures/                     # Test data (currently unused)
├── videos/                       # Test recordings (git-ignored)
└── screenshots/                  # Failure screenshots (git-ignored)
```

### Configuration

See `cypress.config.js` for:

- Base URL configuration
- Viewport settings
- Timeout values
- Retry logic for flaky tests
- Video/screenshot settings

## Running Tests

### Locally

Tests must run against a site with the branch's `data-testid` template attributes deployed. Use your local DevKinsta instance or the CI staging site.

**Quick Start:**

```bash
# Run against local DevKinsta (recommended)
CYPRESS_BASE_URL=https://novaramediacom.local npm test

# Open Cypress UI for interactive debugging
CYPRESS_BASE_URL=https://novaramediacom.local npm run cy:open

# Run a single spec
CYPRESS_BASE_URL=https://novaramediacom.local npx cypress run --spec cypress/e2e/single-post-audio.cy.js
```

**Advanced Options:**

```bash
npm run test:chrome      # Run in Chrome
npm run test:firefox     # Run in Firefox
npm run test:headed      # See browser while running
```

### In CI (GitHub Actions)

Tests run automatically on:

- Pull requests to `master`, `main`, or `development` branches
- Direct pushes to these branches

**Workflow configuration:** `.github/workflows/cypress.yml`

**Key features:**

- Runs in Ubuntu with Chrome browser
- 15-minute timeout per job
- Uploads videos/screenshots on failure
- Uses npm caching for faster runs

## Custom Commands

We've created several helper commands to make tests more maintainable:

### `cy.checkPageLoad()`

Sets up console error monitoring for the page.

```javascript
cy.checkPageLoad();
cy.visit('/');
```

### `cy.verifyNoConsoleErrors()`

Verifies no relevant console errors occurred (filters out third-party script errors).

```javascript
cy.checkPageLoad();
cy.visit('/some-page');
cy.verifyNoConsoleErrors();
```

### `cy.checkImages()`

Validates that images loaded successfully (skips lazy-loaded placeholders).

```javascript
cy.checkImages();
```

### `cy.testResponsive(callback)`

Tests behavior across mobile, tablet, and desktop viewports.

```javascript
cy.testResponsive((viewport) => {
  cy.get('.menu').should('be.visible');
});
```

### `cy.waitForWordPress()`

Waits for WordPress-specific elements to be ready.

```javascript
cy.waitForWordPress();
```

### `cy.findPostUrlFromArchive(archiveUrl)`

Visits a category archive page and finds the first single post URL from the post cards. Excludes serial podcast categories that redirect to show pages.

```javascript
cy.findPostUrlFromArchive('/category/audio').then((url) => {
  // url is a string like '/2026/01/27/episode-title/' or null if none found
});
```

## Writing New Tests

### Test File Template

```javascript
describe('Page Name', () => {
  beforeEach(() => {
    cy.checkPageLoad();
    cy.visit('/page-url');
  });

  it('should load successfully', () => {
    cy.url().should('include', '/page-url');
    cy.title().should('not.be.empty');
  });

  it('should display critical elements', () => {
    cy.get('header').should('be.visible');
    cy.get('main').should('exist');
    cy.get('footer').should('be.visible');
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive', () => {
    const viewports = [
      { width: 375, height: 667 }, // Mobile
      { width: 768, height: 1024 }, // Tablet
      { width: 1280, height: 720 }, // Desktop
    ];

    viewports.forEach((viewport) => {
      cy.viewport(viewport.width, viewport.height);
      cy.get('main').should('be.visible');
    });
  });
});
```

### Best Practices

1. **Keep tests simple** - Focus on critical path, not edge cases
2. **Use data attributes** - Add `data-testid="element-name"` for stable selectors
3. **Avoid hard-coded delays** - Use `cy.wait()` with aliases, not arbitrary timeouts
4. **Be resilient** - Tests should work against live content that changes
5. **Filter third-party errors** - Don't fail on Google Analytics, social media widgets, etc.

## Debugging Failed Tests

### Local Debugging

1. **Run with Test Runner:**

   ```bash
   npm run cy:open
   ```

   - Click on failed test
   - Use time-travel debugging
   - Inspect DOM at failure point

2. **View screenshots:**

   ```bash
   open cypress/screenshots/
   ```

3. **Watch videos:**
   ```bash
   open cypress/videos/
   ```

### CI Debugging

1. Go to failed GitHub Actions run
2. Click "Summary" tab
3. Download "cypress-artifacts" or "cypress-results"
4. Extract and view videos/screenshots

### Common Issues

**"Timed out retrying" errors:**

- Element selector changed - update test
- Page loads slowly - increase timeout in config
- Element is hidden - check CSS/responsive behavior

**"ResizeObserver" or analytics errors:**

- These are filtered automatically - if test fails, it's something else

**Flaky tests:**

- Tests retry 2x in CI automatically
- Consider adding explicit waits: `cy.get('.element').should('be.visible')`
- Check for race conditions in page load

## Environment Configuration

### Environment Variables

- `CYPRESS_BASE_URL` - Override base URL (default: https://novaramedia.com)
- `CYPRESS_VIDEO` - Enable/disable video recording
- `CYPRESS_SCREENSHOT_ON_FAILURE` - Enable/disable failure screenshots

### GitHub Secrets

For CI, you can configure:

- `CYPRESS_BASE_URL` - Test against staging instead of production
- Add as repository secret in Settings → Secrets and variables → Actions

## Test Coverage Roadmap

### Phase 1: Core Tests ✅ (Complete)

- [x] Homepage
- [x] Support page
- [x] Single post (article)
- [x] GitHub Actions CI

### Phase 2: Secondary Views ✅ (Complete)

- [x] Single post (video category)
- [x] Single post (audio category)
- [x] About page
- [x] Jobs page
- [x] Novara Live category archive

### Phase 3: Future Enhancements (Planned)

- [ ] Visual regression testing
- [ ] Accessibility testing (pa11y, axe)
- [ ] Performance benchmarks
- [ ] User interaction flows (comments, search)
- [ ] Backend PHP unit tests (PHPUnit)

## Contributing

When adding new tests:

1. Follow the existing test structure
2. Add tests to `cypress/e2e/` directory
3. Use descriptive test names
4. Update this documentation if adding new patterns
5. Ensure tests pass locally before pushing
6. Tests must pass in CI before PR can merge

## Resources

- [Cypress Documentation](https://docs.cypress.io/)
- [Cypress Best Practices](https://docs.cypress.io/guides/references/best-practices)
- [WordPress Testing Best Practices](https://make.wordpress.org/core/handbook/testing/)
- [Our Workflow Documentation](.github/workflows/README.md)
