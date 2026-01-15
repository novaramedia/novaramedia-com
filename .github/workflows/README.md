# GitHub Actions Workflows

## Cypress Tests

The `cypress.yml` workflow runs automated end-to-end tests for the WordPress theme using Cypress.

### How it works

1. **Trigger**: Runs on Pull Requests and pushes to `master`, `main`, or `development` branches
2. **Environment**: Ubuntu with Node.js 18 and Chrome browser
3. **Test Execution**: Runs all Cypress tests in headless mode
4. **Artifacts**: Uploads test videos and screenshots (especially on failure)

### Test Configuration

- **Base URL**: Tests run against production (`https://novaramedia.com`) by default
- **Override**: Set `CYPRESS_BASE_URL` repository secret to test against staging
- **Timeout**: 15-minute maximum per test run
- **Retries**: Failed tests automatically retry 2 times

### Success Criteria

For a PR to be mergeable:
- ✅ All Cypress tests must pass
- ✅ No critical console errors
- ✅ All priority pages load successfully

### Viewing Test Results

When tests fail:
1. Click on the failed GitHub Actions run
2. Go to "Summary" tab
3. Download "cypress-artifacts" or "cypress-results"
4. Videos show full test execution
5. Screenshots capture failure state

### Local Testing

Before pushing, run tests locally:
```bash
npm test              # Run all tests
npm run cy:open       # Interactive debugging
```

See the main [README.md](../../README.md#howto-testing) and [TESTING.md](../../TESTING.md) for detailed testing documentation.

## Release Notification to Slack

The `release-notification.yml` workflow automatically sends structured notifications to the public digital team Slack channel when a new version is released.

### How it works

1. **Trigger**: Activates when a Pull Request with title starting with "Version " is merged into the `master` or `main` branch
2. **Version Extraction**: Extracts the version number from `package.json` in the merged code
3. **Release Notes**: Parses `CHANGELOG.md` to extract the release notes for that specific version
4. **Slack Notification**: Sends a structured message with:
   - Release version and repository info
   - Link to the merged PR
   - Complete release notes from changelog
   - Commit SHA for deployment tracking

### Setup Requirements

To enable Slack notifications, you need to:

1. **Create a Slack Webhook URL**:
   - Go to your Slack workspace settings
   - Navigate to "Incoming Webhooks" 
   - Create a new webhook for the target channel
   - Copy the webhook URL

2. **Add the webhook as a GitHub secret**:
   - Go to repository Settings → Secrets and variables → Actions
   - Add a new repository secret named `SLACK_WEBHOOK_URL`
   - Paste the webhook URL as the value

### Message Format

The Slack notification includes:
- Header with release version
- Repository and PR links
- Full release notes from CHANGELOG.md
- Deployment commit information

### Troubleshooting

- Ensure PR titles start with "Version " to trigger the workflow
- Verify the version exists in CHANGELOG.md with format: `## [X.Y.Z] - DATE`
- Check that the `SLACK_WEBHOOK_URL` secret is properly configured