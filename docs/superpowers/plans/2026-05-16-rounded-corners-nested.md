# Rounded Corners — Nested Class & Deprecations Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add `ui-rounded-box--nested` to express the nested-box visual rule, deprecate `--large` and `ui-rounded-image`, fix all template usages, and correct the support form corner bug.

**Architecture:** All CSS additions go into `src/styl/upstream-to-library.styl` (local overrides/additions pending nm-stylus-library upstream). Template changes are pure class renames. No new files created.

**Tech Stack:** PHP templates, Stylus, Webpack (`npm run build`)

---

## File Map

| File | Change |
|------|--------|
| `src/styl/upstream-to-library.styl` | Add `ui-rounded-box--nested`, deprecation comments |
| `lib/renderers.php` | Remove `ui-rounded-box--large` from support form class |
| `partials/front-page/show-blocks/audio.php` | Add `ui-rounded-box--nested` to inner white box; swap image class |
| 13 other PHP template files | Swap `ui-rounded-image` → `ui-rounded-box` |

---

### Task 1: Add `ui-rounded-box--nested` and deprecation notes to upstream-to-library.styl

**Files:**
- Modify: `src/styl/upstream-to-library.styl`

- [ ] **Step 1: Append the new class and deprecation comments**

Open `src/styl/upstream-to-library.styl`. Append at the end of the file (after the existing content at line 46):

```stylus
// Rounded corner nesting rule:
// Inner color-blocked boxes (ui-rounded-box inside another ui-rounded-box)
// need a larger radius (4px) to look optically parallel with outer corners.
// Visual rule: outer color-blocked box → ui-rounded-box (2px, default)
//              inner color-blocked box → ui-rounded-box--nested (4px)
// Images/thumbnails use ui-rounded-box too — same 2px as outer boxes.
// Upstream: add --nested modifier to nm-stylus-library ui-rounded-box block.
.ui-rounded-box--nested
  --ui-border-radius: var(--corner-radius-large)

// Deprecated: ui-rounded-image is CSS-identical to ui-rounded-box at default.
// Element-type distinction is misleading — border-radius applies to all elements.
// Replaced by ui-rounded-box throughout this theme.
// Upstream: remove ui-rounded-image from nm-stylus-library, consolidate into ui-rounded-box.

// Deprecated: ui-rounded-box--large has no semantic meaning.
// Replaced by ui-rounded-box--nested which describes the actual use case.
// Upstream: rename --large to --nested in nm-stylus-library ui-rounded-box block.
```

- [ ] **Step 2: Build to confirm no Stylus errors**

```bash
npm run build 2>&1 | tail -20
```

Expected: build completes without errors. CSS output should contain `.ui-rounded-box--nested`.

Verify:

```bash
grep "ui-rounded-box--nested" dist/main.css
```

Expected: outputs the compiled rule with `border-radius: var(--corner-radius-large)` (or `4px`).

- [ ] **Step 3: Commit**

```bash
git add src/styl/upstream-to-library.styl dist/main.css dist/main.css.map
git commit -m "Add ui-rounded-box--nested class and deprecate --large and ui-rounded-image"
```

---

### Task 2: Swap `ui-rounded-image` → `ui-rounded-box` across all templates

**Files:**
- Modify: 14 PHP template files (bulk rename, no logic change)

- [ ] **Step 1: Verify current count before replace**

```bash
grep -rn "ui-rounded-image" partials/ src/blocks/ --include="*.php" | wc -l
```

Expected: `16`

- [ ] **Step 2: Run bulk replace**

```bash
find partials/ src/blocks/ -name "*.php" -exec sed -i '' 's/ui-rounded-image/ui-rounded-box/g' {} +
```

- [ ] **Step 3: Verify replace completed**

```bash
grep -rn "ui-rounded-image" partials/ src/blocks/ --include="*.php"
```

Expected: no output (zero matches).

```bash
grep -rn "ui-rounded-box" partials/ src/blocks/ --include="*.php" | grep -v "ui-rounded-box--" | wc -l
```

Expected: count increased by 16 compared to before (was 10 `ui-rounded-box` usages, now 26).

- [ ] **Step 4: Commit**

```bash
git add partials/ src/blocks/
git commit -m "Swap ui-rounded-image to ui-rounded-box across all templates"
```

---

### Task 3: Apply `ui-rounded-box--nested` to the audio block inner white box

**Files:**
- Modify: `partials/front-page/show-blocks/audio.php:43`

Context: Three nesting levels exist in this file:
- Line 27: outer grid wrapper, `ui-rounded-box` — no background, just clipping
- Line 28: colored product box, `ui-rounded-box` — this is the outer visible box (2px correct)
- Line 43: white post box inside the colored box — needs `ui-rounded-box--nested` (4px)

- [ ] **Step 1: Edit audio.php line 43**

Find (line 43):
```php
        <div class="background-white font-color-black pt-4 pb-4 pl-4 pr-4 mb-4 ui-rounded-box">
```

Replace with:
```php
        <div class="background-white font-color-black pt-4 pb-4 pl-4 pr-4 mb-4 ui-rounded-box ui-rounded-box--nested">
```

- [ ] **Step 2: Verify no other `ui-rounded-box` in this file needs `--nested`**

```bash
grep -n "ui-rounded-box" partials/front-page/show-blocks/audio.php
```

Expected output:
```
27:    <div class="grid-item is-s-24 is-xxl-12 mb-4 font-color-... ui-rounded-box">
28:      <div class="front-page__audio-product ... ui-rounded-box">
43:        <div class="background-white ... ui-rounded-box ui-rounded-box--nested">
56:                      'class' => 'ui-rounded-box',
```

Line 27 — outer wrapper, no bg, 2px: correct.
Line 28 — outer colored box, 2px: correct.
Line 43 — inner white box, now 4px: correct.
Line 56 — thumbnail image, 2px: correct.

- [ ] **Step 3: Commit**

```bash
git add partials/front-page/show-blocks/audio.php
git commit -m "Apply ui-rounded-box--nested to nested inner box in audio block"
```

---

### Task 4: Fix support form corner bug

**Files:**
- Modify: `lib/renderers.php:307`

Bug: support form is an outer color-blocked box but uses `ui-rounded-box--large` (4px). Outer boxes use `ui-rounded-box` (2px).

- [ ] **Step 1: Edit renderers.php**

Find (around line 307):
```php
    <form class="support-form background-red font-color-white ui-rounded-box ui-rounded-box--large" action="https://donate.novaramedia.com/regular" id="<?php echo esc_attr( $instance ); ?>">
```

Replace with:
```php
    <form class="support-form background-red font-color-white ui-rounded-box" action="https://donate.novaramedia.com/regular" id="<?php echo esc_attr( $instance ); ?>">
```

- [ ] **Step 2: Confirm no other `ui-rounded-box--large` remains in the codebase**

```bash
grep -rn "ui-rounded-box--large" src/ partials/ lib/ --include="*.php" --include="*.styl"
```

Expected: no output. The only mention should be the deprecation comment in `upstream-to-library.styl`.

```bash
grep -rn "ui-rounded-box--large" src/styl/upstream-to-library.styl
```

Expected: the deprecation comment line only.

- [ ] **Step 3: Commit**

```bash
git add lib/renderers.php
git commit -m "Fix support form corner radius — remove --large from outer box"
```

---

### Task 5: Final build and visual check

- [ ] **Step 1: Full build**

```bash
npm run build 2>&1 | tail -20
```

Expected: no errors.

- [ ] **Step 2: Audit final class usage**

```bash
grep -rn "ui-rounded-image\|ui-rounded-box--large" partials/ src/ lib/ --include="*.php" --include="*.styl" | grep -v "upstream-to-library.styl"
```

Expected: no output. Both deprecated names gone from all active code.

```bash
grep -rn "ui-rounded-box--nested" partials/ src/ lib/ --include="*.php"
```

Expected: `partials/front-page/show-blocks/audio.php:43` — one match.

- [ ] **Step 3: Visual check on local**

Load the following pages in a browser against local DevKinsta:
1. Home page — check front page audio block (nested white box should have 4px, outer colored box 2px)
2. Any article/archive page — check thumbnail images still have subtle rounding
3. Support page or any page with the support form — check form corners are now 2px (was 4px)

- [ ] **Step 4: Commit dist if changed**

```bash
git status
```

If `dist/` files show as modified (should already be committed from Task 1, but verify):

```bash
git add dist/main.css dist/main.css.map
git commit -m "Build: rounded corners fix dist output"
```
