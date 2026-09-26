# GitHub Actions Workflows

## Playwright Tests

The `playwright.yml` workflow runs the end-to-end smoke tests for the WordPress theme using Playwright.

### How it works

1. **Trigger**: Runs on Pull Requests to `master`, `main`, or `development` branches, and on manual `workflow_dispatch`
2. **Deploy**: Deploys the PR commit to Kinsta staging via SSH + git
3. **Test**: Installs Chromium and runs all Playwright tests against staging
4. **Cleanup**: Resets staging back to the `development` branch
5. **Artifacts**: Uploads the HTML report and traces when tests fail

### Test Configuration

- **Base URL**: Tests run against Kinsta staging (set via `STAGING_URL` secret, passed as `PLAYWRIGHT_BASE_URL`)
- **Timeout**: 10-minute maximum per job
- **Retries**: Failed tests automatically retry 2 times, recording a trace on the first retry
- **Concurrency**: The `kinsta-staging` group serialises runs so only one workflow touches staging at a time
- **Fork PRs**: Skipped automatically (secrets not available)

### Success Criteria

For a PR to be mergeable:
- ✅ All Playwright tests must pass
- ✅ No theme-owned console errors
- ✅ All priority pages load successfully

### Viewing Test Results

When tests fail:
1. Click on the failed GitHub Actions run
2. Go to "Summary" tab
3. Download `playwright-artifacts-<run id>`
4. Unzip, then `npx playwright show-report playwright-report` for the HTML report
5. `npx playwright show-trace test-results/<test>/trace.zip` replays a failed test step by step

### Local Testing

Before pushing, run tests against your DevKinsta site:
```bash
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm test         # Run all tests
PLAYWRIGHT_BASE_URL=https://novaramediacom.local npm run test:ui  # Interactive debugging
```

See [docs/testing/testing.md](../../docs/testing/testing.md) for the full testing guide.

## Release Notification to Slack

The `release-notification.yml` workflow automatically sends structured notifications to the public digital team Slack channel when a new version is released.

### How it works

1. **Trigger**: Activates when a Pull Request with title starting with "Release: " (as created by `scripts/release.sh`) is merged into the `master` or `main` branch
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

- Ensure PR titles start with "Release: " to trigger the workflow
- Verify the version exists in CHANGELOG.md with format: `## [X.Y.Z] - DATE`
- Check that the `SLACK_WEBHOOK_URL` secret is properly configured
