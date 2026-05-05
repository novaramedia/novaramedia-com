# GitHub Actions Workflows

## Cypress Tests

The `cypress.yml` workflow runs automated end-to-end tests for the WordPress theme using Cypress.

### How it works

1. **Trigger**: Runs on Pull Requests to `development` branch, and on manual `workflow_dispatch`
2. **Deploy**: Deploys the PR commit to Kinsta staging via SSH + git
3. **Test**: Runs all Cypress tests in headless Chrome against staging
4. **Cleanup**: Resets staging back to the `development` branch
5. **Artifacts**: Uploads test videos and screenshots (especially on failure)

### Test Configuration

- **Base URL**: Tests run against Kinsta staging (set via `STAGING_URL` secret)
- **Timeout**: 10-minute maximum per job
- **Retries**: Failed tests automatically retry 2 times
- **Fork PRs**: Skipped automatically (secrets not available)

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

## Deploy Production

The `deploy-production.yml` workflow deploys to the Kinsta production server when a GitHub Release is published.

### How it works

1. **Trigger**: Activates when a GitHub Release is published (created by `scripts/release.sh`)
2. **Deploy**: SSHes into Kinsta production, checks out the release tag
3. **Activate**: Runs `wp theme activate` via SSH
4. **Cache clear**: Clears Kinsta cache via API (optional — skipped if secrets not configured)
5. **Verify**: Polls the production URL to confirm the site is accessible
6. **Slack**: Posts success or failure notification to the team channel

### Setup Requirements

Required secrets: `KINSTA_SSH_KEY`, `KINSTA_PROD_SSH_HOST`, `KINSTA_PROD_SSH_PORT`, `KINSTA_PROD_SSH_USER`, `PROD_URL`, `SLACK_WEBHOOK_URL`

Optional secrets (cache clearing): `KINSTA_API_KEY`, `KINSTA_SITE_ID`, `KINSTA_PROD_ENV_ID`