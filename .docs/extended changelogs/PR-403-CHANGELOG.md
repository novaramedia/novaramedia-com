# PR #403 Changelog: Migrate to nm-stylus-library Grid System

## Overview

This PR modernizes the CSS architecture by replacing the legacy 12-column flex-grid system with the 24-column nm-stylus-library grid, removing deprecated spacing classes, eliminating Stylus variables in favor of CSS custom properties, and consolidating redundant styles.

**Net result:** -86,599 lines removed, +16,181 lines added across 150 files

---

## 1. Grid System Migration

### What Changed

- **Old system:** 12-column flex-grid with `flex-grid-row`, `flex-item-{breakpoint}-{n}`, `flex-offset-{breakpoint}-{n}`
- **New system:** 24-column nm-stylus-library grid with `grid-row`, `is-{breakpoint}-{n}`, `offset-{breakpoint}-{n}`

### Column Value Conversion

All column values were doubled to maintain equivalent widths:

```
Old (12-col)  →  New (24-col)
flex-item-s-12   →  is-s-24    (full width)
flex-item-m-6    →  is-m-12    (half width)
flex-item-l-4    →  is-l-8     (third width)
flex-item-xl-3   →  is-xl-6    (quarter width)
```

### Files Updated (13 PHP templates)

- `category-committed.php`, `category-foreign-agent.php`, `category.php`
- `footer.php`, `page-jobs.php`, `page-newsletters.php`, `page__text-copy.php`
- `partials/bottom-bar/cookie-notice.php`, `partials/bottom-bar/support-bar.php`
- `specials/taxonomy-focus-breaking-britain.php`
- And others...

### Removed Files

- `src/styl/grid-functions.styl` (61 lines) - Old grid mixin definitions

---

## 2. Margin & Padding Class Migration

### What Changed

Replaced gutter-derived spacing classes with nm-stylus-library numeric spacing helpers.

### Class Mapping

| Old Class             | New Class | Value        |
| --------------------- | --------- | ------------ |
| `margin-top-tiny`     | `mt-2`    | 0.5rem       |
| `margin-top-small`    | `mt-4`    | 1rem         |
| `margin-top-basic`    | `mt-5`    | 1.5rem       |
| `margin-top-mid`      | `mt-6`    | 2rem         |
| `margin-top-large`    | `mt-8`    | 3rem         |
| `margin-bottom-tiny`  | `mb-2`    | 0.5rem       |
| `margin-bottom-small` | `mb-4`    | 1rem         |
| `margin-bottom-basic` | `mb-5`    | 1.5rem       |
| `margin-bottom-mid`   | `mb-6`    | 2rem         |
| `margin-bottom-large` | `mb-8`    | 3rem         |
| `padding-top-*`       | `pt-*`    | (same scale) |
| `padding-bottom-*`    | `pb-*`    | (same scale) |

### Mobile-Specific Classes

| Old Class                    | New Class |
| ---------------------------- | --------- |
| `mobile-margin-top-none`     | `mt-s-0`  |
| `mobile-margin-bottom-none`  | `mb-s-0`  |
| `mobile-padding-top-none`    | `pt-s-0`  |
| `mobile-padding-bottom-none` | `pb-s-0`  |

### Files Updated (25+ PHP templates)

Including: `404.php`, `footer.php`, `page-about.php`, `page-jobs.php`, all `partials/` files, etc.

### CSS Removed

- ~120 margin/padding class definitions from `site.styl`
- ~600 lines of responsive margin/padding overrides from `responsive/*.styl`

---

## 3. Stylus Variable Deprecation

### Color Variables

All Stylus `$color-*` variables replaced with CSS custom properties from nm-stylus-library:

| Old (Stylus)        | New (CSS Custom Property) |
| ------------------- | ------------------------- |
| `$color-blue`       | `var(--color-blue)`       |
| `$color-black-soft` | `var(--color-black-soft)` |
| `$color-white-soft` | `var(--color-white-soft)` |
| `$color-red`        | `var(--color-red)`        |
| `$color-gray-base`  | `var(--color-gray-base)`  |

### Spacing Variables Removed

- `$margin-tiny`, `$margin-small`, `$margin-basic`, `$margin-mid`, `$margin-large`
- `$gutter` (was used to derive margin/padding classes)

### Grid Variables Removed

All legacy grid variables removed - nm-stylus-library's `grid-maker()` uses its own parameters:

- `$col` (was 42px/32px/25px/19px per breakpoint) - **REMOVED**
- `$cols = 24` - **REMOVED**
- `$gutter = 16px` - **REMOVED**

---

## 4. Responsive File Consolidation

### Before vs After Line Counts

| File                 | Before     | After    | Reduction |
| -------------------- | ---------- | -------- | --------- |
| `responsive/xl.styl` | ~175 lines | 15 lines | -91%      |
| `responsive/l.styl`  | ~175 lines | 25 lines | -86%      |
| `responsive/m.styl`  | ~190 lines | 23 lines | -88%      |
| `responsive/s.styl`  | ~250 lines | 59 lines | -76%      |

### What Remains in Responsive Files

Only truly breakpoint-specific values:

- `html` font-size
- Support form widths (percentage varies per breakpoint)
- Support page hero padding (m breakpoint only)
- Mobile-only display toggles (s breakpoint only)

---

## 5. nm-stylus-library Bug: Unresolved `$margin-tiny`

### The Bug

The nm-stylus-library (`node_modules/nm-stylus-library/modules/typography.styl` line 201) references an undefined Stylus variable:

```stylus
.wp-caption-text, .wp-element-caption
  text-align: left
  margin-top: $margin-tiny  // ← Undefined variable
```

### Impact

This compiles to literal text in the output CSS:

```css
.page-copy .wp-caption .wp-caption-text,
.text-copy .wp-caption .wp-caption-text {
  margin-top: $margin-tiny; /* Invalid CSS - browser ignores */
}
```

### Workaround Applied

Added override in `site.styl` that comes after the library import:

```stylus
.text-copy, .page-copy
  .wp-caption
    .wp-caption-text
      margin-top: 0.5rem
```

This successfully overrides the broken library output.

### Recommended Fix

The nm-stylus-library should either:

1. Define `$margin-tiny` internally, or
2. Replace with CSS custom property: `var(--spacing-2)` or similar

---

## 6. site.styl Modular Refactor

### New File Structure

site.styl refactored from ~370 lines to ~105 lines (mostly imports).

**New Components:**

```
src/styl/components/
├── spinner.styl (29 lines)
└── quote.styl (15 lines)
```

**New Layouts:**

```
src/styl/layouts/
├── archives.styl (19 lines)
├── bottom-bar.styl (54 lines)
├── post-indexes.styl (10 lines)
└── single.styl (16 lines)
```

**New Pages:**

```
src/styl/pages/
├── about.styl (6 lines)
├── novara-fm-archive.styl (moved from layouts/)
└── support-page.styl (moved from layouts/)
```

### Removed (Obsolete)

- `.button` class (replaced by `ui-button` from library)
- `.support-form-holder`, `.support-form-submit`, `.support-form-slider` (unused)

---

## 7. Analytics.js Cleanup

### What Changed

- Fixed `#menu-toggle` selector → `.site-header__nav-toggle`
- Removed dead `.support-form-slider` binding (element no longer exists)
- Removed custom `debounce()` function (use lodash instead)
- Kept valid `.related-posts .post` click tracking

### File Size

56 lines → 27 lines

---

## 8. Future Work Notes

### Container Width Variables

Container widths are currently hardcoded. Future library update should add:

```stylus
$container-width-xxl: 1400px
$container-width-xl: 1056px
$container-width-l: 888px
$container-width-m: 744px
$container-width-s: 98% (max-width: 460px)
```

---

## 9. Other Cleanups

### Removed (from earlier commits in this PR)

- Kuoto Swiss font references
- Duplicate grid function definitions
- Uncomplicated color variable remnants
- Newsletter signup block (moved/deprecated)
- Various unused image assets

### Build Output

- CSS bundle reduced significantly
- JS bundle reduced from ~50k lines to minimal
- Build succeeds with only performance warnings (unrelated to this work)

---

## Commit History

1. `b07c25f` - Remove library grid function dupe
2. `4b7994b` - Remove kuoto swiss
3. `031f0de` - Remove uncomplicated old color vars
4. `f691505` - Initial plan
5. `6aae85f` - Replace old grid system with nm-stylus-library grid
6. `09e69e1` - Correct migrate classes (fix column doubling)
7. `e2d0dbb` - Merge pull request #403
8. `3ea6224` - Migrate all old margins and padding
9. `78368d8` - Reduce responsive style dups
10. `7d5e7a1` - Move things to upstreaming location
11. `32c10d3` - Remove legacy grid variables and redundant styles
12. `29fa117` - Refactor site.styl into modular component and layout files
13. _(pending)_ - Clean up Analytics.js, move page styles, update docs

---

## Testing Checklist

- [ ] Grid layouts render correctly at all breakpoints (xxl, xl, l, m, s)
- [ ] Spacing (margins/padding) appears correct throughout site
- [ ] Support form displays properly
- [ ] Support bar and cookie notice functional
- [ ] Category archives (committed, foreign-agent) display correctly
- [ ] Single post layouts intact
- [ ] Caption text under images has proper margin
- [ ] No visual regressions on front page
