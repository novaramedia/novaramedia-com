# Conditional required meta validation + publish-only gating

**Date:** 2026-07-16
**Branch:** `fix/required-post-meta-validation`
**Status:** Implemented (this branch)
**Builds on:** required post meta validation feature (#567) already on this branch

## Problem

1. YouTube ID and Soundcloud URL meta fields are labelled "Required!" but nothing
   enforces them. They are only required when the post is a video or audio post
   respectively — i.e. requirement is conditional on the post's category.
2. The existing required validation fires on every `#post` form submit,
   including Save Draft. Editors often create drafts before assets (video
   upload, audio embed) exist, so draft saves must not be blocked.

## Decisions

- **Conditional requirement via category checkboxes, client-side.** The
  validator already runs client-side at submit; the category checklist
  (`#categorychecklist`) is in the same form, so checked state is readable at
  submit time regardless of post status. No server round-trip.
- **Descendant categories count.** `single.php` dispatches templates by the
  post's *top-level* category, so a post ticked only in a child of `video`
  still renders as a video post. The conditional check therefore matches the
  target category **or any of its descendants**.
- **Slugs in markup, IDs resolved at render time.** The field attribute
  carries the category *slug* (`"video"`), not term IDs. The validator emits a
  slug → term-IDs map (self + descendants) into its inline script when it
  prints. Term IDs drift between installs (local / staging / prod); slugs
  don't. Metabox definitions stay static and environment-agnostic.
- **"Post must have a category" validation is out of scope.** The validator
  only inspects CMB2 meta fields carrying `data-validation` attributes;
  category presence is separate functionality (and WP auto-assigns
  Uncategorized regardless). Future issue if editorial wants it.
- **Drafts save freely; publish validates everything.** All required
  validation (existing standfirst / short description, new conditional fields)
  and the word-length check are skipped for Save Draft and Preview submits,
  and enforced for Publish / Schedule / Update / Submit for Review.
- **Server-side `save_post` validation rejected for now** — robust against
  JS-off but a whole different mechanism (revert-to-draft + admin notice),
  inconsistent with the existing client-side feature. Possible future
  hardening.
- **Label-text matching rejected** — fragile against category renames.
- **Term IDs in field attributes rejected** — IDs drift across installs;
  slugs in markup + render-time map instead (see below).

## Design

### 1. New attribute: `data-validation-required-category`

Category **slug** (e.g. `"video"`). Field is treated as required **only when**
the named category — or any of its descendants — has a checked box in
`#categorychecklist` (checkbox id pattern `in-category-{id}`).

Set on fields via CMB2 `attributes` (both target fields are plain
`text` / `text_url` types, which render `attributes` fine — no wysiwyg
`editor_class` bridge needed).

### 2. PHP: `meta-boxes-post.php`

Static attribute values only — no ID resolution here:

```php
'attributes' => array(
  'data-validation'                   => 'true',
  'data-validation-required-category' => 'video',
),
```

YouTube ID field (`_cmb_utube`) uses `'video'`; Soundcloud URL (`_cmb_sc`)
uses `'audio'`.

### 2b. PHP: slug → term-IDs map in `cmb2-validation.php`

When the validator script prints, emit a JSON map of every category slug to
its term IDs (self + `get_term_children`):

```js
const categoryMap = { "video": [12, 34], "audio": [56], ... };
```

Built from `get_categories( array( 'hide_empty' => false ) )`, output via
`wp_json_encode()`. Category tree on this site is small; payload negligible.
Slug referenced by a field but missing from the map (category deleted) →
field not required. Safe fail.

### 3. JS: `cmb2-validation.php` fork

**Publish-only gate** at top of `checkValidation`:

```js
const submitter = event.originalEvent && event.originalEvent.submitter;
if ( submitter && submitter.id === 'save-post' ) return;      // Save Draft
if ( $( '#wp-preview' ).val() === 'dopreview' ) return;       // Preview
```

- `submitter` undefined (older browser, programmatic submit) → falls through
  to validating. Safe default = current behaviour.
- Options page forms (Links Bar, fundraising) have no `#save-post` /
  `#wp-preview`; always validate, unchanged.

**Conditional required check** inside the field loop:

- If `validation-required-category` data present: look the slug up in
  `categoryMap`; required ⇔ any mapped ID's `#in-category-{id}` checkbox is
  checked. Slug not in map, or no category checklist in DOM (options pages) →
  not required.
- Else: existing `validation-required === true` behaviour unchanged.
- Reuses `is_empty_value()`, row highlight, alert, scroll-to-error.

**Version bump:** fork 0.3.2 → 0.4.0 (two new behaviours). Update docblock
with the new attribute and the publish-only rule.

## Edge cases

| Case | Behaviour |
|---|---|
| Save Draft with empty required fields | Saves, no validation |
| Preview draft | No validation |
| Publish video post, empty YouTube ID | Blocked |
| Publish article post, empty YouTube ID | Saves — field not required |
| Post in child category of `audio` only | Soundcloud URL required |
| Update already-published post | Validates (post is live) |
| Submit for Review (contributor) | Validates (one step from publish) |
| `video`/`audio` category missing from site | Slug absent from map, field optional |
| Term IDs differ on staging/local | Irrelevant — map built per-environment at render |
| Browser without SubmitEvent.submitter | Validates on all submits (old behaviour) |

## Testing (manual, local)

1. Draft save with everything empty → saves freely.
2. Publish article post, empty YouTube ID → publishes.
3. Publish video post, empty YouTube ID → blocked, row highlighted, alert.
4. Publish post ticked only in child category of audio, empty Soundcloud URL → blocked.
5. Preview a draft with empty required fields → preview opens.
6. Publish with empty standfirst → still blocked (existing behaviour intact).
7. Links Bar options page save with invalid field → still blocked (gate
   doesn't leak into options forms).
