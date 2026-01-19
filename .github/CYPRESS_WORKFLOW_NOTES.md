# Cypress Workflow Optimization Notes

## Current Issues

### 1. Slow File Transfers
- Full SFTP mirror uploads ALL files on every run (~several minutes)
- Cleanup step also does full mirror to restore development branch
- Total transfer time can be 10+ minutes per run

### 2. Workflow Triggers Too Broadly
- Currently triggers on PRs to master, main, development
- Also triggers on push to feature branch (temporary for testing)
- May want to restrict to only critical pre-release PRs

---

## Optimization Ideas

### File Transfer Optimizations

#### Option A: Diff-based Upload
- Use `git diff --name-only development...HEAD` to get changed files
- Only upload files that actually changed
- Pros: Much faster for small changes
- Cons: More complex script, need to handle deleted files

#### Option B: Rsync over SSH (instead of SFTP)
- Rsync has built-in diff/delta transfer
- Only transfers changed bytes within files
- Requires SSH access (we already have this for WP-CLI)
- Pros: Very efficient, handles deletions properly
- Cons: Need to verify rsync available on Kinsta

#### Option C: Skip Cleanup/Restore Step
- Don't restore development branch after tests
- Let staging stay on PR branch until next run
- Pros: Saves entire second transfer
- Cons: Staging may be in unexpected state between runs

#### Option D: Incremental lftp
- lftp mirror has `--only-newer` flag
- Could skip unchanged files based on timestamp
- Pros: Simple change to existing script
- Cons: Timestamps may not be reliable across git checkouts

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

## Implementation Priority

### Phase 1: Quick Wins (Do Now)
- [ ] Change triggers to only PRs to master/main
- [ ] Remove temporary push trigger for feature branch
- [ ] Consider removing cleanup/restore step

### Phase 2: Transfer Optimization (Future)
- [ ] Investigate rsync over SSH as replacement for lftp
- [ ] Test diff-based upload approach
- [ ] Benchmark different approaches

### Phase 3: Advanced (If Needed)
- [ ] Consider caching strategies
- [ ] Investigate Kinsta-specific optimizations
- [ ] Look at parallel test execution

---

## Questions to Resolve

1. Is it acceptable for staging to remain on PR branch between workflow runs?
2. Do we need tests on PRs to development, or only master?
3. Is rsync available on Kinsta SSH?
4. Should we add path filters to skip non-code changes?

---

## Current Workflow Status

- **Branch**: `copilot/add-cypress-testing-ci`
- **Last Issue Fixed**: Cypress action using yarn instead of npm (added `install: false`)
- **Workflow Status**: WORKING - all deployment steps pass
- **Known Working**: SFTP deploy, WP-CLI theme activation, cache clear, npm install, Cypress execution

---

## Test Selector Issues (Need Fixing)

The Cypress tests run but some fail due to CSS selectors not matching actual DOM:

### homepage.cy.js (7/8 passing)
- **Failed**: "should display post content"
- **Issue**: `article, .article, .post` not found
- **Fix**: Update selector to match actual homepage article elements

### single-post.cy.js (0/10 - before all hook failed)
- **Failed**: before all hook couldn't find article links
- **Issue**: `main article a, .main article a, #main article a, article h2 a, article h3 a` not found
- **Fix**: Update selector to match actual homepage article link structure

### support-page.cy.js (8/9 passing)
- **Failed**: "should have appropriate heading structure"
- **Issue**: `h1` element not found on support page
- **Fix**: Check if support page has h1 or uses different heading structure

---

## Time Breakdown (Observed)

| Step | Duration |
|------|----------|
| SFTP Deploy | ~10-12 min |
| WP-CLI + Cache | ~30 sec |
| npm ci | ~25 sec |
| Cypress Tests | ~2 min |
| SFTP Cleanup | ~5 min |
| **Total** | **~18 min** |

The SFTP transfers account for most of the time (~15 min combined).
