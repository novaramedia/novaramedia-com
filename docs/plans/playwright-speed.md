# Playwright CI speed

Audit of where the staging Playwright job spends its time, and what to change. Nothing here is built yet beyond the Dependabot skip (below).

## Where the time goes

Measured 2026-10-20 on the `Playwright Tests (Staging)` job (~4 min end to end):

| Step | Time |
| --- | --- |
| Deploy, activate, cache clear, reachability check | ~7s |
| `npm ci` | ~35s |
| Browser install | ~19s |
| **`npx playwright test` (116 tests, 2 workers)** | **~141s** |

Per test, against staging (local JSON reporter run): every test costs **3–4s regardless of what it asserts**, ranging from 2.7s to 4.8s on average per file. The cost is the page load, not the checks. Each spec reloads its page in `beforeEach` through `gotoFresh()`, which cache-busts, so 116 tests means 116+ uncached WordPress renders on staging for roughly 15 distinct pages.

Ruled out:

- **Official Playwright container image** (PR #636, closed): saves the 19s browser install but costs ~24s image pull plus ~6s installing SSH/git/curl. Net slower.
- **More workers**: 2 → 6 workers cut a local run from 270s to 230s (~15%). Staging is the bottleneck, not runner parallelism.

## 1. Load each page once per spec

The biggest win, and test-only (no workflow change).

- One `gotoFresh()` per page instead of per test: fold a spec's checks into one test per page, each check wrapped in `test.step()` so the report still names what failed.
- Use `expect.soft()` for independent checks so one failure doesn't hide the rest.
- Responsive checks already reuse the loaded page via `setViewportSize()`; keep that.
- `consoleErrors` is per test, so a consolidated test still collects errors for the whole page visit.
- Specs whose tests click or mutate the page (nav toggle, attribution form) keep a fresh load for those tests only.

Estimate: ~20 page loads instead of ~120, so test time drops from ~141s to roughly 30–40s.

## 2. Core and full suites

Tag one consolidated test per template `@core`: home, single article/audio/video, a category archive, support, about, jobs, vanity URLs. That's around 10 page loads.

- Normal PR pushes: `npx playwright test --grep @core`.
- Full suite: when a PR is marked ready for review, on PRs into `master` (releases), and on manual dispatch.
- Run the full suite locally for spec changes before pushing.

## 3. Trigger less often

The workflow runs on every push to every PR, drafts included. 99 runs in the month to 2026-10-20, 23 of them on one branch.

- **Skip draft PRs**: add `ready_for_review` to the trigger types and `!github.event.pull_request.draft` to the job condition. Marking a PR ready runs it.
- **Cancel superseded runs**: workflow-level `concurrency: playwright-<PR number>` with `cancel-in-progress: true`, keeping `kinsta-staging` (no cancel) at job level as the staging lock. The reset and SSH cleanup steps are already `if: always()`, so a cancelled run still puts staging back on `development`.
- **Allowlist paths** rather than ignore them: `**.php`, `dist/**`, `tests/e2e/**`, `playwright.config.js`, `package-lock.json`, the workflow itself. Staging serves committed `dist/`, so `src/` changes without a rebuild can't change the result.

Note: GitHub keeps only one *pending* run per concurrency group. With several PRs queued on `kinsta-staging`, older pending runs get cancelled in favour of newer ones. Fewer runs makes this rarer.

## Later, maybe

- `npm ci` installs ~2,300 packages to use one. Installing only `@playwright/test` would save ~30s, but the config `require`s it from the repo, so it needs care.

## Done

- Dependabot PRs skip the staging job (they get no SSH key, and a lockfile bump never changes committed `dist/`); `dependabot-build.yml` checks they still build instead.

## Order

1 first (biggest win, no CI change). Then 2 and 3 together after discussion, since both change when CI runs.
