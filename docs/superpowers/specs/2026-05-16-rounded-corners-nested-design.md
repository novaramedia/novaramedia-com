# Rounded Corners — Nested Class & Deprecations

Date: 2026-05-16
Task: Bug – rounded corners uneven

## Problem

Two rounded-corner helper classes exist in `nm-stylus-library` with identical default CSS:

- `ui-rounded-box` — used on color-blocked divs
- `ui-rounded-image` — used on images/thumbnails

Both apply `border-radius: var(--corner-radius)` (2px). The element-type distinction is misleading and unnecessary.

A `--large` modifier exists (`--corner-radius-large`, 4px) but has no semantic meaning. It is currently misused on the support form outer element, which should be 2px.

No class exists to express the correct visual rule: **inner color-blocked boxes need a larger radius than outer ones** to look optically parallel with the outer corners.

## Visual Rule

- Outer color-blocked box → `ui-rounded-box` (2px)
- Inner color-blocked box nested inside another `ui-rounded-box` → `ui-rounded-box--nested` (4px)
- Images/thumbnails → `ui-rounded-box` (2px), same as outer boxes

The eye reads nested corners relative to the outer corner. At 2px outer, the inner corners need ~4px to appear consistent rather than sharper than the container.

## Changes

### 1. `src/styl/upstream-to-library.styl` — new classes + deprecation notes

Add `ui-rounded-box--nested` with documentation explaining the visual rule.

Add deprecation comments for:
- `ui-rounded-image` — use `ui-rounded-box` instead
- `ui-rounded-box--large` — use `ui-rounded-box--nested` instead

No CSS removed from this file (library classes still compile from `nm-stylus-library`). Deprecations are upstream flags only.

### 2. Template swap — `ui-rounded-image` → `ui-rounded-box`

16 usages across 14 files. Pure class rename, no visual change.

Files:
- `partials/front-page/above-the-fold/featured-post-primary.php`
- `partials/front-page/above-the-fold/featured-post-secondary.php`
- `partials/front-page/above-the-fold/latest-article.php` (×2)
- `partials/front-page/mega-block.php`
- `partials/front-page/products-bar.php` (×2)
- `partials/front-page/show-blocks/novara-live.php` (×2)
- `partials/front-page/show-blocks/audio.php`
- `partials/front-page/show-blocks/downstream.php` (×2)
- `partials/post-layouts/archive-event.php`
- `partials/post-layouts/archive-post.php` (×2 + 1 on div.u-video-embed-container)
- `partials/singles/articles/articles-header-basic.php`
- `partials/singles/articles/articles-header-large-image.php`
- `partials/singles/single-post-video.php`
- `src/blocks/related-post/render.php` (×2)

### 3. Apply `--nested` where needed

**`partials/front-page/show-blocks/audio.php:43`**
White box inside colored `ui-rounded-box` → add `ui-rounded-box--nested`.

No other templates have confirmed nested color-blocked boxes after audit. `audio.php` is the only triple-nested case.

Note: `support-form.styl` schedule tab buttons use hardcoded `--corner-radius-large` with asymmetric rounding (top corners only). These are not part of the class system and are left as-is.

### 4. Fix support form bug

**`lib/renderers.php:307`**

```php
// Before
<form class="support-form background-red font-color-white ui-rounded-box ui-rounded-box--large" ...>

// After
<form class="support-form background-red font-color-white ui-rounded-box" ...>
```

The support form is an outer color-blocked box. `--large` (4px) was incorrect — it is not nested. Remove `ui-rounded-box--large`.

## Out of Scope

- Modifying `nm-stylus-library` package directly (changes go via `upstream-to-library.styl`)
- Converting `support-form.styl` asymmetric radius values to the class system
- Removing `ui-rounded-image` or `--large` from the library CSS (deprecation only, not deletion)

## Upstream Flags

When upstreaming to `nm-stylus-library`:
1. Add `ui-rounded-box--nested` as a modifier
2. Deprecate/remove `ui-rounded-box--large`
3. Deprecate/remove `ui-rounded-image` (merged into `ui-rounded-box`)
