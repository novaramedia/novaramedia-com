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

- Library local clone: `~/Sites/novaramedia/nm-stylus-library`
  (separate repo, **not** vendored in the theme; npm pulls it from
  `github:novaramedia/nm-stylus-library#vX.Y.Z`).
- Library state: clean, `main`, version `0.13.0`, tags `v0.11/12/13`.
  Release flow = **`release-it`** (`npm run release`, requires `main`): runs
  `build:variables`, bumps version, moves `CHANGELOG.md` `[Unreleased]` → version
  via the keep-a-changelog plugin, commits `[Release] x.y.z`, tags `vx.y.z`,
  pushes, creates a **draft** GitHub release (published manually). Not hand-tagged.
  (`main` is one unreleased commit ahead of `v0.13.0`.)
- Library variables are generated: edit `variables.json` / `build-variables.js`,
  never `variables.styl`/`variables.js` directly (`npm run build:variables`).
  `npm test` validates the three stay in sync.
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
| 1 | Container-width vars | **Add** `$container-xxl…$container-smax` Stylus vars. Container values already exist in `variables.json` (`layout.containerXxl…containerSMax`) and emit as `--container-*` custom props. `variables.styl` is **auto-generated** — extend `scripts/build-variables.js` to also emit `$container-*` Stylus vars from the layout container entries, then `npm run build:variables`. Validator (`validate-variables.js`) doesn't check layout vars, so `npm test` still passes. | `scripts/build-variables.js` (regenerates `variables.styl`/`.js`) | — |
| 2 | `.ui-tag:before` dot | **Fix:** `width`/`height` `1cap` → `0.7em` (cap unit renders inconsistently across browsers). | `modules/ui.styl` | — |
| 3 | `.text-overflow-ellipsis` | **Fix:** replace invalid `overflow: ellipsis` with `white-space:nowrap` + `overflow:hidden` + `text-overflow:ellipsis`. (Currently broken at `modules/typography.styl:124-125`.) | `modules/typography.styl` | — |
| 4 | `.ui-button--gray` | **Add** modifier (bg `--color-gray-mid`, text `--color-black-soft`, hover keeps gray border + black text). Confirmed not pre-existing (the other `--gray` rules are `.ui-dot` and a tag block). | `modules/ui.styl` (`.ui-button` block) | — |
| 5 | Grid nesting name | **Rename + alias:** add canonical `.grid-row--nested` + `.grid-row--nested-tight`; keep `.grid--nested(-tight)` as deprecated aliases for one cycle. | `functions/grid-function-new.styl` | ~33 `grid--nested(-tight)` usages across ~20 files → `grid-row--nested(-tight)`. Deferred to Phase 2 (alias bridges). |
| 6 | Video embed container | **Generalise + modernise:** new base `.ui-embed-container` (default 16:9 via modern `aspect-ratio`) + ratio modifiers (`--4-3`, `--1-1`). Deprecate old `.u-video-embed-container` (`helpers.styl`, give it the new impl so existing usages upgrade) + the mis-nested `.ui-responsive-video-container` (`ui.styl`) as one-cycle aliases. | new `.ui-embed-container` in `modules/ui.styl`; deprecate in `utility/helpers.styl` + `modules/ui.styl` | 21 `.u-video-embed-container` usages across 18 files → `ui-embed-container`. Deferred to Phase 2 (alias bridges). |
| 7/8/9 | Rounded-box consolidation | **Add + deprecate:** add `.ui-rounded-box--nested` (= `--corner-radius-large`); keep `--large` as alias one cycle; keep `.ui-rounded-image` one cycle with deprecation comment. | `modules/ui.styl` | — (theme already off these) |
| 10 | Border greys | **Swap all four** base utils (`.ui-border`, `-top`, `-bottom`, `-left`) `--color-gray-light-old` → `--color-gray-light`. (Answers the note's "same for others?" = yes.) | `modules/ui.styl` | — |
| 11 | `.ui-border--gray-mid` | **Add** modifier (`border-color: var(--color-gray-mid)`), placed after base utils so source order wins — no `:not()` hack (that hack was a theme-side workaround, unneeded in the lib). Keep `.ui-border--black`. | `modules/ui.styl` | — |

## Plan

**Workflow principle (from author):** complete ALL library updates first; theme
migration is a distinct later stage done when updating the dep in the theme
project. Aliases bridge the gap so the theme keeps working between the two.

### Phase 1 — library (active)

1. Branch `upstream-nmcom-4-7-0` off `main` in `~/Sites/novaramedia/nm-stylus-library`.
2. Apply items 1–11 to target files (all additive or alias-deprecation; no hard
   removals). For item 1, edit `build-variables.js` then `npm run build:variables`.
3. Run `npm test` (variables sync) — must pass.
4. Add a `## [Unreleased]` block to `CHANGELOG.md` (Keep a Changelog format:
   Added / Changed / Fixed / Deprecated) covering items 1–11, noting deprecated
   aliases scheduled for removal next cycle. The existing "Corrected xxl
   container size variable" entry stays.
5. Commit on the branch; open PR to `main` (lib uses PRs).
6. **Manual gate (user):** merge to `main`, then `npm run release` (release-it,
   minor → `0.14.0`): builds variables, bumps, tags `v0.14.0`, pushes, creates a
   **draft** GitHub release. User reviews + publishes the draft. (Outward action
   stays with the user.)

### Phase 2 — theme (deferred; at theme dep-update stage)

7. Re-pin theme `package.json` → `#v0.14.0`; `npm install`; `npm run build`.
8. Migrate usages: `.u-video-embed-container` → `.ui-embed-container` (21 sites);
   `grid--nested(-tight)` → `grid-row--nested(-tight)` (~33 sites).
9. Clear promoted entries from `upstream-to-library.styl`; leave short
   "promoted to nm-stylus-library v0.14.0" breadcrumbs noting aliases due for
   removal next cycle.
10. Verify regressions via the `verify-dep-update` skill (bundle A/B + smoke).
    Smoke targets: any tag dot, a gray button, nested grids, every video embed
    (homepage YouTube, support page, novara-live, how-we-are-funded), bordered
    elements.
11. Commit on `chore/stylus-library-0.14.0`; open PR to `development` (user merges).

### Phase 3 — codify skill (after Phases 1–2)

New skill `stylus-library-bump`, authored from the real Phase 1–2 steps via the
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

## Future considerations (not this release — capture for the skill)

### Library docblock convention

Adopt a lightweight, consistent comment convention in `nm-stylus-library` for
modules/mixins/classes:

- One-line purpose above each block.
- `@since vX.Y.Z` when introduced.
- `@deprecated since vX.Y.Z → use <replacement>` on aliases scheduled for removal.

Payoff beyond docs: deprecations become **machine-greppable**. The bump skill
can `grep '@deprecated since'` to auto-list aliases due for removal in the next
cycle, instead of relying on memory or staging breadcrumbs. The library already
has loose header comments (e.g. the grid/ui module headers); this just
formalises them. Defer any doc *generator* tooling — convention first.

### Repo-agnostic skill design (donor repo is not always this theme)

Upstream styles can originate from **any** consumer/"donor" repo — this
WordPress theme today, but also a microservice or the Meteor app. The
`stylus-library-bump` skill must not hardcode theme specifics. Parameterise /
discover per run:

- **Staging-file path** — here `src/styl/upstream-to-library.styl`; varies per
  donor. Ask or detect.
- **Dependency-pin mechanism** — here npm `github:novaramedia/nm-stylus-library#vX`
  in `package.json`; another donor may pin differently (different manager,
  submodule, etc.). Detect the pin location, don't assume `package.json`.
- **Build + smoke targets** — delegated to `verify-dep-update`, which is already
  project-aware; the skill passes the donor's build/smoke context through.
- **Library clone path** — `~/Sites/novaramedia/nm-stylus-library` for this
  user; ask/confirm, store as a per-machine setting rather than hardcoding.

The invariant across donors: triage staging items against current lib → promote
(additive / one-cycle alias for breaking) → release lib (manual push gate) →
re-pin donor → migrate donor usages → clean staging → verify.

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
