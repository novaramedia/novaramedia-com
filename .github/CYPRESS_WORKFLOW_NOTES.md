# Cypress Workflow Optimization Notes

## Current Approach

The workflow deploys to Kinsta staging via **SSH + git** (not SFTP). On each PR:

1. SSH into staging server
2. `git fetch origin && git checkout <commit-sha>` (detached HEAD)
3. Run Cypress tests against staging
4. Reset staging to `development` branch

This is fast compared to the original SFTP mirror approach — deploy + cleanup takes seconds rather than minutes.

## Workflow Triggers

- `pull_request` to `master`, `main`, or `development`
- `workflow_dispatch` (manual)
- Fork PRs are skipped (secrets not available)

## Optimization Ideas

### Trigger Optimizations

#### Option A: Only PRs to Master (Recommended)
```yaml
on:
  pull_request:
    branches:
      - master
      - main
```
- Only runs for release PRs
- Development PRs don't need staging tests
- Significantly reduces workflow runs

#### Option B: Path Filtering
```yaml
on:
  pull_request:
    branches:
      - master
    paths:
      - '**.php'
      - '**.js'
      - '**.css'
      - 'cypress/**'
```
- Skip if only docs/config changed
- Reduces unnecessary runs

#### Option C: Manual Trigger Only
```yaml
on:
  workflow_dispatch:
```
- Run only when explicitly requested
- Most control, least automation

---

## Questions to Resolve

1. Do we need tests on PRs to development, or only master?
2. Should we add path filters to skip non-code changes?
3. Is the 10-minute timeout sufficient as tests grow?
