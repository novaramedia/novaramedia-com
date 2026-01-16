# Testing Implementation Plan: PR-to-Staging Pipeline

## Executive Summary

This document outlines the plan to implement automated Cypress testing against a staging environment, triggered when Pull Requests are opened against the `master` or `development` branches.

## Current State

### What We Have

- ✅ Cypress testing framework configured with Priority 1 tests
- ✅ GitHub Actions workflow for running tests
- ✅ Kinsta hosting with a permanent staging environment
- ✅ **WP Pusher** plugin for syncing theme from GitHub branches
- ✅ Public staging URL (no authentication required)
- ✅ Kinsta API access available

### The Problem

- Tests currently run against **production** by default
- No automated deployment to staging on PR creation
- Can't test breaking changes before they hit production
- Manual process to sync theme to staging via WP Pusher

## Proposed Architecture

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  Developer      │     │  GitHub Actions  │     │  Kinsta         │
│  Opens PR       │────▶│  Workflow        │────▶│  Staging Site   │
└─────────────────┘     └──────────────────┘     └─────────────────┘
                                │                        │
                                │                        │
                                ▼                        ▼
                        ┌──────────────────┐     ┌─────────────────┐
                        │  Wait for        │     │  Theme deployed │
                        │  deployment      │────▶│  to staging     │
                        └──────────────────┘     └─────────────────┘
                                │
                                ▼
                        ┌──────────────────┐
                        │  Run Cypress     │
                        │  tests against   │
                        │  staging URL     │
                        └──────────────────┘
                                │
                                ▼
                        ┌──────────────────┐
                        │  Report results  │
                        │  on PR           │
                        └──────────────────┘
```

## Implementation Options

### Option A: WP Pusher Webhook (Recommended)

Use WP Pusher's built-in webhook to trigger theme sync from the PR branch to staging.

**How WP Pusher Webhooks Work:**

1. WP Pusher provides a unique push-to-deploy URL for each theme
2. When the webhook is called, WP Pusher pulls the latest code from the configured branch
3. The webhook can include a `branch` parameter to pull from a specific branch

**Pros:**

- Uses existing deployment mechanism (WP Pusher already configured)
- Simple webhook call from GitHub Actions
- No complex API integration needed
- Already proven to work for your setup

**Cons:**

- Need to expose/configure WP Pusher webhook
- Less visibility into deployment status (no detailed progress)
- Need to reset branch after tests complete

**Required Setup:**

1. Get WP Pusher webhook URL from staging site (WP Admin → WP Pusher → Themes)
2. Store webhook URL in GitHub Secrets
3. Add workflow step to call webhook with PR branch name
4. Add wait step for deployment to propagate
5. Clear Kinsta cache via API (optional but recommended)

**Implementation:**

```yaml
- name: Deploy PR branch to staging via WP Pusher
  run: |
    # Trigger WP Pusher to pull from PR branch
    curl -X POST "${{ secrets.WP_PUSHER_WEBHOOK_URL }}" \
      -d "branch=${{ github.head_ref }}"

    # Wait for deployment to propagate
    sleep 30

    # Clear Kinsta cache (optional)
    curl -X POST "https://api.kinsta.com/v2/sites/${{ secrets.KINSTA_SITE_ID }}/environments/${{ secrets.KINSTA_STAGING_ENV_ID }}/clear-cache" \
      -H "Authorization: Bearer ${{ secrets.KINSTA_API_KEY }}"
```

---

### Option B: Kinsta API Direct Integration

Use Kinsta's REST API to trigger theme deployment to staging directly from GitHub Actions.

**Pros:**

- Direct control over deployment timing
- Can verify deployment completed before running tests
- No dependency on WordPress plugin for CI/CD

**Cons:**

- Requires Kinsta API credentials in GitHub Secrets
- Single staging environment (queue contention if multiple PRs)

**Implementation:**

1. Create Kinsta API credentials
2. Store credentials in GitHub Secrets
3. Add deployment job to GitHub workflow
4. Add health check/wait step
5. Run Cypress against staging URL

### Option B: Webhook to WordPress Plugin

Trigger the existing WordPress plugin to sync from the PR branch.

**Pros:**

- Uses existing deployment mechanism
- Less configuration needed

**Cons:**

- Dependent on plugin availability/reliability
- Less control over timing and verification
- Need to expose webhook endpoint

### Option C: Git-based Deploy via SSH/SFTP

Use GitHub Actions to deploy theme files directly to staging via SSH/SFTP.

**Pros:**

- Full control over what gets deployed
- Fast deployment (only theme files)

**Cons:**

- Need to manage SSH keys
- Bypass existing deployment flow
- May cause inconsistencies with production deployment

---

## Recommended Approach: Option A (Kinsta API)

### Prerequisites

1. **Kinsta API Access**
   - Log into MyKinsta dashboard
   - Create API credentials with appropriate permissions
   - Note the Site ID and Environment ID for staging

2. **GitHub Secrets Required**
   - `KINSTA_API_KEY` - API authentication token
   - `KINSTA_SITE_ID` - The site identifier
   - `KINSTA_STAGING_ENV_ID` - Staging environment ID
   - `STAGING_URL` - Full URL of staging site (e.g., `https://staging-novaramedia.kinsta.cloud`)

### Implementation Steps

#### Step 1: Update GitHub Workflow

Create a new workflow or modify `.github/workflows/cypress.yml`:

```yaml
name: PR Tests on Staging

on:
  pull_request:
    branches:
      - master
      - main
      - development

jobs:
  deploy-to-staging:
    runs-on: ubuntu-latest
    outputs:
      deployment_status: ${{ steps.deploy.outputs.status }}
    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Deploy theme to Kinsta staging
        id: deploy
        run: |
          # Trigger deployment via Kinsta API
          # Implementation details in Step 2
        env:
          KINSTA_API_KEY: ${{ secrets.KINSTA_API_KEY }}
          KINSTA_SITE_ID: ${{ secrets.KINSTA_SITE_ID }}
          KINSTA_STAGING_ENV_ID: ${{ secrets.KINSTA_STAGING_ENV_ID }}

      - name: Wait for deployment
        run: |
          # Poll deployment status until complete
          # Or use webhook callback

  cypress-tests:
    needs: deploy-to-staging
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '18'
          cache: 'npm'

      - name: Install dependencies
        run: npm ci

      - name: Run Cypress tests
        uses: cypress-io/github-action@v6
        with:
          browser: chrome
        env:
          CYPRESS_BASE_URL: ${{ secrets.STAGING_URL }}

      - name: Upload artifacts on failure
        uses: actions/upload-artifact@v4
        if: failure()
        with:
          name: cypress-artifacts
          path: |
            cypress/videos
            cypress/screenshots
```

#### Step 2: Kinsta API Integration Script

Create a deployment script that:

1. Pushes theme files to staging via Kinsta API
2. Waits for deployment to complete
3. Verifies the site is accessible

```bash
# scripts/deploy-to-kinsta-staging.sh
#!/bin/bash

# This would use Kinsta's API to:
# 1. Trigger a git pull on staging environment
# 2. Or use SFTP to upload changed theme files
# 3. Clear cache
# 4. Verify deployment
```

#### Step 3: Health Check Before Tests

Add a pre-test verification step:

```yaml
- name: Verify staging is accessible
  run: |
    for i in {1..30}; do
      if curl -s -o /dev/null -w "%{http_code}" "${{ secrets.STAGING_URL }}" | grep -q "200"; then
        echo "Staging site is accessible"
        exit 0
      fi
      echo "Waiting for staging site... attempt $i"
      sleep 10
    done
    echo "Staging site not accessible after 5 minutes"
    exit 1
```

---

## Alternative: Kinsta's Preview Environments

Kinsta has a feature for **Preview Environments** (ephemeral staging per PR). This would be ideal but requires:

1. Checking if feature is available on your plan
2. API support for creating preview environments
3. More complex teardown logic

**Recommendation:** Start with Option A using the permanent staging environment. Migrate to preview environments later if needed.

---

## Handling Staging Contention

With a single staging environment, multiple simultaneous PRs could conflict. Options:

### Option 1: Queue-based (Simple)

- Use GitHub Actions concurrency groups
- Only one PR can deploy/test at a time
- Other PRs wait in queue

```yaml
concurrency:
  group: staging-tests
  cancel-in-progress: false
```

### Option 2: Branch-aware deployment

- Staging always reflects the PR branch being tested
- Reset staging to development after tests complete

### Option 3: Scheduled batching

- Batch test runs during off-hours
- Reduces contention for active development

**Recommendation:** Start with Option 1 (queue-based). It's simple and prevents race conditions.

---

## Timeline & Phases

### Phase 1: Foundation (Week 1)

- [ ] Create Kinsta API credentials
- [ ] Add GitHub Secrets
- [ ] Create deployment script
- [ ] Update workflow for staging deployment

### Phase 2: Integration (Week 2)

- [ ] Add health check step
- [ ] Configure concurrency groups
- [ ] Test end-to-end pipeline
- [ ] Update documentation

### Phase 3: Refinement (Week 3)

- [ ] Add PR comment with test results
- [ ] Implement staging reset after tests
- [ ] Monitor and tune timeouts
- [ ] Consider preview environments

---

## Required GitHub Secrets

Configure these in: **Repository Settings → Secrets and Variables → Actions**

| Secret Name             | Required | Description                       | Where to Find                                                            |
| ----------------------- | -------- | --------------------------------- | ------------------------------------------------------------------------ |
| `WP_PUSHER_WEBHOOK_URL` | **Yes**  | WP Pusher push-to-deploy URL      | Staging WP Admin → WP Pusher → Themes → Click theme → Push-to-deploy URL |
| `STAGING_URL`           | **Yes**  | Full staging site URL             | e.g., `https://staging-novaramedia.kinsta.cloud`                         |
| `KINSTA_API_KEY`        | Optional | Kinsta API key for cache clearing | MyKinsta → API Keys → Create new                                         |
| `KINSTA_SITE_ID`        | Optional | Site ID for API calls             | MyKinsta → Sites → Site Info                                             |
| `KINSTA_STAGING_ENV_ID` | Optional | Staging environment ID            | MyKinsta → Sites → Environments                                          |

### How to Get WP Pusher Webhook URL

1. Log into staging site's WordPress admin
2. Navigate to **WP Pusher → Themes**
3. Click on the theme (novaramedia-com)
4. Look for "Push-to-deploy URL" - it looks like:
   ```
   https://staging-site.kinsta.cloud/?wppusher-hook&token=abc123xyz
   ```
5. Copy this full URL (including the token)
6. Add it as `WP_PUSHER_WEBHOOK_URL` secret in GitHub

### How to Get Kinsta API Credentials (Optional but Recommended)

1. Log into [MyKinsta](https://my.kinsta.com/)
2. Go to **Company → API Keys**
3. Click **Create API Key**
4. Give it a name like "GitHub Actions Staging"
5. Copy the key immediately (shown only once)
6. For Site ID: **Sites → Your Site → Info → Site Details**
7. For Environment ID: **Sites → Your Site → Environments → Staging → ID shown in URL**

---

## Security Considerations

1. **API Credentials**
   - Store in GitHub Secrets (encrypted)
   - Use minimal required permissions
   - Rotate periodically

2. **Staging Access**
   - Consider password protecting staging
   - Exclude from search engines
   - Don't include in tests if auth is added

3. **Sensitive Data**
   - Don't commit credentials to repo
   - Use environment variables for URLs
   - Keep `cypress.env.json` in `.gitignore`

---

## Questions to Resolve

1. **What WordPress plugin are you using for GitHub sync?**
   - Need to understand if it exposes webhooks or API
   - May be simpler to trigger this vs direct Kinsta API

2. **Is the staging URL password protected?**
   - If yes, Cypress will need credentials
   - May need to adjust test configuration

3. **How quickly does the current plugin sync changes?**
   - Need to understand typical deployment time
   - Affects timeout configuration

4. **Do you have Kinsta API access currently?**
   - Need to confirm API availability on your plan
   - May need to contact Kinsta support

---

## Success Criteria

- [ ] PRs to master/development automatically deploy to staging
- [ ] Cypress tests run against staging URL
- [ ] Tests must pass for PR to be mergeable
- [ ] Test results visible in PR checks
- [ ] Artifacts (videos/screenshots) available for debugging
- [ ] Pipeline completes in < 10 minutes total

---

## Next Steps

1. **Immediate:** Answer questions above about current plugin and Kinsta access
2. **Then:** Gather Kinsta API credentials and site IDs
3. **Then:** Implement Phase 1 workflow changes
4. **Finally:** Test and iterate on the pipeline
