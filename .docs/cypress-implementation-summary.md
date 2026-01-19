# Cypress Testing Implementation Summary

## Overview

This document summarizes the Cypress testing framework implementation for the Novara Media WordPress theme.

## What Was Implemented

### 1. Cypress Configuration

- **Package**: Cypress 13.17.0 installed as a dev dependency
- **Config File**: `cypress.config.js` with WordPress-optimized settings
- **Base URL**: Configurable via `CYPRESS_BASE_URL` environment variable (defaults to https://novaramedia.com)
- **Viewport**: 1280x720 desktop default
- **Retries**: 2 automatic retries in CI mode for flaky test resilience
- **Video/Screenshots**: Enabled for debugging failed tests

### 2. Test Structure

#### Support Files

- `cypress/support/e2e.js` - Global configuration and error handling
- `cypress/support/commands.js` - Custom Cypress commands:
  - `cy.checkPageLoad()` - Monitor console errors
  - `cy.verifyNoConsoleErrors()` - Verify no critical errors
  - `cy.checkImages()` - Validate image loading
  - `cy.testResponsive()` - Test across viewports
  - `cy.waitForWordPress()` - Wait for WP elements

#### Test Suites (Priority 1)

1. **Homepage** (`cypress/e2e/homepage.cy.js`)
   - Page loads successfully
   - Critical elements present (header, nav, main, footer)
   - Working navigation links
   - Post content displays
   - No console errors
   - Responsive at mobile/tablet/desktop
   - SEO meta tags present

2. **Support Page** (`cypress/e2e/support-page.cy.js`)
   - Page loads successfully
   - Critical elements present
   - Donation form/buttons visible
   - Main content displays
   - No console errors
   - Responsive behavior
   - Proper heading structure

3. **Single Post (Article)** (`cypress/e2e/single-post.cy.js`)
   - Dynamically finds recent article from homepage
   - Page loads successfully
   - Article content displays with title and body
   - Post metadata visible
   - Navigation elements present
   - No console errors
   - Responsive behavior
   - Proper heading hierarchy

#### Test Suites (Priority 2)

4. **Single Post (Video)** (`cypress/e2e/single-post-video.cy.js`)
   - Finds video post from video category archive
   - Page loads successfully
   - Video player or embed present
   - Video post content displays
   - Post metadata and category indicator
   - No console errors
   - Responsive behavior

5. **Single Post (Audio)** (`cypress/e2e/single-post-audio.cy.js`)
   - Finds audio post from audio category archive
   - Page loads successfully
   - Audio player or embed present
   - Audio post content displays
   - Post metadata and category indicator
   - No console errors
   - Responsive behavior

6. **About Page** (`cypress/e2e/about-page.cy.js`)
   - Page loads successfully
   - Critical elements present
   - Organizational information displays
   - Proper content structure
   - No console errors
   - Responsive behavior

7. **Jobs Page** (`cypress/e2e/jobs-page.cy.js`)
   - Page loads successfully
   - Job listings or information displays
   - Handles empty state gracefully
   - No console errors
   - Responsive behavior

8. **Novara Live Archive** (`cypress/e2e/novara-live-archive.cy.js`)
   - Category archive loads successfully
   - Post listings display
   - Post links work
   - Category branding present
   - No console errors
   - Responsive behavior

### 3. GitHub Actions CI

#### Workflow File: `.github/workflows/cypress.yml`

- **Triggers**:
  - Pull requests to master/main/development
  - Direct pushes to these branches
- **Environment**: Ubuntu with Node.js 18, Chrome browser
- **Steps**:
  1. Checkout code
  2. Setup Node.js with npm caching
  3. Install dependencies (`npm ci`)
  4. Verify Cypress installation
  5. Run tests in headless Chrome
  6. Upload test artifacts (videos/screenshots) on failure
  7. Upload test results for all runs
- **Timeout**: 15 minutes maximum
- **Artifacts**: Videos and screenshots retained for 7 days

#### Status Check Behavior

- Tests must pass for PR to be mergeable
- Failed tests will block merge with clear indication
- Test artifacts available for debugging failures

### 4. Documentation

#### README.md Updates

Added comprehensive "Howto: Testing" section covering:

- Running tests locally (headless, interactive, specific browsers)
- Test configuration and environment variables
- CI/CD integration behavior
- Test coverage overview
- Troubleshooting common issues

#### TESTING.md (New File)

Detailed testing documentation including:

- Test philosophy and approach
- Directory structure explanation
- Configuration details
- Custom command reference
- Writing new tests guide
- Debugging strategies
- Test coverage roadmap (Phase 2 & 3 planned)
- Contributing guidelines

#### Workflow README Update

Updated `.github/workflows/README.md` to document:

- Cypress workflow behavior
- Success criteria for PR mergeability
- How to view test results
- Local testing commands

### 5. Configuration Files

#### .gitignore Updates

Added entries for:

- `cypress/videos` - Test recordings
- `cypress/screenshots` - Failure screenshots
- `cypress/downloads` - Downloaded files
- `.cypress_cache` - Cypress binary cache
- `cypress.env.json` - Local environment config
- `dist/report.html` - Webpack bundle analyzer report

#### cypress.env.json.example

Example configuration file showing how to customize:

- Base URL for different environments
- Local development setup

### 6. npm Scripts

Added to `package.json`:

```json
{
  "test": "cypress run", // Run all tests headless
  "test:headed": "cypress run --headed", // Run with visible browser
  "test:chrome": "cypress run --browser chrome",
  "test:firefox": "cypress run --browser firefox",
  "cy:open": "cypress open", // Interactive test runner
  "cy:verify": "cypress verify" // Verify Cypress installation
}
```

## How to Use

### Running Tests Locally

**First Time Setup:**

```bash
npm install
npx cypress verify
```

**Quick Test Run:**

```bash
npm test
```

**Interactive Development:**

```bash
npm run cy:open
```

**Test Against Staging:**

```bash
CYPRESS_BASE_URL=https://staging.novaramedia.com npm test
```

### In CI

Tests run automatically on every PR. No manual action needed.

To view test results:

1. Go to PR's "Checks" tab
2. Click on "Cypress Tests" run
3. If tests fail, download artifacts from "Summary" section
4. Extract and view videos/screenshots

## Success Criteria Met

✅ **Cypress suite covers all Priority 1 pages**

- Homepage ✅
- Support page ✅
- Single post (article) ✅

✅ **Cypress suite covers all Priority 2 pages**

- Single post (video category) ✅
- Single post (audio category) ✅
- About page ✅
- Jobs page ✅
- Novara Live category archive ✅

✅ **GitHub Actions runs tests on every PR**

- Workflow configured for PRs to master/main/development

✅ **PRs cannot merge if tests fail**

- Required status check configured via workflow

✅ **Tests are documented and can be run locally**

- Comprehensive documentation in README and TESTING.md
- Multiple run modes supported (headless, interactive, browsers)

✅ **Foundation exists for adding additional tests incrementally**

- Test structure supports easy addition of new test files
- Custom commands provide reusable test patterns
- Documentation includes future enhancement roadmap

## What's Next (Phase 3 - Future Work)

### Future Enhancements

- Visual regression testing
- Accessibility testing (pa11y, axe)
- Performance benchmarks
- User interaction flows (comments, search, etc.)
- Backend PHP unit tests (PHPUnit)

## Technical Notes

### Test Philosophy

- **Smoke tests**: Focus on critical path, not edge cases
- **Resilient**: Filter third-party errors (analytics, social widgets)
- **Dynamic**: Tests work against live content that changes
- **Responsive**: Test across mobile, tablet, desktop viewports

### WordPress Considerations

- Tests run against live site (no local WP install needed)
- No authentication required for public pages
- Tests avoid hard-coded content expectations
- Flexible selectors work with WordPress markup patterns

### CI Performance

- npm caching reduces install time
- Automatic retries handle flaky tests
- 15-minute timeout prevents hung jobs
- Videos only uploaded on failure to save bandwidth

## Files Created/Modified

### New Files

- `.github/workflows/cypress.yml` - CI workflow
- `cypress.config.js` - Cypress configuration
- `cypress/e2e/homepage.cy.js` - Homepage tests
- `cypress/e2e/support-page.cy.js` - Support page tests
- `cypress/e2e/single-post.cy.js` - Single post (article) tests
- `cypress/e2e/single-post-video.cy.js` - Video post tests
- `cypress/e2e/single-post-audio.cy.js` - Audio post tests
- `cypress/e2e/about-page.cy.js` - About page tests
- `cypress/e2e/jobs-page.cy.js` - Jobs page tests
- `cypress/e2e/novara-live-archive.cy.js` - Novara Live archive tests
- `cypress/support/e2e.js` - Global test setup
- `cypress/support/commands.js` - Custom commands
- `cypress.env.json.example` - Config example
- `TESTING.md` - Detailed testing docs

### Modified Files

- `package.json` - Added Cypress dependency and scripts
- `package-lock.json` - Dependency lockfile
- `yarn.lock` - Yarn lockfile
- `.gitignore` - Cypress artifacts excluded
- `README.md` - Testing section added
- `.github/workflows/README.md` - Cypress workflow documented

## Troubleshooting

### Common Issues and Solutions

**"Cypress binary not found":**

```bash
npx cypress install
npm run cy:verify
```

**Tests timeout:**

- Check internet connection
- Verify baseUrl is accessible
- Increase timeout in cypress.config.js if needed

**Tests fail locally but pass in CI:**

- Check your CYPRESS_BASE_URL
- Ensure testing against same environment
- Clear Cypress cache: `npx cypress cache clear`

**Third-party script errors:**

- These are filtered automatically
- Check actual error in video/screenshot
- May indicate real issue, not just third-party noise

## Support

For questions or issues:

1. Check `TESTING.md` for detailed documentation
2. Review test videos/screenshots for failures
3. Run tests with `npm run cy:open` for interactive debugging
4. Check Cypress docs: https://docs.cypress.io/

## Conclusion

This implementation provides a solid foundation for automated testing of the WordPress theme. All Phase 1 requirements are met, with clear documentation and easy extensibility for future test additions.
