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

**Process note (learned 2026-06-28):** the staging notes are the author's terse
reminders, not specs. Each item was re-derived against the *current* library
source before deciding — several notes were stale (named things that already
exist under a different name, or assumed a hard change where a one-cycle
deprecation is better). Do not take a staging note at face value; interview the
author per item. Net result: `0.14.0` is cleanly **minor** — every breaking
change is staged as a one-cycle alias deprecation (no hard removals this release).

Library greys (`variables.styl`): `--color-gray-light-old: rgb(190,190,190)`,
`--color-gray-light: #EDEAEA`, `--color-gray-mid: #D4D4D4`.

| # | Item | Decision | Library target | Theme migration |
|---|---|---|---|---|
| 1 | Container-width vars | **Add** `$container-xxl…$container-smax` Stylus vars mirroring existing `--container-*` custom-prop values; stem naming (`$container-xl`, not `-width-`). Custom props already exist; the `$` vars are the gap (usable in `@media`/`calc`). | `variables.styl` | — |
| 2 | `.ui-tag:before` dot | **Fix:** `width`/`height` `1cap` → `0.7em` (cap unit renders inconsistently across browsers). | `modules/ui.styl` | — |
| 3 | `.text-overflow-ellipsis` | **Fix:** replace invalid `overflow: ellipsis` with `white-space:nowrap` + `overflow:hidden` + `text-overflow:ellipsis`. | `utility/helpers.styl` | — |
| 4 | `.ui-button--gray` | **Add** modifier (bg `--color-gray-mid`, text `--color-black-soft`, hover keeps gray border + black text). Confirmed not pre-existing (the other `--gray` rules are `.ui-dot` and a tag block). | `modules/ui.styl` (`.ui-button` block) | — |
| 5 | Grid nesting name | **Rename + alias:** add canonical `.grid-row--nested` + `.grid-row--nested-tight`; keep `.grid--nested(-tight)` as deprecated aliases for one cycle. | `functions/grid-function-new.styl` | `src/blocks/related-post/render.php` (2×) → `grid-row--nested` |
| 6 | Video embed container | **Generalise + modernise:** new base `.ui-embed-container` (default 16:9 via modern `aspect-ratio`) + ratio modifiers (`--4-3`, `--1-1`). Deprecate old `.u-video-embed-container` (`helpers.styl`) and `.ui-responsive-video-container` (`ui.styl`) as one-cycle aliases. | `modules/ui.styl` (new); deprecate in `helpers.styl` + `ui.styl` | all `.u-video-embed-container` usages (category.php, page-support.php ×2, category-novara-live.php, page-how-we-are-funded.php ×2, category-downstream.php, …) → `ui-embed-container` |
| 7/8/9 | Rounded-box consolidation | **Add + deprecate:** add `.ui-rounded-box--nested` (= `--corner-radius-large`); keep `--large` as alias one cycle; keep `.ui-rounded-image` one cycle with deprecation comment. | `modules/ui.styl` | — (theme already off these) |
| 10 | Border greys | **Swap all four** base utils (`.ui-border`, `-top`, `-bottom`, `-left`) `--color-gray-light-old` → `--color-gray-light`. (Answers the note's "same for others?" = yes.) | `modules/ui.styl` | — |
| 11 | `.ui-border--gray-mid` | **Add** modifier (`border-color: var(--color-gray-mid)`), placed after base utils so source order wins — no `:not()` hack (that hack was a theme-side workaround, unneeded in the lib). Keep `.ui-border--black`. | `modules/ui.styl` | — |

## Plan

### Phase A — execute the bump (this session)

1. Library: branch `upstream-nmcom-4-7-0` off `main`.
2. Apply items 1–11 to the target files (all additive or alias-deprecation;
   no hard removals this release).
3. Bump library `package.json` → `0.14.0`; prepend `CHANGELOG.md` entry
   (note the deprecated aliases + their removal-next-cycle); `[Release] 0.14.0`
   commit.
4. **Manual gate:** user reviews, pushes branch/tag `v0.14.0` (outward action
   stays with the user — no auto-push).
5. Theme: re-pin `package.json` → `#v0.14.0`; `npm install`; `npm run build`.
6. Theme migrations: `.u-video-embed-container` → `.ui-embed-container`
   (several templates); `grid--nested` → `grid-row--nested`
   (`related-post/render.php`).
7. Theme: clear the promoted entries from `upstream-to-library.styl`. Replace
   them with short "promoted to nm-stylus-library v0.14.0" breadcrumbs noting
   the deprecated aliases scheduled for removal next cycle. Keep nothing that
   was fully promoted.
8. Verify regressions via the existing `verify-dep-update` skill
   (bundle A/B + smoke). Smoke targets = item usages: any tag dot, a gray
   button, nested grids (related-post block), every migrated video embed
   (homepage YouTube, support page, novara-live, how-we-are-funded), bordered
   elements.
9. Commit theme changes on `chore/stylus-library-0.14.0`; open PR to
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
- Renames keep one-cycle aliases, so no consumer breaks in `0.14.0`. The alias
  removals land next cycle — track them (e.g. a follow-up staging breadcrumb).
- Item 6 changes the embed implementation (padding-bottom hack → `aspect-ratio`)
  AND the markup class. Both must be verified together in the smoke test; a
  visual regression here would hit every video embed across the site.
- `aspect-ratio` is supported in all evergreen browsers since 2021; acceptable
  for this audience. No fallback added.
