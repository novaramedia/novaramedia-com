# Stylus library version bump + upstream — design

Date: 2026-06-28
Notion task: Stylus library version bump and update (Tasks DB)

## Problem

The theme stages shared style changes locally in `src/styl/upstream-to-library.styl`
("the upstream file") as live overrides + TODO notes. Periodically these must be
promoted into the shared `nm-stylus-library` repo, the library re-released, the
theme's dependency re-pinned to the new version, and the staging file cleaned.
This cycle recurs every few releases and is currently un-codified.

## Goal

1. Promote the pending items into the library.
2. Release library `0.14.0` (minor) and re-pin the theme to it.
3. Clean the promoted entries out of the staging file.
4. Verify no regressions in the theme.
5. Codify the whole cycle as a repeatable skill (`stylus-library-bump`).

## Context (as explored 2026-06-28)

- Library local clone: `/Users/patrickbest/Sites/novaramedia/nm-stylus-library`
  (separate repo, **not** vendored in the theme; npm pulls it from
  `github:novaramedia/nm-stylus-library#vX.Y.Z`).
- Library state: clean, `main`, version `0.13.0`, tags `v0.11/12/13`.
  Release flow = bump `package.json` + update `CHANGELOG.md` + `[Release] x.y.z`
  commit + `git tag vx.y.z`. (`main` is one unreleased commit ahead of `v0.13.0`.)
- Theme pin: `package.json` → `nm-stylus-library: github:...#v0.13.0`.
- Theme is the sole consumer.
- Prior upstreaming branch convention: `upstream-nmcom-<theme-version>`
  (e.g. `upstream-nmcom-1-4-4`).

## Staging file triage (`src/styl/upstream-to-library.styl`)

### A. Real overrides to PROMOTE (additions/fixes)

| Item | Library target |
|---|---|
| `.ui-tag:before` width/height → `0.7em` (cap-unit browser fix) | `modules/ui.styl` |
| `.text-overflow-ellipsis` → white-space + overflow + text-overflow (fix invalid `overflow: ellipsis`) | `utility/helpers.styl` |
| `.ui-button--gray` modifier | `modules/ui.styl` |
| `.grid-row.grid-row--nested` negative-margin helper | `modules/layout.styl` |
| `.ui-border-top:not(--black):not(--gray-mid)` → new `--color-gray-light` | `modules/ui.styl` |
| `.ui-border--gray-mid` colour modifier | `modules/ui.styl` |

### B. Breaking consolidation (theme source confirmed not using these)

- `.ui-rounded-box--nested` (4px / `--corner-radius-large`) — implemented by
  **renaming** the existing `.ui-rounded-box--large` → `--nested`.
- Remove `.ui-rounded-image` (CSS-identical to `.ui-rounded-box` at default).

### C. Already satisfied upstream → DELETE from staging, no promotion

- Container-width vars: already exist as `--container-*` in library `variables.styl`.
  (Staging note used stale `$container-width-*` syntax.)
- `.ui-16-9-embed-container`: already exists as `.ui-responsive-video-container`
  (ui-prefixed, general video embed, 56.25%) in library `modules/ui.styl`.

## Plan

### Phase A — execute the bump (this session)

1. Library: branch `upstream-nmcom-4-7-0` off `main`.
2. Apply A (additions/fixes) and B (rename + remove) to the target files.
3. Bump library `package.json` → `0.14.0`; prepend `CHANGELOG.md` entry;
   `[Release] 0.14.0` commit.
4. **Manual gate:** user reviews, pushes branch/tag `v0.14.0` (outward action
   stays with the user — no auto-push).
5. Theme: re-pin `package.json` → `#v0.14.0`; `npm install`; `npm run build`.
6. Theme: delete promoted (A+B) and already-upstream (C) entries from
   `upstream-to-library.styl`; keep anything genuinely deferred.
7. Verify regressions via the existing `verify-dep-update` skill
   (bundle A/B + smoke). Smoke targets = the promoted classes' usages.
8. Commit theme changes on `chore/stylus-library-0.14.0`; open PR to
   `development` (user merges).

### Phase B — codify skill (after Phase A)

New skill `stylus-library-bump`, authored from the real Phase A steps via the
superpowers `writing-skills` skill. It orchestrates the cycle and **delegates
the regression tail to `verify-dep-update`** rather than duplicating it.

Captured non-obvious knowledge:
- Library is a separate local clone (`~/Sites/novaramedia/nm-stylus-library`),
  not vendored; npm pin is `github:...#vX.Y.Z`.
- Branch convention `upstream-nmcom-<theme-version>`.
- Staging-file → library target-file map (ui/layout/helpers/variables).
- Always triage staging items into promote / breaking / already-upstream —
  staging notes go stale; verify against current library before promoting.
- Breaking removals: confirm zero theme-source usage (php/js/styl, excl. dist)
  first.
- Release flow + manual push/tag gate.
- Order: release library → re-pin → install → build → clean staging → verify.

## Out of scope

- Changing the library build/release tooling.
- Promoting items not currently in the staging file.

## Risks

- Library `main` is ahead of `v0.13.0` by one commit ("Fix container size
  error") — branch off `main`, so `0.14.0` includes it. Confirm that is intended.
- Renames are breaking for any *other* consumer; theme is sole consumer, so
  low risk, but note it in the library CHANGELOG.
