# Stylus Library 0.14.0 Bump + Upstream Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Promote the 11 staged style items from the theme's `upstream-to-library.styl` into `nm-stylus-library`, release it as `0.14.0`, then (deferred) re-pin and migrate the theme.

**Architecture:** Two repos, three phases. Phase 1 (active): all library edits + changelog on a feature branch; user merges + `npm run release`. Phase 2 (deferred, at theme dep-update): re-pin, migrate ~54 call sites, clean staging, verify. Phase 3: author the `stylus-library-bump` skill from the real steps. Every breaking change ships as a one-cycle alias so nothing breaks in `0.14.0`.

**Tech Stack:** Stylus, a JSON-driven variables generator (`build-variables.js`), `release-it` + `@release-it/keep-a-changelog`, npm `github:` git-ref dependency, WordPress PHP templates (consumer).

## Global Constraints

- **LIB** = `~/Sites/novaramedia/nm-stylus-library`. **THEME** = this repo (`novaramedia-com`).
- LIB `variables.styl` / `variables.js` are **auto-generated** — never hand-edit. Edit `variables.json` and/or `scripts/build-variables.js`, then `npm run build:variables`.
- LIB release is `npm run release` (release-it) on `main` only — bumps, tags `vX.Y.Z`, pushes, creates a **draft** GitHub release. Never hand-tag. The user runs this (outward gate).
- Version: `0.13.0` → `0.14.0` (minor). Nothing is hard-removed this release; all breaking changes are one-cycle alias deprecations.
- Deprecation comment convention: `// @deprecated since v0.14.0 → use <replacement>. Removed next release.`
- Greys: `--color-gray-light-old: rgb(190,190,190)`, `--color-gray-light: #EDEAEA`, `--color-gray-mid: #D4D4D4`.
- No `dist/` commits in THEME unless source changed (theme CLAUDE.md).
- Stylus has no unit tests; per-task verification = grep/inspect the change + `npm test` (variables sync). Real regression check = `verify-dep-update` skill in Phase 2.

---

## Phase 1 — Library (active)

### Task 1: Branch the library

**Files:** none (git only)

- [ ] **Step 1: Confirm clean main**

Run: `cd ~/Sites/novaramedia/nm-stylus-library && git switch main && git status --short && git log --oneline -1`
Expected: clean tree; HEAD = `bc596f0 Fix container size error`.

- [ ] **Step 2: Create branch**

Run: `git switch -c upstream-nmcom-4-7-0`
Expected: `Switched to a new branch 'upstream-nmcom-4-7-0'`.

---

### Task 2: Item 1 — container Stylus variables

**Files:**
- Modify: `scripts/build-variables.js` (constants block, ~line 43)
- Regenerate: `variables.styl`, `variables.js`

**Interfaces:**
- Produces: Stylus vars `$container-xxl`, `$container-xl`, `$container-l`, `$container-m`, `$container-s`, `$container-smax` (values mirror `layout.container*` in `variables.json`).

- [ ] **Step 1: Add a container-vars block to the generator**

In `scripts/build-variables.js`, immediately after the constants block (the `content += '\n';` that follows the `variables.constants` loop, ~line 43), insert:

```js
  // Container width Stylus variables (mirror --container-* custom props; usable in @media / calc)
  content += '// Container widths\n';
  Object.entries(variables.layout)
    .filter(([key]) => key.startsWith('container'))
    .forEach(([key, value]) => {
      content += `$${camelToKebab(key)} = ${value}\n`;
    });
  content += '\n';
```

- [ ] **Step 2: Regenerate**

Run: `npm run build:variables`
Expected: "Generated variables.styl" / "Generated variables.js".

- [ ] **Step 3: Verify the Stylus vars exist**

Run: `grep '^\$container-' variables.styl`
Expected:
```
$container-xxl = 1400px
$container-xl = 1056px
$container-l = 888px
$container-m = 744px
$container-s = 98%
$container-smax = 460px
```

- [ ] **Step 4: Verify sync test passes**

Run: `npm test`
Expected: "All synchronization checks passed!" (exit 0).

- [ ] **Step 5: Commit**

```bash
git add scripts/build-variables.js variables.styl variables.js
git commit -m "feat: emit \$container-* Stylus vars from layout tokens"
```

---

### Task 3: Items 2 & 3 — tag-dot em fix + ellipsis fix

**Files:**
- Modify: `modules/ui.styl` (standalone `.ui-tag` block, `&:before`, ~line 129)
- Modify: `modules/typography.styl:124-125`

- [ ] **Step 1: Fix the tag dot unit**

In `modules/ui.styl`, in the **standalone** `.ui-tag` block (not the `.ui-tag-block` nested ones), find the `&:before` with the `1cap` sizing:

```stylus
    width 1cap
    height: 1cap
```

Replace with:

```stylus
    width: 0.7em
    height: 0.7em
```

- [ ] **Step 2: Fix the ellipsis utility**

In `modules/typography.styl`, replace lines 124-125:

```stylus
.text-overflow-ellipsis
  overflow: ellipsis
```

with:

```stylus
.text-overflow-ellipsis
  white-space: nowrap
  overflow: hidden
  text-overflow: ellipsis
```

- [ ] **Step 3: Verify**

Run: `grep -n "0.7em" modules/ui.styl; grep -nA3 "text-overflow-ellipsis" modules/typography.styl`
Expected: two `0.7em` lines; ellipsis block shows the three valid properties; no `overflow: ellipsis` remains.

- [ ] **Step 4: Commit**

```bash
git add modules/ui.styl modules/typography.styl
git commit -m "fix: tag dot uses 0.7em (not 1cap); valid text-overflow-ellipsis"
```

---

### Task 4: Item 4 — `.ui-button--gray`

**Files:**
- Modify: `modules/ui.styl` (`.ui-button` block, after `&--green` ~line 256)

- [ ] **Step 1: Add the modifier**

In `modules/ui.styl`, inside the `.ui-button` block, after the `&--green` modifier, add:

```stylus
  &--gray
    background-color: var(--color-gray-mid)
    color: var(--color-black-soft)
    &:hover
      border-color: var(--color-gray-mid)
      color: var(--color-black-soft)
```

- [ ] **Step 2: Verify**

Run: `grep -nA5 "&--gray" modules/ui.styl | grep -A5 gray-mid`
Expected: the new block present with `--color-gray-mid` and the hover override.

- [ ] **Step 3: Commit**

```bash
git add modules/ui.styl
git commit -m "feat: add .ui-button--gray modifier"
```

---

### Task 5: Items 7/8/9 — rounded-box consolidation (add + deprecate)

**Files:**
- Modify: `modules/ui.styl` (the `.ui-rounded-image` / `.ui-rounded-box` blocks, ~line 343)

- [ ] **Step 1: Add `--nested`, deprecate `--large` and `.ui-rounded-image`**

Replace the existing rounded block:

```stylus
.ui-rounded-image
  --ui-border-radius: var(--corner-radius)
  border-radius: var(--ui-border-radius)
  &--large
    --ui-border-radius: var(--corner-radius-large)

.ui-rounded-box
  --ui-border-radius: var(--corner-radius)
  border-radius: var(--ui-border-radius)
  &--large
    --ui-border-radius: var(--corner-radius-large)
  &--top
```

with:

```stylus
// @deprecated since v0.14.0 → use .ui-rounded-box (CSS-identical at default). Removed next release.
.ui-rounded-image
  --ui-border-radius: var(--corner-radius)
  border-radius: var(--ui-border-radius)
  &--large
    --ui-border-radius: var(--corner-radius-large)

.ui-rounded-box
  --ui-border-radius: var(--corner-radius)
  border-radius: var(--ui-border-radius)
  &--nested
    --ui-border-radius: var(--corner-radius-large)
  // @deprecated since v0.14.0 → use --nested. Removed next release.
  &--large
    --ui-border-radius: var(--corner-radius-large)
  &--top
```

(Leave the `&--top` / `&--bottom` modifiers that follow untouched.)

- [ ] **Step 2: Verify**

Run: `grep -nB1 -A2 "ui-rounded" modules/ui.styl | grep -E "deprecated|--nested|--large|ui-rounded"`
Expected: `--nested` present; `--large` retained with deprecation comment; `.ui-rounded-image` has deprecation comment.

- [ ] **Step 3: Commit**

```bash
git add modules/ui.styl
git commit -m "feat: ui-rounded-box--nested; deprecate --large and ui-rounded-image"
```

---

### Task 6: Items 10/11 — border greys + `--gray-mid`

**Files:**
- Modify: `modules/ui.styl` (the `.ui-border*` block, ~lines 317-329)

- [ ] **Step 1: Swap base utils to new grey, add modifier**

Replace:

```stylus
.ui-border
  border: 1px solid var(--color-gray-light-old)

.ui-border-bottom
  border-bottom: 1px solid var(--color-gray-light-old)

.ui-border-top
  border-top: 1px solid var(--color-gray-light-old)

.ui-border-left
  border-left: 1px solid var(--color-gray-light-old)

.ui-border--black
  border-color: var(--color-black-soft)
```

with:

```stylus
.ui-border
  border: 1px solid var(--color-gray-light)

.ui-border-bottom
  border-bottom: 1px solid var(--color-gray-light)

.ui-border-top
  border-top: 1px solid var(--color-gray-light)

.ui-border-left
  border-left: 1px solid var(--color-gray-light)

.ui-border--black
  border-color: var(--color-black-soft)

.ui-border--gray-mid
  border-color: var(--color-gray-mid)
```

(Note: `--gray-mid` is placed after the base utilities, so source order wins — no `:not()` hack needed. The `formElementBase()` `&--border-gray` and `:disabled` rules still use `-old`; out of scope, leave them.)

- [ ] **Step 2: Verify**

Run: `grep -nE "ui-border(\b|--|-)" modules/ui.styl | grep -E "gray-light|gray-mid|gray-light-old"`
Expected: four base utils now reference `--color-gray-light` (not `-old`); a `.ui-border--gray-mid` line referencing `--color-gray-mid`.

- [ ] **Step 3: Commit**

```bash
git add modules/ui.styl
git commit -m "feat: border utils use new gray-light; add .ui-border--gray-mid"
```

---

### Task 7: Item 5 — `.grid-row--nested` canonical + alias

**Files:**
- Modify: `functions/grid-function-new.styl` (the `.grid--nested` / `.grid--nested-tight` rules, ~lines 63-70)

**Interfaces:**
- Produces: `.grid-row--nested`, `.grid-row--nested-tight` (canonical); `.grid--nested`, `.grid--nested-tight` retained as deprecated aliases.

- [ ] **Step 1: Add canonical names via selector list**

Replace:

```stylus
    .grid--nested {
      margin-left: calc(var(--grid-gutter) / 2 * -1);
      margin-right: calc(var(--grid-gutter) / 2 * -1);
    }

    .grid--nested-tight {
      margin-left: calc(var(--grid-gutter) / 4 * -1);
      margin-right: calc(var(--grid-gutter) / 4 * -1);
    }
```

with:

```stylus
    // .grid--nested @deprecated since v0.14.0 → .grid-row--nested. Removed next release.
    .grid-row--nested, .grid--nested {
      margin-left: calc(var(--grid-gutter) / 2 * -1);
      margin-right: calc(var(--grid-gutter) / 2 * -1);
    }

    // .grid--nested-tight @deprecated since v0.14.0 → .grid-row--nested-tight. Removed next release.
    .grid-row--nested-tight, .grid--nested-tight {
      margin-left: calc(var(--grid-gutter) / 4 * -1);
      margin-right: calc(var(--grid-gutter) / 4 * -1);
    }
```

- [ ] **Step 2: Verify**

Run: `grep -nE "grid-row--nested|grid--nested" functions/grid-function-new.styl`
Expected: both canonical + alias selectors present, with deprecation comments.

- [ ] **Step 3: Commit**

```bash
git add functions/grid-function-new.styl
git commit -m "feat: .grid-row--nested(-tight); deprecate .grid--nested(-tight)"
```

---

### Task 8: Item 6 — `.ui-embed-container` + deprecate old video containers

**Files:**
- Modify: `modules/ui.styl` (add new top-level block at end; deprecate the mis-nested `.ui-responsive-video-container` ~line 514)
- Modify: `utility/helpers.styl` (deprecate `.u-video-embed-container` ~line 57)

**Interfaces:**
- Produces: `.ui-embed-container` (default 16:9 via `aspect-ratio`) + `--4-3`, `--1-1` ratio modifiers.

- [ ] **Step 1: Add the canonical container at the end of `modules/ui.styl`**

Append:

```stylus

// Embeds
// -------------

// Responsive aspect-ratio container for iframes / embeds (video, maps, etc.).
// Default 16:9; use ratio modifiers for other ratios.
.ui-embed-container
  width: 100%
  aspect-ratio: 16 / 9
  overflow: hidden
  iframe, object, embed, video
    width: 100%
    height: 100%
    border: 0
    display: block
    clip-path: inset(1px 1px)
  &--4-3
    aspect-ratio: 4 / 3
  &--1-1
    aspect-ratio: 1 / 1
```

- [ ] **Step 2: Deprecate the mis-nested `.ui-responsive-video-container`**

In `modules/ui.styl`, immediately above the existing `.ui-responsive-video-container` (nested under `.ui-inline-list`, ~line 514), add:

```stylus
    // @deprecated since v0.14.0 → use .ui-embed-container (also fixes this rule's accidental nesting under .ui-inline-list). Removed next release.
```

(Leave its body; it is unused by the theme.)

- [ ] **Step 3: Deprecate `.u-video-embed-container` (keep its impl)**

In `utility/helpers.styl`, immediately above `.u-video-embed-container` (~line 57), add:

```stylus
// @deprecated since v0.14.0 → use .ui-embed-container. Removed next release.
```

(Keep the existing padding-bottom implementation unchanged — the 21 existing consumers must not change behaviour this cycle; the aspect-ratio upgrade arrives when they migrate to `.ui-embed-container` in Phase 2.)

- [ ] **Step 4: Verify**

Run: `grep -nA12 "^\.ui-embed-container" modules/ui.styl; grep -nB1 "u-video-embed-container" utility/helpers.styl`
Expected: new `.ui-embed-container` block with `aspect-ratio` + modifiers; deprecation comments on both old classes.

- [ ] **Step 5: Commit**

```bash
git add modules/ui.styl utility/helpers.styl
git commit -m "feat: .ui-embed-container (aspect-ratio); deprecate old video containers"
```

---

### Task 9: Changelog + PR

**Files:**
- Modify: `CHANGELOG.md` (`## [Unreleased]` section)

- [ ] **Step 1: Fill the Unreleased section**

In `CHANGELOG.md`, under `## [Unreleased]` (which already has `### Fixed` / `- Corrected xxl container size variable`), set the section to:

```markdown
## [Unreleased]

### Added

- `$container-*` Stylus variables (`$container-xxl`…`$container-smax`) for use in `@media`/`calc`, mirroring the `--container-*` custom properties
- `.ui-button--gray` button modifier
- `.ui-border--gray-mid` border colour modifier
- `.ui-rounded-box--nested` (4px) modifier for nested colour-blocked boxes
- `.grid-row--nested` / `.grid-row--nested-tight` — canonical names for nested-grid negative margins
- `.ui-embed-container` responsive aspect-ratio embed container (default 16:9) with `--4-3` / `--1-1` ratio modifiers

### Changed

- `.ui-tag` dot indicator sized in `em` (`0.7em`) instead of `1cap` for cross-browser consistency
- `.ui-border`, `.ui-border-top`, `.ui-border-bottom`, `.ui-border-left` now use `--color-gray-light` (new grey) instead of `--color-gray-light-old`

### Fixed

- Corrected xxl container size variable
- `.text-overflow-ellipsis` now uses valid `white-space` + `overflow` + `text-overflow` (was invalid `overflow: ellipsis`)

### Deprecated

- `.ui-rounded-image` → use `.ui-rounded-box`; removed next release
- `.ui-rounded-box--large` → use `.ui-rounded-box--nested`; removed next release
- `.grid--nested` / `.grid--nested-tight` → use `.grid-row--nested(-tight)`; removed next release
- `.u-video-embed-container` / `.ui-responsive-video-container` → use `.ui-embed-container`; removed next release
```

- [ ] **Step 2: Commit**

```bash
git add CHANGELOG.md
git commit -m "docs: changelog for 0.14.0"
```

- [ ] **Step 3: Push branch + open PR to main**

```bash
git push -u origin upstream-nmcom-4-7-0
gh pr create --base main --title "Upstream nmcom 4.7.0 → library 0.14.0" --body "Promotes 11 staged items from novaramedia-com upstream-to-library.styl. All additive or one-cycle alias deprecations; no hard removals. See theme docs/superpowers/specs/2026-06-28-stylus-library-bump-design.md."
```

- [ ] **Step 4: MANUAL GATE — user merges + releases**

The user:
1. Reviews + merges the PR to `main`.
2. `git switch main && git pull`
3. `npm run release` → choose **minor** (`0.14.0`). release-it builds variables, moves changelog, tags `v0.14.0`, pushes, creates a **draft** GitHub release.
4. Reviews + publishes the draft release on GitHub.

Do not automate steps 1–4 — outward action stays with the user.

---

## Phase 2 — Theme (deferred; run at the theme dep-update stage)

> Executed later, in THEME, on branch `chore/stylus-library-0.14.0` (already created). Aliases keep the theme working until then.

### Task 10: Re-pin + install + build

- [ ] In THEME `package.json`, change `nm-stylus-library` ref `#v0.13.0` → `#v0.14.0`.
- [ ] `npm install` (updates `package-lock.json` to the new git SHA).
- [ ] `npm run build`; confirm exit 0.
- [ ] Commit `package.json` + `package-lock.json` (+ `dist/` only if source-driven output changed).

### Task 11: Migrate call sites

- [ ] Video embeds — replace all 21 `u-video-embed-container` with `ui-embed-container` across the 18 PHP files (`category.php`, `page-how-we-are-funded.php`, `category-novara-live.php`, `page-support.php`, `category-downstream.php`, `single-newsletter.php`, `page__newsletter.php`, `single-event.php`, `category-do-your-own-research.php`, `blocks/related-post/render.php`, `lib/functions-filters.php`, `partials/post-layouts/archive-post.php`, `partials/specials/banners/support-video.php`, `partials/singles/single-post-video.php`, `partials/front-page/show-blocks/dyor.php`, `partials/front-page/show-blocks/dyor-alt.php`, `partials/front-page/above-the-fold/featured-post-primary.php`, `src/blocks/related-post/render.php`).
- [ ] Grid — replace ~33 `grid--nested` / `grid--nested-tight` with `grid-row--nested` / `grid-row--nested-tight` across the ~20 files from the spec scan. Preserve other classes on the same element.
- [ ] Verify zero stragglers: `grep -rn "u-video-embed-container\|grid--nested" --include="*.php" . | grep -v node_modules` → empty.
- [ ] Rebuild; commit.

### Task 12: Clean the staging file

- [ ] In THEME `src/styl/upstream-to-library.styl`, remove the promoted entries; replace with a short breadcrumb block, e.g.:

```stylus
// Promoted to nm-stylus-library v0.14.0 (2026-06): container $ vars, ui-tag dot em fix,
// text-overflow-ellipsis fix, ui-button--gray, grid-row--nested(-tight), ui-embed-container,
// ui-rounded-box--nested, ui-border new-grey + --gray-mid.
// Aliases kept in lib for one cycle (removed next release): ui-rounded-image,
// ui-rounded-box--large, grid--nested(-tight), u-video-embed-container, ui-responsive-video-container.
```

- [ ] Commit.

### Task 13: Verify + PR

- [ ] Run the `verify-dep-update` skill (bundle A/B + smoke). Smoke targets: a tag dot, a gray button, nested grids, every video embed (homepage YouTube, support page, novara-live, how-we-are-funded), bordered elements.
- [ ] Open PR `chore/stylus-library-0.14.0` → `development`. User merges.

---

## Phase 3 — Skill (after Phases 1–2)

### Task 14: Author `stylus-library-bump` skill

- [ ] Use the superpowers `writing-skills` skill to author `~/.claude/skills/stylus-library-bump/SKILL.md` from the real steps above.
- [ ] Encode the captured knowledge: lib clone path (ask/confirm, don't hardcode), `upstream-nmcom-<consumer-version>` branch convention, staging→target file map, generated-variables rule (edit json/generator, not styl), release-it draft-release flow + manual gate, per-item triage (promote / one-cycle alias / already-upstream — verify against current lib, notes go stale), and repo-agnostic donor handling (staging path, dep-pin mechanism, build/smoke all parameterised).
- [ ] Delegate the regression tail to `verify-dep-update` rather than duplicating it.

---

## Self-Review

- **Spec coverage:** items 1–11 each have a task (1→T2, 2&3→T3, 4→T4, 7/8/9→T5, 10/11→T6, 5→T7, 6→T8); changelog T9; theme re-pin/migrate/clean/verify T10–13; skill T14. ✓
- **Generated-variables risk:** Task 2 edits the generator + regenerates + runs `npm test`. ✓
- **Release flow:** Task 9 uses release-it via the user, not a hand tag. ✓
- **No hard removals:** every breaking change in T5/T7/T8 keeps a one-cycle alias. ✓
- **Type/name consistency:** `.ui-embed-container`, `.grid-row--nested(-tight)`, `.ui-rounded-box--nested`, `.ui-border--gray-mid`, `$container-*` used identically across tasks, changelog, and Phase 2 migration. ✓
