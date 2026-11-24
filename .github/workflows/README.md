# GitHub Actions Workflows

## PHP Unit Tests

The `php-unit.yml` workflow runs the PHPUnit suite in `tests/unit/` on pull requests and pushes to `development` that touch PHP files or the suite. It needs no secrets and no WordPress install: Brain Monkey stubs WordPress functions. Test tooling is installed into `tests/unit/vendor/` only; the theme's committed `vendor/` is never touched. That split is a stopgap for the git-pull deploy: once production deploys from CI with a `composer install --no-dev` step (`docs/plans/production-ci-deploy.md`), the tooling moves to the root `composer.json`. Runs on PHP 8.2 (production) and 8.4 (local DevKinsta, next PHP cycle). See `docs/testing/testing.md`.

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
- **Dependabot PRs**: Skipped. They can't read the SSH secrets, and a lockfile-only bump doesn't change what staging serves. `dependabot-build.yml` checks them instead

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

## Dependabot Build Check

The `dependabot-build.yml` workflow runs on Dependabot PRs in place of the staging Playwright run. It needs no secrets and doesn't touch staging.

### How it works

1. Installs and builds the PR head (`npm ci && npm run build`), so a broken lockfile or build fails the check
2. Confirms the Playwright CLI still runs, so a broken `@playwright/test` bump shows up before merge
3. Installs and builds the base commit the same way, then compares the hashes of the built files in `dist/` and `blocks/`
4. Fails if the built output differs. A lockfile bump that changes the bundle (usually a runtime dependency) needs `dist/` rebuilt and committed by hand, and a look before merging

Comparing two fresh builds rather than the committed `dist/` keeps the check independent of whether the committed bundle is current.

## Release Notification to Slack

The `release-notification.yml` workflow posts to Slack when a release PR is merged into `master`. It calls the shared [`slack-post.yml`](https://github.com/novaramedia/nm-shared-workflows) workflow, which follows the Slack Messages rules in [nm-agents-shared/conventions.md](https://github.com/novaramedia/nm-agents-shared/blob/main/conventions.md#slack-messages).

It reports the **merge**, not a deploy — production is still updated manually afterwards (see [docs/releases.md](../../docs/releases.md#deploying)). Once production deploys run from CI, the notification should fire after a successful deploy and move to the public digital team channel — see [docs/plans/production-ci-deploy.md](../../docs/plans/production-ci-deploy.md).

### How it works

1. **Trigger**: a merged PR into `master` whose title starts with "Release: " (as created by `scripts/release.sh`)
2. **Version**: read from `package.json` in the merged code
3. **Post**: the shared workflow pulls the `## [X.Y.Z]` section from `CHANGELOG.md` and posts it with:
   - a header with the version
   - the release notes, rendered as markdown (headings, lists, links)
   - buttons: View site, Release PR, Commit
   - a footer linking the workflow run

### Setup

- **Secret `SLACK_BOT_TOKEN`**: the Novara Slack bot token (`xoxb-…`, `chat:write`). Settings → Secrets and variables → Actions → Secrets.
- **Variable `SLACK_RELEASE_CHANNEL`**: channel name or ID. Settings → Secrets and variables → Actions → Variables. Changing channel needs no code change.
- **Invite the bot** to that channel, or Slack rejects the post with `not_in_channel`.

### Troubleshooting

- PR titles must start with "Release: " to trigger the workflow
- The version must exist in CHANGELOG.md as `## [X.Y.Z] - DATE`; if not, the post goes out without notes and the run shows a warning
- A failed Slack post shows as a warning on the run (with Slack's error, e.g. `not_in_channel`, `invalid_auth`) rather than a failure

