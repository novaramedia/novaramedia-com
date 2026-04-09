# Release-Based Deploy Flow Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

> **Implementation note (post-review):** During Copilot review on PR #461, the tagging responsibility was moved out of release-it and into `scripts/release.sh`. Release-it now *only* handles version bump, changelog, and build (`git.commit`/`git.tag`/`git.push` all `false`). The script owns `git tag`, `git push --tags`, and `gh release create`. Task 3 and Task 4 below have been updated to reflect the final implementation — the release-it config block is intentionally *not* enabling `git.tag` or `github.release`.

**Goal:** Replace the master-branch deploy flow with GitHub Release-triggered auto-deploy to production, removing the master branch entirely.

**Architecture:** All work merges to `development`. When ready to ship, `release.sh` bumps version, tags, and creates a GitHub Release. A new workflow `deploy-production.yml` triggers on `release: published`, SSHes into the Kinsta production server, checks out the release tag, and clears cache. The existing Slack notification moves to trigger on releases instead of PR merges.

**Tech Stack:** GitHub Actions, SSH (Kinsta), release-it, GitHub CLI

---

## Current State

- **Git flow:** development → PR to master → manual pull on production server
- **Release script:** `scripts/release.sh` bumps version via release-it, commits, pushes development, optionally creates PR to master
- **Slack notification:** triggers on PR merge to master with title starting "Version"
- **Staging deploy:** `deploy-staging.yml` SSHes into Kinsta staging, does `git fetch && git checkout <ref>`
- **Cypress tests:** trigger on PRs to master
- **Secrets available:** `KINSTA_SSH_KEY`, `KINSTA_SSH_USER`, `KINSTA_SFTP_HOST`, `KINSTA_SSH_PORT` (staging — production secrets TBD)
- **GitHub default branch:** already `development`
- **Master branch protection:** minimal (PR reviews required but 0 approvers, no enforcement)

## What Needs to Happen

1. Add production SSH secrets to GitHub
2. Create `deploy-production.yml` (triggered on GitHub Release)
3. Update `release.sh` to tag and create a GitHub Release instead of PRing to master
4. Keep release-it git tagging disabled — tagging owned by `release.sh` (see implementation note above)
5. Update Slack notification to trigger on releases
6. Update Cypress workflow to trigger on PRs to development
7. Remove master branch and its protections
8. Update documentation (CLAUDE.md, copilot-instructions)

---

### Task 1: Add Production SSH Secrets

**Files:** None (GitHub settings only)

These steps are manual — they cannot be done via CLI without the actual secret values.

- [ ] **Step 1: Get production SSH details from Kinsta**

In MyKinsta → Sites → novaramedia.com → Info → SFTP/SSH, note the production environment's:
- SSH host
- SSH port
- SSH user

The existing `KINSTA_SSH_KEY` may already work for production (same site, different environment). Verify by SSHing manually.

- [ ] **Step 2: Add production secrets to GitHub**

```bash
# If production uses the same SSH key as staging (likely on Kinsta):
gh secret set KINSTA_PROD_SSH_HOST --repo novaramedia/novaramedia-com
gh secret set KINSTA_PROD_SSH_PORT --repo novaramedia/novaramedia-com
gh secret set KINSTA_PROD_SSH_USER --repo novaramedia/novaramedia-com
gh secret set KINSTA_PROD_ENV_ID --repo novaramedia/novaramedia-com
```

If production uses a different SSH key, also set `KINSTA_PROD_SSH_KEY`.

- [ ] **Step 3: Add production URL variable**

```bash
gh secret set PROD_URL --repo novaramedia/novaramedia-com
# Value: https://novaramedia.com
```

---

### Task 2: Create Production Deploy Workflow

**Files:**
- Create: `.github/workflows/deploy-production.yml`

This is modelled on the donate repo's `deploy-prod.yml` pattern (trigger on release) and the existing `deploy-staging.yml` SSH steps (Kinsta-specific).

- [ ] **Step 1: Create the workflow file**

```yaml
# Deploy to Production on GitHub Release
#
# Triggered when a GitHub Release is published.
# SSHes into the Kinsta production server, checks out the release tag,
# clears cache, and verifies the site is up.
#
# Required secrets: KINSTA_SSH_KEY, KINSTA_PROD_SSH_HOST, KINSTA_PROD_SSH_PORT,
# KINSTA_PROD_SSH_USER, KINSTA_API_KEY, KINSTA_SITE_ID, KINSTA_PROD_ENV_ID, PROD_URL

name: Deploy Production

on:
  release:
    types: [published]

permissions:
  contents: read

concurrency:
  group: kinsta-production
  cancel-in-progress: false

jobs:
  deploy:
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - name: Setup SSH
        run: |
          mkdir -p ~/.ssh
          echo "$KINSTA_SSH_KEY" > ~/.ssh/kinsta_key
          chmod 600 ~/.ssh/kinsta_key
          ssh-keyscan -p "$KINSTA_SSH_PORT" "$KINSTA_SSH_HOST" >> ~/.ssh/known_hosts 2>/dev/null
        env:
          KINSTA_SSH_KEY: ${{ secrets.KINSTA_SSH_KEY }}
          KINSTA_SSH_PORT: ${{ secrets.KINSTA_PROD_SSH_PORT }}
          KINSTA_SSH_HOST: ${{ secrets.KINSTA_PROD_SSH_HOST }}

      - name: Deploy release tag to production
        run: |
          TAG="${{ github.event.release.tag_name }}"
          echo "Deploying $TAG to production..."

          ssh -i ~/.ssh/kinsta_key -p "$KINSTA_SSH_PORT" \
            "${KINSTA_SSH_USER}@${KINSTA_SSH_HOST}" \
            "cd public/wp-content/themes/novaramedia-com && \
             git fetch origin --tags && \
             git checkout $TAG"

          echo "Deploy complete: $TAG"
        env:
          KINSTA_SSH_PORT: ${{ secrets.KINSTA_PROD_SSH_PORT }}
          KINSTA_SSH_USER: ${{ secrets.KINSTA_PROD_SSH_USER }}
          KINSTA_SSH_HOST: ${{ secrets.KINSTA_PROD_SSH_HOST }}

      - name: Activate theme
        run: |
          ssh -i ~/.ssh/kinsta_key -p "$KINSTA_SSH_PORT" \
            "${KINSTA_SSH_USER}@${KINSTA_SSH_HOST}" \
            "cd public && wp theme activate novaramedia-com"
        env:
          KINSTA_SSH_PORT: ${{ secrets.KINSTA_PROD_SSH_PORT }}
          KINSTA_SSH_USER: ${{ secrets.KINSTA_PROD_SSH_USER }}
          KINSTA_SSH_HOST: ${{ secrets.KINSTA_PROD_SSH_HOST }}

      - name: Clear Kinsta cache
        continue-on-error: true
        run: |
          if [ -z "$KINSTA_API_KEY" ] || [ -z "$KINSTA_SITE_ID" ] || [ -z "$KINSTA_PROD_ENV_ID" ]; then
            echo "Skipping cache clear - Kinsta API secrets not configured"
            exit 0
          fi

          echo "Clearing Kinsta production cache..."
          response=$(curl -s -w "\n%{http_code}" -X POST \
            "https://api.kinsta.com/v2/sites/${KINSTA_SITE_ID}/environments/${KINSTA_PROD_ENV_ID}/clear-cache" \
            -H "Authorization: Bearer ${KINSTA_API_KEY}" \
            -H "Content-Type: application/json")

          http_code=$(echo "$response" | tail -n1)
          body=$(echo "$response" | sed '$d')

          echo "Cache clear HTTP status: $http_code"
          if [ "$http_code" -lt 200 ] || [ "$http_code" -ge 300 ]; then
            echo "Cache clear response body:"
            echo "$body"
          fi
        env:
          KINSTA_API_KEY: ${{ secrets.KINSTA_API_KEY }}
          KINSTA_SITE_ID: ${{ secrets.KINSTA_SITE_ID }}
          KINSTA_PROD_ENV_ID: ${{ secrets.KINSTA_PROD_ENV_ID }}

      - name: Verify production is accessible
        run: |
          echo "Checking production site..."
          sleep 5
          http_code=$(curl -L -s -o /dev/null -w "%{http_code}" "$PROD_URL")

          if [ "$http_code" -ge 200 ] && [ "$http_code" -lt 400 ]; then
            echo "Production is live (HTTP $http_code)"
          else
            echo "WARNING: production returned HTTP $http_code"
            exit 1
          fi
        env:
          PROD_URL: ${{ secrets.PROD_URL }}

      - name: Cleanup SSH keys
        if: always()
        run: rm -f ~/.ssh/kinsta_key

  notify-slack-success:
    needs: deploy
    if: success()
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Send Slack notification
        uses: slackapi/slack-github-action@37ebaef184d7626c5f204ab8d3baff4262dd30f0 # v1.27.0
        with:
          payload: >
            {
              "text": "*Deployed: novaramedia.com ${{ github.event.release.tag_name }}*\n\n${{ github.event.release.body }}\n\n*Release:* ${{ github.event.release.html_url }}"
            }
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK_URL }}

  notify-slack-failure:
    needs: deploy
    if: failure()
    runs-on: ubuntu-latest
    steps:
      - name: Send Slack failure notification
        uses: slackapi/slack-github-action@37ebaef184d7626c5f204ab8d3baff4262dd30f0 # v1.27.0
        with:
          payload: >
            {
              "text": "*FAILED: novaramedia.com ${{ github.event.release.tag_name }} deploy failed*\n\n@patrick check the workflow run: ${{ github.server_url }}/${{ github.repository }}/actions/runs/${{ github.run_id }}"
            }
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK_URL }}
```

- [ ] **Step 2: Commit**

```bash
git add .github/workflows/deploy-production.yml
git commit -m "Add production deploy workflow triggered by GitHub Release"
```

---

### Task 3: Leave release-it Config as Version-Bump-Only

**Files:**
- `package.json:84-102` (release-it config section) — **no changes**

> **Final implementation note:** This task originally proposed enabling `git.tag: true` and `github.release: true` in release-it so it would create the tag and GitHub Release. During Copilot review, this was reverted — `scripts/release.sh` now owns tagging and release creation (see Task 4). Release-it stays version-bump-only, which keeps a single source of truth for the tag/release step.

The release-it config remains:

```json
"release-it": {
  "git": {
    "commit": false,
    "tag": false,
    "push": false
  },
  "npm": {
    "publish": false
  },
  "plugins": {
    "@release-it/keep-a-changelog": {
      "filename": "CHANGELOG.md"
    }
  },
  "hooks": {
    "before:release": "npm run build",
    "after:release": "sed -i '' 's/${latestVersion}/${version}/' style.css"
  }
}
```

Rationale:
- Release-it handles: version bump, changelog update, build, style.css bump
- `release.sh` handles: commit, tag, push, GitHub Release creation
- Single source of truth for tagging avoids duplicate-tag errors if release-it and the script both try to create the same tag

---

### Task 4: Update release.sh

**Files:**
- Modify: `scripts/release.sh`

The script currently creates a "Build: x.x.x" commit, pushes development, and optionally creates a PR to master. The new flow:
1. Same preflight checks and build
2. Same commit and push to development
3. Create and push the `v${version}` tag
4. Create the GitHub Release via `gh release create` — the `release: published` event triggers the production deploy workflow

> **Final implementation note:** This task originally assumed release-it would create the tag and GitHub Release (see Task 3). During review, responsibility shifted entirely to `release.sh`. The current script creates the tag itself, pushes it, and runs `gh release create` with `--notes-file` pointing at a temp file containing the extracted changelog entry. It also guards against a pre-existing tag (local or remote) to make the flow retry-safe after a partial failure. See `scripts/release.sh` for the canonical implementation — a full copy is intentionally not embedded here to avoid drift.

**Script responsibilities (final):**
- Preflight: branch check, clean working tree, pull latest
- Run `release-it --ci` (bumps version, updates changelog, runs build)
- Commit all changes as `Build: x.x.x`
- Guard against existing tag, then `git tag` + `git push` both development and the tag
- Extract the changelog entry for this version into a temp file
- `gh release create` with `--notes-file` (triggers production deploy via the release workflow)

---

### Task 5: Update Cypress Workflow Trigger

**Files:**
- Modify: `.github/workflows/cypress.yml` (line 28-30)

Currently triggers on PRs to master. With no master branch, this needs to trigger on PRs to development.

- [ ] **Step 1: Read the current trigger section**

```bash
head -35 .github/workflows/cypress.yml
```

- [ ] **Step 2: Update the trigger branch**

Change:
```yaml
on:
  pull_request:
    branches:
      - master
```

To:
```yaml
on:
  pull_request:
    branches:
      - development
```

- [ ] **Step 3: Commit**

```bash
git add .github/workflows/cypress.yml
git commit -m "Run Cypress tests on PRs to development"
```

---

### Task 6: Remove Old Slack Notification Workflow

**Files:**
- Delete: `.github/workflows/release-notification.yml`

Slack notifications are now handled by the `deploy-production.yml` workflow (Task 2), so this standalone workflow is redundant.

- [ ] **Step 1: Delete the file**

```bash
git rm .github/workflows/release-notification.yml
```

- [ ] **Step 2: Commit**

```bash
git commit -m "Remove standalone release notification workflow (now in deploy-production)"
```

---

### Task 7: Update Documentation

**Files:**
- Modify: `CLAUDE.md`
- Modify: `.github/copilot-instructions.md` (check for master branch references)

- [ ] **Step 1: Read copilot-instructions for master references**

```bash
grep -n 'master' .github/copilot-instructions.md
```

- [ ] **Step 2: Update CLAUDE.md**

Replace references to the master branch flow:

Change:
```markdown
- **PR Target:** development (then merge to master for releases)
```

To:
```markdown
- **PR Target:** development (releases are tagged and auto-deployed via GitHub Release)
```

- [ ] **Step 3: Update copilot-instructions.md**

Replace any references to "merge to master" or "PR to master" with the new flow: releases are tagged from development and auto-deploy.

- [ ] **Step 4: Commit**

```bash
git add CLAUDE.md .github/copilot-instructions.md
git commit -m "Update docs: remove master branch references, document release-deploy flow"
```

---

### Task 8: Remove Master Branch

**Files:** None (GitHub settings only)

This is the final step — do it after everything above is merged and tested.

- [ ] **Step 1: Verify default branch is development**

```bash
gh api repos/novaramedia/novaramedia-com --jq '.default_branch'
# Expected: development
```

- [ ] **Step 2: Remove branch protection from master**

```bash
gh api -X DELETE repos/novaramedia/novaramedia-com/branches/master/protection
```

- [ ] **Step 3: Delete master branch on remote**

```bash
git push origin --delete master
```

- [ ] **Step 4: Delete local master branch**

```bash
git branch -D master 2>/dev/null || true
```

---

## Post-Implementation: The New Release Flow

### Shipping a release
```bash
./scripts/release.sh patch    # or minor, major
```
This will: bump version → build → commit → push development → push tag → create GitHub Release → auto-deploy to production → Slack notification.

### Hotfix (like the survey URL fix)
```bash
# Fix on development
git add . && git commit -m "Fix survey URL"
# Ship immediately
./scripts/release.sh patch
```
Total time from fix to production: ~2 minutes.

### Rollback
```bash
# Re-deploy a previous tag via the staging deploy workflow (adapted for prod)
# Or: create a revert commit on development and release a new patch
```

---

## Important Note: release-it and Git Tags

The release-it `github.release` option requires a `GITHUB_TOKEN` with repo access. When running locally, release-it will use the `gh` CLI's auth token if available. Verify this works by checking:

```bash
gh auth status
```

If release-it can't create the GitHub Release, you can fall back to creating it manually after pushing the tag:

```bash
gh release create v4.5.3 --title "v4.5.3" --notes-file <(sed -n '/^## \[4.5.3\]/,/^## \[/p' CHANGELOG.md | sed '$d')
```
