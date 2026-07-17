# Block editor meta validation — shared-core refactor

**Date:** 2026-07-17
**Status:** Implemented (this branch)
**Builds on:** `docs/specs/2026-07-16-conditional-required-meta-validation-design.md`

## Problem

The required-meta validator (PR #568) is classic editor only. The block editor
saves posts via the REST API and never fires a native form submit, so the
validator's submit handler never binds — posts publish there with **no meta
validation and no error**. The site's Classic Editor plugin runs with "allow
users to switch editors" enabled, so the bypass is reachable today, and block
editor usage is expected to grow.

A server-side gate can't fix it alone: Gutenberg persists the post via REST
*first* and POSTs legacy metabox values in a second request
(`meta-box-loader=1`), so at REST-save time the incoming meta isn't visible.
A client-side block editor path is required.

## Decisions

- **Follow-up PR, not part of #568.** #568 ships classic-only with the
  limitation documented; this work gets its own review cycle.
- **Live lock UX in the block editor** (not alert-parity, not advisory
  pre-publish warnings). Gutenberg has no cancellable submit; the supported
  gating mechanism is `lockPostSaving()`. Alert-interception was rejected as
  fragile; advisory-only was rejected as weaker than classic behaviour.
- **Shared-core refactor** (not a parallel standalone module). Rule logic is
  written once and consumed by two thin adapters. Accepts re-testing the
  classic path — it's a rewrite of *delivery* (inline print → enqueued
  bundle), not of rules.
- **Separate admin webpack config on `@wordpress/scripts`** (not new entries
  in the theme config). Follows the existing `webpack.blocks.config.js`
  precedent ("exists to avoid conflicts with the theme's main
  webpack.config.js"). Keeps admin tooling decoupled from the prod front-end
  build, and wp-scripts' dependency extraction automatically externalises
  `@wordpress/data` / `@wordpress/notices` to `wp.*` globals and emits
  `.asset.php` files (dependency list + cache-busting version) for
  `wp_enqueue_script`. Also establishes a home for future admin-only JS
  (front page layout tooling, editor enhancements, metabox UX) instead of
  inline-printing from PHP — which is how `cmb2-validation.php` grew in the
  first place.

## Design

### 1. Build

```
webpack.admin.config.js   — extends @wordpress/scripts default config;
                            entries below, output to dist/admin/
src/admin/                — admin-only JS home
  meta-validation/
    core.js               — pure rules
    dom.js                — shared DOM helpers
    classic.js            — classic editor adapter (entry)
    block-editor.js       — block editor adapter (entry)
dist/admin/               — built bundles + .asset.php files (committed,
                            per repo dist rule)
```

npm scripts: `build:admin`, `dev:admin`; `build` chains theme + blocks +
admin. **No changes to the existing `webpack.config.js`.**

### 2. Modules

- **`core.js`** — no DOM, no `wp.*` globals. `isEmptyValue(value)` (current
  DOMParser whitespace/markup-empty semantics), `countWords(value)`,
  `validateField(value, rules, activeCategoryIds, categoryMap)` →
  `{ valid, reason }`. `rules` = parsed `data-validation*` attribute set.
- **`dom.js`** — scan `[data-validation]` fields including the
  `nm-validation-required` wysiwyg `editor_class` bridge; read a field's
  current value (`tinyMCE.triggerSave()` first when present); row highlight
  add / remove / clear-all (same `background-color` visual as today); field
  label lookup (`.cmb-th label`).
- **`classic.js`** — behaviour identical to the current inline script: form
  detection (`#post`, `#nm_secondary_options_page`, `#nm_fundraising_options`),
  submit handler, Save Draft (`SubmitEvent.submitter` id `save-post`) and
  Preview (`#wp-preview` = `dopreview`) gating with stale-highlight clearing,
  `alert()` + scroll-to-first-error.
- **`block-editor.js`** — see §4.

### 3. PHP changes

`lib/meta/cmb2-validation.php` is **renamed to `lib/meta/meta-validation.php`**
and shrinks to a loader (~50–70 lines):

- The fake plugin header goes. It was a vestige of the copied CMB2 wiki
  snippet — this is theme lib code, not a plugin — and once the JS lives in
  `src/admin/` the "NM Fork: CMB2 js validation" framing is false. Normal
  file docblock instead; fork version numbering (0.4.0 etc.) ends with it —
  git history covers change tracking, as for every other lib file.
- Keeps the category slug → term-IDs map builder (unchanged, post edit
  screens only).
- Drops all inline JS.
- Enqueues `dist/admin/meta-validation-classic.js` for classic screens via
  the `cmb2_after_form` hook (same only-where-a-CMB2-form-renders semantics
  as the old inline print, and no need to enumerate options-page screen ids);
  enqueues `dist/admin/meta-validation-block-editor.js` on
  `enqueue_block_editor_assets`. Both read deps/version from their
  `.asset.php` and get `categoryMap` via `wp_localize_script`.
- `functions.php:130` include path updated
  (`get_template_part( 'lib/meta/meta-validation' )`).

`lib/meta/meta-boxes-post.php` untouched — the same `data-validation*`
attributes and `editor_class` marker drive both paths.

### 4. Block editor adapter

- **Field discovery:** same DOM scan as classic — CMB2 metaboxes render in
  Gutenberg's metabox panel with attributes intact.
- **Live re-validation triggers:** delegated `input`/`change` listeners on
  the metabox container, plus `wp.data.subscribe` for category changes —
  `select('core/editor').getEditedPostAttribute('categories')` returns term
  IDs directly (no checkbox DOM to match against).
- **Lock condition:** fields invalid **AND** (saved post status is
  publish/future/private **OR** publish sidebar is open **OR** edited status
  is publish/future/private). Consequences:
  - Drafts save and preview freely (lock never engages while edited status
    is draft and the publish sidebar is closed).
  - Published/scheduled posts gate every save — matches classic Update
    behaviour.
  - "Switch to draft" unlocks: the action changes the edited status to
    draft, the subscriber re-evaluates, save proceeds.
- **On lock:** `dispatch('core/editor').lockPostSaving('nm-meta-validation')`
  + one persistent error notice (`core/notices`, fixed id so it replaces in
  place) listing each failing field label + reason, + red row highlight on
  the failing metabox rows.
- **On valid:** `unlockPostSaving`, remove the notice, clear highlights.

### 5. Known limitation

If a user has disabled pre-publish checks in their editor preferences, the
first publish of a draft skips the pre-publish sidebar, and the
status-change → subscriber → lock sequence may fire too late to stop that
save. Prefs-dependent and rare; documented, not solved client-side.
Server-side hardening (revert-to-draft + admin notice) remains a possible
future layer — rejected for now in the parent spec and unchanged here.

## Edge cases

| Case | Behaviour |
|---|---|
| Classic editor, all 7 cases from parent spec | Unchanged (regression matrix) |
| Block editor: draft save with empty required fields | Saves freely, no lock |
| Block editor: publish flow, empty required field | Publish button locked, error notice + row highlight |
| Block editor: fix the field while locked | Unlocks live, notice clears |
| Block editor: untick conditional category while locked | Unlocks live (categories from wp.data) |
| Block editor: update published post, empty required field | Locked until fixed |
| Block editor: switch published post to draft while locked | Unlocks, save proceeds |
| Block editor: pre-publish checks disabled in prefs | First-publish race — documented limitation |
| Options pages (Links Bar, fundraising) | Classic adapter only, unchanged |

## Testing

Manual matrix above, both editors, local. `core.js` is pure and
unit-testable, but the theme has no JS unit runner (Cypress only) — adding
one is out of scope; the module boundary keeps the option open.

Build verification: `npm run build`, commit `dist/admin/` (repo dist rule).

## Approvals needed before implementation

- New `webpack.admin.config.js` + npm scripts = build tooling addition —
  **requires team approval per CLAUDE.md**, even though the existing
  `webpack.config.js` is untouched.

## Out of scope

- Format validation (URL patterns, YouTube ID shape) — separate design, see
  `docs/specs/2026-07-17-meta-field-format-validation-design.md`.
- Server-side validation layer.
- Migrating these fields off CMB2 metaboxes to native Gutenberg sidebar
  panels (`register_post_meta` + `PluginDocumentSettingPanel`) — the
  long-term Gutenberg-native answer, but a rewrite of field rendering and
  save paths for both editors.
- "Post must have a category" validation.
