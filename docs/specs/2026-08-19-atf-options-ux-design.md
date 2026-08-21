# Above-the-Fold Options — Editor UX Design

**Date:** 2026-08-19
**Status:** Agreed design, pre-implementation
**Scope:** The "Above the Fold: Featured" options page (`nm_above_the_fold_featured_options_page`), plus reusable pieces other Front Page options subpages can adopt later.

## Problem

The Above the Fold: Featured admin page is eight `post_search_text` fields whose
visible value is a raw post ID. To editors it reads as a page of numbers:
scary to the unfamiliar, and mistakes (wrong ID, deleted post, the same post in
two zones, a post that later got unpublished) are invisible until they hit the
live front page. The front-page Layout editor solved this class of problem for
section ordering; this design does the same for the featured-post zones.

## Decisions (agreed 2026-08-19)

1. **Fork the picker.** `webdevstudios/cmb2-post-search-field` v0.2.5 is dead
   upstream (last commit 2019-05-06, latest tag is the version we vendor, 15
   open issues, 0 PRs). Vendoring it buys nothing; owning it lets us improve
   it. Fork into `lib/meta/` as an NM Fork, same precedent as
   `cmb2-validation.php`.
2. **Title-only hint at each picker.** No thumbnails, no status pills, no
   duplicate warnings at field level — those live in the preview module. The
   hint is a native feature of the forked field, not a bolt-on.
3. **A mock-layout preview module at the top of the page** carries the
   intelligence: zone map, edit-state colour coding, collision detection,
   click-to-field.
4. **Colour grammar is edit-state, not post-status.** A loaded page shows a
   neutral baseline; colour appears only when something needs attention or
   changed this session (full grammar below).
5. **Red means "won't work publicly".** Unresolvable ID, deleted post, or a
   post that exists but is not published (draft/scheduled/trashed) — the front
   end has no status guard, so a non-published featured ID renders publicly
   and links visitors to a 404. One mental model: red zone = broken on the
   live site.

## Components

### 1. NM fork of the post-search field

- Copy `vendor/webdevstudios/cmb2-post-search-field/lib/init.php` →
  `lib/meta/cmb2-post-search-field.php`. NM Fork docblock header (plugin-style,
  like `cmb2-validation.php`): upstream link, upstream version forked from
  (0.2.5), fresh version `1.0.0`.
- `functions.php`: require the lib copy instead of the vendor path.
- Remove `webdevstudios/cmb2-post-search-field` from `composer.json` and
  `composer.lock`; delete the vendor directory. Same PR — no window where both
  load (the class is guarded by a `class_exists` check regardless).
- Storage format unchanged: the field keeps writing post IDs to the same
  option keys. No data migration.
- Fork improvements in scope now:
  - Render a title-hint element as part of the field markup (component 3).
- The find-posts modal already fires `change` on the input after writing a
  selection (`handleSelected()` ends with `.val( checked ).trigger( 'change' )`
  — verified against source 2026-08-20), so the resolver simply listens for
  it; no fork surgery needed there.
- Anything else stays as-is until a need arises.

### 2. REST endpoint: `nm/v1/resolve-posts`

- `GET /wp-json/nm/v1/resolve-posts?ids=1,2,3` (comma list, capped at ~20 ids).
- Response per id:
  `{ id, found: bool, title, status, date, thumbnail: url|null }`
  — `found: false` for ids that resolve to nothing. `thumbnail` is a small
  admin-appropriate size (e.g. `thumbnail`), needed only by preview zones that
  show images.
- `permission_callback`: `current_user_can( 'edit_posts' )`. Standard
  `X-WP-Nonce` cookie auth (`wp_create_nonce( 'wp_rest' )` localized alongside
  the script). That gate only decides who can hit the endpoint at all —
  per-post visibility is narrower: a resolved post's metadata is returned only
  if it's published, or the current user can `edit_post` that specific post;
  otherwise it comes back as the `found: false` shape. This mirrors what the
  user can already see in wp-admin (e.g. a Contributor can't see another
  user's draft there either), so the endpoint never reveals more than that.
- Registered from a new `lib/admin/post-resolve.php`. Response is
  read-only metadata about posts the user can already see in the admin — no
  content bodies.

### 3. Title hint at each picker (phase 1 UX)

- The forked field renders `<span class="nm-post-search-title">` after the
  input. Server-fills it for the saved value at render time (no flash of
  unresolved IDs on load).
- The fork's own hint JS (`lib/meta/js/cmb2-post-search-field-hints.js`,
  plain enqueued file — no webpack) updates the hint on input change / modal
  pick, debounced ~300ms, batched through the shared endpoint client
  (`lib/admin/js/post-resolve.js`, registered by `post-resolve.php`).
- States: resolved → title text; unresolvable or non-published → red text
  (e.g. "No published post with this ID").
- Enqueued only on screens that contain the field (the fork knows when it
  rendered; enqueue from its render hook).
- Reusable by construction: every `post_search_text` field everywhere gets the
  hint for free once the fork lands — post meta boxes included. That is
  acceptable; the hint is small and harmless.

### 4. ATF preview module (phase 2 UX)

**Injection.** `cmb2_before_form` hook filtered to the
`nm_above_the_fold_featured_options_page` box (mechanism family as the
validator's `cmb2_after_form`). PHP renders the skeleton + saved-state data
inline; page-specific JS (`lib/admin/js/atf-preview.js`) keeps it live.

**Structure** mirrors `partials/front-page/above-the-fold.php`'s real grid:

```
┌─ ABOVE THE FOLD (click a zone to edit it) ───────────────────┐
│ Featured block 1        │ Latest      │ Featured block 2     │
│ ▓ thumb + title (large) │ (greyed:    │ ▓ thumb + title      │
│ ▓ thumb + title (med)   │ automatic — │ ▓ thumb + title      │
│ · title line            │ latest News)│ · title line         │
│ · title line            │             │ · title line         │
└──────────────────────────────────────────────────────────────┘
```

- Primary and 2nd zones show thumbnail + title (they render thumbnails on the
  front end); 3rd/4th zones are title-only lines (they don't).
- Latest-articles column is static grey — it is query-driven, not editable
  here.
- Small text badges under each block's primary zone reflect its option fields:
  "See Also ✓", "More On: <section>", "Product-linked ✓", "Video embed ✓".

**Colour grammar (edit-state):**

| State | Rendering |
|---|---|
| Saved value, untouched this session | Neutral — page load shows no colour at all |
| Value changed since page load (unsaved) | Green |
| Empty zone | Dashed grey outline |
| Broken — ID unresolvable, deleted, or not published | Red |
| Collision — same ID in two zones | Both zones amber + note strip naming them ("Same post in Featured 1 primary and Featured 2 4th") |

Precedence when states overlap: red > amber > green. (A changed-but-broken
zone is red; a changed-and-colliding pair is amber; green only when the new
value is valid and unique.)

**Interaction.** Click a zone → smooth-scroll to its field row, focus the
input, flash-highlight the row. No editing inside the preview — it is a map,
not a second form.

**Liveness.** Any watched field change (pickers, checkboxes, selects)
re-renders the affected zones from the same debounced batch resolve. Baseline
values are captured at page load for the changed/unchanged comparison.

## Failure handling

- Endpoint unreachable/error: hints render nothing; preview shows a single
  "Couldn't load post data — preview may be stale" banner. Never blocks form
  interaction or saving.
- Everything is display-only. The CMB2 save path is untouched; the feature can
  fail completely with zero risk to stored options.

## Out of scope (follow-ups, not this build)

- **Front-end status guard** — the real fix for non-published featured posts
  is for the featured partials to skip them at render. Separate issue, filed
  independently; red-zone signalling here is the editor-side mitigation.
- Applying the preview module to other options pages (Highlight section). The
  resolver + hint arrive everywhere with the fork; a second preview is a new,
  smaller design if wanted.
- Live preview of the Latest column, drag-and-drop between zones, WYSIWYG
  fidelity.

## Testing

Manual, on DevKinsta (no admin-side Cypress):

1. Load page with saved values → neutral baseline, titles shown at pickers and
   zones.
2. Pick a new post via modal → hint + zone update, zone green.
3. Type a garbage ID → red hint, red zone.
4. Set same post in two zones across blocks → both amber + note strip.
5. Clear a field → dashed zone.
6. Save → reload shows new neutral baseline.
7. Feature a draft post → red zone (and confirm it did render publicly before
   the front-end guard follow-up lands — evidence for that issue).
8. Click every zone → correct field focused.
9. Regression: post meta boxes using `post_search_text` (posts, About page)
   still search, select, and save; hint appears and is unobtrusive.
10. Endpoint blocked (network tab) → banner, form still saves.

## Rollout

- **PR 1 — fork + resolve + hints:** fork the field into `lib/meta/`, remove
  the composer dep and vendor copy, add the endpoint and shared resolver JS,
  native title hints. Ships value on every `post_search_text` field.
- **PR 2 — ATF preview module:** skeleton render, colour grammar, collisions,
  click-to-field.

Both PRs are display-only admin changes: no dist/webpack impact, no data
migrations, normal release train (no hotfix needed).
