# Release Flows

Two ways code reaches production. Both end in a PR to `master`; merging that PR
is the deploy trigger. A human always merges — scripts only create PRs.

Versioning is semver. `scripts/release.sh` wraps `release-it`, which bumps
`package.json`, converts the `[Unreleased]` section of `CHANGELOG.md` into the
new version, runs `npm run build`, and stamps `style.css`. The script then
commits everything as `Build: x.y.z` and pushes.

## Normal release (minor/major)

Ships everything accumulated on `development`.

```
git checkout development
./scripts/release.sh [major|minor|patch] [--pr]
```

- Must run from a clean `development` (no uncommitted or untracked files).
- Creates PR `development` → `master` titled `Release: x.y.z`.
- Merge the PR to deploy, then work through `docs/post-deploy-checklist.md`.

## Hotfix release (patch)

For shipping one small fix **without** dragging along whatever is sitting
unreleased on `development`. Branches from `master` (production state), not
`development`.

```
git checkout -b hotfix/<short-name> origin/master
# make the fix, add a CHANGELOG entry under ## [Unreleased]
git commit ...
./scripts/release.sh --hotfix [--pr]
```

- `--hotfix` requires a `hotfix/*` branch and forces a patch increment.
- Creates PR `hotfix/<short-name>` → `master` titled `Release: x.y.z (hotfix)`.
- Merge the PR to deploy, then work through `docs/post-deploy-checklist.md`.

### Back-merge (required, do not skip)

After the hotfix PR merges, `master` holds the fix, the version bump, and the
changelog entry that `development` lacks. Sync them back so the next normal
release doesn't collide on version numbers or changelog history:

```
git fetch origin
git checkout development && git pull --ff-only origin development
git merge origin/master && git push origin development
```

The script prints this reminder at the end of every `--hotfix` run.

### When a hotfix is the wrong tool

If `development` has nothing unreleased (check with
`git log origin/master..origin/development`), the normal flow ships the same
code with less ceremony — a hotfix branch buys nothing. Hotfixes exist for when
`development` is ahead with work you don't want to ship yet.
