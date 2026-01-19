# no future utopia now

novaramedia.com

## We are Dev[0]

### Howto: Testing

This project uses [Cypress](https://www.cypress.io/) for end-to-end testing of the WordPress theme.

#### Running Tests Locally

1. **Install dependencies** (if not already done):

   ```bash
   npm install
   ```

2. **Run tests in headless mode** (same as CI):

   ```bash
   npm test
   ```

3. **Run tests with interactive UI**:

   ```bash
   npm run cy:open
   ```

   This opens the Cypress Test Runner where you can:
   - See tests run in a real browser
   - Debug failing tests
   - Time-travel through test steps

4. **Run tests in specific browsers**:
   ```bash
   npm run test:chrome    # Run in Chrome
   npm run test:firefox   # Run in Firefox
   npm run test:headed    # Run with browser visible
   ```

#### Test Configuration

- Tests run against production (`https://novaramedia.com`) by default
- To test against a different URL, set the `CYPRESS_BASE_URL` environment variable:
  ```bash
  CYPRESS_BASE_URL=https://staging.novaramedia.com npm test
  ```

#### CI/CD Integration

- Tests run automatically on all Pull Requests via GitHub Actions
- PRs **cannot be merged** if tests fail
- Test results and videos are available as artifacts in the GitHub Actions run
- See `.github/workflows/cypress.yml` for CI configuration

#### Test Coverage

Current test suites cover:

**Priority 1 (Core Tests):**

- **Homepage** - Main landing page loads with key elements
- **Support Page** - Donation/support page functionality
- **Single Post (Article)** - Individual article display and navigation

**Priority 2 (Secondary Views):**

- **Single Post (Video)** - Video category posts with media player
- **Single Post (Audio)** - Audio/podcast posts with audio player
- **About Page** - Organizational information and content
- **Jobs Page** - Job listings and career information
- **Novara Live Archive** - Category archive with post listings

Each test validates:

- Page loads without errors
- Critical DOM elements are present
- Responsive behavior across mobile/tablet/desktop
- No broken images in main content
- No console errors (excluding third-party scripts)

#### Troubleshooting

**Cypress binary not found:**

```bash
npx cypress install
npm run cy:verify
```

**Tests timeout:**

- Check your internet connection
- Verify the base URL is accessible
- Increase timeout in `cypress.config.js` if needed

**Test failures:**

- Review screenshots in `cypress/screenshots/`
- Watch videos in `cypress/videos/`
- Run tests with `npm run cy:open` for interactive debugging

For more information, see the [Cypress documentation](https://docs.cypress.io/).

### Howto: release

- Pull `development`
- `yarn release`
- Don't commit, tag or push in release-it process
- After post release-it scripts are run commit in format `Build: x.x.x`
- Create PR to master branch in format `Version x.x.x` with changelog entries as description

### Semver

- Patches for bugfixes, copy updates, minor changes
- Minor version for any significant new functionality
- Major for breaking changes and significant design system iterations
