# ATF Options UX — PR 1: Picker Fork + Resolve Endpoint + Title Hints

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Own the CMB2 post-search picker as an NM fork and make every picker show the resolved post title (red when the ID won't work publicly), backed by a new `nm/v1/resolve-posts` REST endpoint.

**Architecture:** Three units. (1) `lib/meta/cmb2-post-search-field.php` — verbatim fork of the dead vendored addon, plus a server-rendered title hint in the field markup and its own hint UI in `lib/meta/js/cmb2-post-search-field-hints.js`. (2) `lib/admin/post-resolve.php` — standalone utility: `nm_resolve_post()` helper + REST route + the registered `nm-post-resolve` browser client (`lib/admin/js/post-resolve.js`, plain enqueued file, NO webpack). (3) Consumers implement against the utility: the field's hints JS here, the PR 2 preview later. *(Restructured post-review — see addendum at the end; the executed steps below predate it and name the original file layout.)*

**Tech Stack:** WordPress/CMB2 (classic admin), jQuery (already enqueued by the field), WP REST API, Composer (dependency removal only).

**Spec:** `docs/specs/2026-08-19-atf-options-ux-design.md`

## Global Constraints

- Production-quality only; no build-system (webpack) changes — the JS file is enqueued raw, never bundled.
- Storage format unchanged: pickers keep writing comma-separated post IDs to the same option/meta keys. No data migration.
- Display-only feature: the CMB2 save path must be untouched; JS failure must never block form interaction or saving.
- `post_search_text` is used with multi-ID values elsewhere (post meta `_cmb_contributors`, `_cmb_related_posts` use `select_behavior => 'add'`): every hint codepath must handle comma lists, not just single IDs.
- Red rule (spec decision 5): red = won't work publicly = ID unresolvable **or** post status ≠ `publish`.
- No i18n wrappers on new user-facing strings (matches `cmb2-validation.php` precedent — plain English strings).
- Verification is `php -l` / `node --check` plus manual browser checks on the local DevKinsta site (no PHPUnit, no admin Cypress, wp-cli cannot reach the DevKinsta DB from the host).
- Branch: `feature/atf-picker-fork` off `development`; PR targets `development`. Never commit to `development` directly; the user merges PRs.

---

### Task 1: Fork the field into `lib/meta/`, drop the composer dep

**Files:**
- Create: `lib/meta/cmb2-post-search-field.php` (from `vendor/webdevstudios/cmb2-post-search-field/lib/init.php`)
- Modify: `functions.php:159-165` (the `cmb_initialize_cmb_meta_boxes` block) and the `get_template_part` list around `functions.php:131-150`
- Modify: `composer.json`, `composer.lock` (via `composer remove`)
- Delete: `vendor/webdevstudios/` (composer does this)

**Interfaces:**
- Produces: class `CMB2_Post_Search_field` now loaded from the theme, hooks unchanged (`cmb2_render_post_search_text`, `cmb2_after_form`). Later tasks edit `render_field()` and `render_js()` in the fork.

- [ ] **Step 1: Copy the vendor file into the theme**

```bash
cp vendor/webdevstudios/cmb2-post-search-field/lib/init.php lib/meta/cmb2-post-search-field.php
```

- [ ] **Step 2: Edit the fork header and guards**

In `lib/meta/cmb2-post-search-field.php`:

1. Replace the file docblock (lines 1–15 of the copy) with:

```php
<?php
/**
 * Plugin Name: NM Fork: CMB2 Post Search field
 * Description: Custom CMB2 field type `post_search_text` — a text input of post IDs with a find-posts search modal. Forked so we own it: upstream is unmaintained (last commit 2019, latest tag v0.2.5).
 * Version: 1.0.0
 *
 * Fork of webdevstudios/cmb2-post-search-field v0.2.5.
 *
 * @link https://github.com/WebDevStudios/CMB2-Post-Search-field
 */

if ( class_exists( 'CMB2_Post_Search_field' ) ) {
  return; // Another copy already loaded (belt and braces; the vendor copy is removed).
}
```

2. Delete everything after the closing `}` of the class and the `CMB2_Post_Search_field::get_instance();` line — i.e. remove the two `_deprecated_function` back-compat functions and the two `remove_action` calls (nothing in the theme calls them; verified 2026-08-20).

- [ ] **Step 3: Load the fork from functions.php and stop requiring the vendor copy**

In `functions.php`, add to the meta `get_template_part` block (after line 133, next to `cmb2-validation`):

```php
get_template_part( 'lib/meta/cmb2-post-search-field' );
```

And reduce `cmb_initialize_cmb_meta_boxes()` to require only CMB2 core:

```php
function cmb_initialize_cmb_meta_boxes() {
  if ( ! class_exists( 'cmb2_bootstrap_202' ) ) {
    require_once 'vendor/cmb2/cmb2/init.php';
  }
}
```

Note the load-order consequence: the fork now loads at theme-load (before `init`), the vendor copy used to load at `init` priority 11. The class only registers hooks, which fire much later (admin render / AJAX), so earlier is safe.

- [ ] **Step 4: Remove the composer dependency**

```bash
composer remove webdevstudios/cmb2-post-search-field
```

Expected: `composer.json` loses the line, `composer.lock` regenerated, `vendor/webdevstudios/` deleted. If composer touches anything beyond those (e.g. reinstalling other deps at new versions), stop and inspect `git diff composer.lock` — only the removal should be present.

- [ ] **Step 5: Lint and verify on the local site**

```bash
php -l lib/meta/cmb2-post-search-field.php && php -l functions.php
```

Manual, on the DevKinsta site wp-admin:
1. Edit any post → Post Meta box renders, Related Posts search icon opens the modal, picking a post writes its ID and (on save) persists.
2. Front Page → Above the Fold: Featured loads without error, pickers work.

Expected: behaviour identical to before the fork — this task is a pure relocation.

- [ ] **Step 6: Commit**

```bash
git add lib/meta/cmb2-post-search-field.php functions.php composer.json composer.lock vendor
git commit -m "feat: fork cmb2-post-search-field into lib/meta

Upstream is unmaintained (last commit 2019; v0.2.5 is both the latest
tag and the version we vendored). Owning it lets us extend the field.
Verbatim copy plus NM Fork header; deprecated back-compat shims
dropped (unused in theme). Composer dep and vendor copy removed."
```

---

### Task 2: `nm/v1/resolve-posts` endpoint + `nm_resolve_post()` helper

**Files:**
- Create: `lib/admin/post-resolve.php`
- Modify: `functions.php` (one `get_template_part` line)

**Interfaces:**
- Produces: `nm_resolve_post( int $post_id ): array` returning `{ id:int, found:bool, title:?string, status:?string, date:?string, thumbnail:?string }`; REST route `GET nm/v1/resolve-posts?ids=1,2,3` returning a JSON array of those shapes. Task 3 calls `nm_resolve_post()`; Task 4 and PR 2 call the route.

- [ ] **Step 1: Create `lib/admin/post-resolve.php`**

```php
<?php
/**
 * Post-resolve helper + REST endpoint.
 *
 * Resolves post IDs to the display metadata the admin UI needs (title,
 * status, date, thumbnail). Shared by the post-search field's title hints
 * (server render + live JS updates) and the ATF options preview module.
 * Read-only; returns nothing an editor cannot already see in wp-admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Resolve one post ID to hint/preview metadata.
 *
 * 'found' is false only when no post object exists for the ID. A trashed
 * or draft post is found — its status tells the caller it won't display
 * publicly (spec: red = unresolvable OR status !== publish).
 *
 * @param int $post_id Post ID.
 * @return array{id:int, found:bool, title:?string, status:?string, date:?string, thumbnail:?string}
 */
function nm_resolve_post( $post_id ) {
  $post_id = absint( $post_id );
  $post    = get_post( $post_id );

  if ( ! $post ) {
    return array(
      'id'        => $post_id,
      'found'     => false,
      'title'     => null,
      'status'    => null,
      'date'      => null,
      'thumbnail' => null,
    );
  }

  return array(
    'id'        => $post_id,
    'found'     => true,
    'title'     => get_the_title( $post ),
    'status'    => get_post_status( $post ),
    'date'      => get_the_date( 'j M Y', $post ),
    'thumbnail' => get_the_post_thumbnail_url( $post, 'thumbnail' ) ?: null,
  );
}

/**
 * REST callback: resolve a comma list of post IDs (capped at 20).
 *
 * @param WP_REST_Request $request Request with an 'ids' param.
 * @return WP_REST_Response|WP_Error
 */
function nm_resolve_posts_rest( $request ) {
  $ids = array_filter( array_map( 'absint', explode( ',', (string) $request->get_param( 'ids' ) ) ) );
  $ids = array_slice( array_values( array_unique( $ids ) ), 0, 20 );

  if ( ! $ids ) {
    return new WP_Error( 'nm_resolve_no_ids', 'No valid post IDs supplied.', array( 'status' => 400 ) );
  }

  return rest_ensure_response( array_map( 'nm_resolve_post', $ids ) );
}

function nm_register_resolve_posts_route() {
  register_rest_route(
    'nm/v1',
    '/resolve-posts',
    array(
      'methods'             => WP_REST_Server::READABLE,
      'callback'            => 'nm_resolve_posts_rest',
      'permission_callback' => function () {
        return current_user_can( 'edit_posts' );
      },
      'args'                => array(
        'ids' => array(
          'required'          => true,
          'type'              => 'string',
          'sanitize_callback' => 'sanitize_text_field',
        ),
      ),
    )
  );
}
add_action( 'rest_api_init', 'nm_register_resolve_posts_route' );
```

- [ ] **Step 2: Load it from functions.php**

Add ABOVE the meta `get_template_part` block (so `nm_resolve_post()` exists before any field renders):

```php
get_template_part( 'lib/admin/post-resolve' );
```

- [ ] **Step 3: Lint**

```bash
php -l lib/admin/post-resolve.php && php -l functions.php
```

- [ ] **Step 4: Verify the endpoint manually**

Logged in to the DevKinsta site wp-admin, in the browser devtools console (nonce-free cookie auth fails by design, so test through a page that has a REST nonce, or simply):

```js
fetch('/wp-json/nm/v1/resolve-posts?ids=1,999999', { headers: { 'X-WP-Nonce': wpApiSettings ? wpApiSettings.nonce : '' } }).then(r => r.json()).then(console.log)
```

If `wpApiSettings` is absent on the current screen, run the fetch from the block editor (it always has it), or defer full verification to Task 4 where our own localized nonce exists.

Expected: array of two objects — a found one with title/status/date, and `{id: 999999, found: false, ...}`. Logged out (private window): the same URL returns a 401/403 JSON error.

- [ ] **Step 5: Commit**

```bash
git add lib/admin/post-resolve.php functions.php
git commit -m "feat: nm/v1/resolve-posts endpoint and nm_resolve_post helper"
```

---

### Task 3: Server-rendered title hint in the forked field

**Files:**
- Modify: `lib/meta/cmb2-post-search-field.php` (`render_field()`, new `title_hint_html()`, hint CSS in `render_js()`'s `<style>` block)

**Interfaces:**
- Consumes: `nm_resolve_post()` from Task 2.
- Produces: `<span class="nm-post-search-title">…</span>` immediately after every `post_search_text` input; broken segments wrapped in `<span class="nm-post-search-title--broken">`. Task 4's JS re-renders this exact markup client-side, so the class names and the ` · ` separator are contract.

- [ ] **Step 1: Add the hint to `render_field()`**

In the fork, change `render_field()` to echo the hint after the input:

```php
public function render_field( $field, $escaped_value, $object_id, $object_type, $field_type ) {
  echo $field_type->input( array(
    'data-search' => json_encode( array(
      'posttype'   => $field->args( 'post_type' ),
      'selecttype' => 'radio' == $field->args( 'select_type' ) ? 'radio' : 'checkbox',
      'selectbehavior' => 'replace' == $field->args( 'select_behavior' ) ? 'replace' : 'add',
      'errortxt'   => esc_attr( $field_type->_text( 'error_text', __( 'An error has occurred. Please reload the page and try again.' ) ) ),
      'findtxt'    => esc_attr( $field_type->_text( 'find_text', __( 'Find Posts or Pages' ) ) ),
    ) ),
  ) );
  echo $this->title_hint_html( $escaped_value ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts.
}
```

- [ ] **Step 2: Add `title_hint_html()` to the class**

```php
/**
 * Server-rendered title hint for a field value (comma list of post IDs).
 *
 * Mirrors the client-side renderer in lib/admin/js/post-resolve.js —
 * keep the markup contract (classes, ' · ' separator) in sync with it.
 * Red rule per docs/specs/2026-08-19-atf-options-ux-design.md: broken =
 * no post with that ID, or status !== publish (front end has no status
 * guard, so a non-published ID renders publicly and 404s for visitors).
 */
protected function title_hint_html( $value ) {
  $ids   = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
  $parts = array();

  foreach ( $ids as $id ) {
    if ( ! function_exists( 'nm_resolve_post' ) ) {
      break;
    }

    $info = nm_resolve_post( $id );

    if ( ! $info['found'] ) {
      $parts[] = '<span class="nm-post-search-title--broken">No post with ID ' . $id . '</span>';
    } elseif ( 'publish' !== $info['status'] ) {
      $parts[] = '<span class="nm-post-search-title--broken">' . esc_html( $info['title'] ) . ' — ' . esc_html( $info['status'] ) . ', won&#8217;t display publicly</span>';
    } else {
      $parts[] = esc_html( $info['title'] );
    }
  }

  return '<span class="nm-post-search-title">' . implode( ' · ', $parts ) . '</span>';
}
```

Note the span renders even when empty — Task 4's JS fills/clears it in place without creating nodes.

- [ ] **Step 3: Add hint styles**

In `render_js()`, extend the existing `<style>` block:

```css
.nm-post-search-title {
  display: block;
  margin-top: 4px;
  font-style: italic;
  color: #646970;
}
.nm-post-search-title .nm-post-search-title--broken {
  color: #b32d2e;
  font-style: normal;
  font-weight: 600;
}
```

- [ ] **Step 4: Lint and verify**

```bash
php -l lib/meta/cmb2-post-search-field.php
```

Manual on DevKinsta wp-admin:
1. ATF options page: every picker with a saved ID shows its post title in grey italics under the input.
2. Temporarily type a garbage ID into a field and reload after saving on a **test value you then restore** — or easier: check a post edit screen's Related Posts field with a known-deleted ID if one exists. A broken ID renders the red bold message.
3. Contributors field on a post (multi-ID): titles joined with `·`.

- [ ] **Step 5: Commit**

```bash
git add lib/meta/cmb2-post-search-field.php
git commit -m "feat: server-rendered post title hint on post_search_text fields"
```

---

### Task 4: Live hint updates — resolver JS

**Files:**
- Create: `lib/admin/js/post-resolve.js`
- Modify: `lib/meta/cmb2-post-search-field.php` (`render_js()` — enqueue + localize)

**Interfaces:**
- Consumes: REST route from Task 2; hint markup contract from Task 3; the upstream modal already fires `change` on the input after writing a selection (`handleSelected()` — `.val(checked).trigger('change')`), so listening to `change` catches modal picks with no fork surgery.
- Produces: global behaviour only; PR 2's preview JS will reuse the same localized `nmPostResolve` object.

- [ ] **Step 1: Enqueue and localize in `render_js()`**

At the top of `render_js()` in the fork, after the `wp_enqueue_script( 'wp-backbone' );` line:

```php
wp_enqueue_script(
  'nm-post-resolve',
  get_template_directory_uri() . '/lib/admin/js/post-resolve.js',
  array( 'jquery' ),
  '1.0.0',
  true
);
wp_localize_script(
  'nm-post-resolve',
  'nmPostResolve',
  array(
    'endpoint' => esc_url_raw( rest_url( 'nm/v1/resolve-posts' ) ),
    'nonce'    => wp_create_nonce( 'wp_rest' ),
  )
);
```

- [ ] **Step 2: Create `lib/admin/js/post-resolve.js`**

```js
/**
 * Live title hints for post_search_text fields.
 *
 * Re-resolves a field's hint when its value changes (typing or a pick in
 * the find-posts modal, which fires change). Markup contract mirrors
 * title_hint_html() in lib/meta/cmb2-post-search-field.php — keep the
 * classes and the ' · ' separator in sync with it. Display-only: any
 * failure leaves the form fully usable.
 */
jQuery(function ($) {
  'use strict';

  if (typeof nmPostResolve === 'undefined') {
    return;
  }

  var DEBOUNCE_MS = 300;

  function parseIds(value) {
    return String(value)
      .split(',')
      .map(function (part) { return parseInt(part, 10); })
      .filter(function (id) { return id > 0; });
  }

  function hintFor($input) {
    return $input.closest('.cmb-td').find('.nm-post-search-title').first();
  }

  function renderHint($hint, results) {
    var parts = results.map(function (info) {
      if (!info.found) {
        return $('<span>', { 'class': 'nm-post-search-title--broken', text: 'No post with ID ' + info.id });
      }
      if (info.status !== 'publish') {
        return $('<span>', {
          'class': 'nm-post-search-title--broken',
          text: info.title + ' — ' + info.status + ', won’t display publicly'
        });
      }
      return $('<span>', { text: info.title });
    });

    $hint.empty();
    parts.forEach(function ($part, i) {
      if (i > 0) {
        $hint.append(' · ');
      }
      $hint.append($part);
    });
  }

  function resolveInput($input) {
    var $hint = hintFor($input);

    if (!$hint.length) {
      return;
    }

    var ids = parseIds($input.val());

    if (!ids.length) {
      $hint.empty();
      return;
    }

    fetch(nmPostResolve.endpoint + '?ids=' + ids.join(','), {
      headers: { 'X-WP-Nonce': nmPostResolve.nonce },
      credentials: 'same-origin'
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('resolve failed: ' + response.status);
        }
        return response.json();
      })
      .then(function (results) {
        renderHint($hint, results);
      })
      .catch(function () {
        $hint.empty(); // Endpoint down: show nothing rather than stale/false info.
      });
  }

  var timers = {};
  var timerSeq = 0;

  $(document).on('change input', '.cmb-type-post-search-text input[type="text"]', function () {
    var input = this;

    if (!input.dataset.nmResolveTimer) {
      input.dataset.nmResolveTimer = String(++timerSeq);
    }

    var key = input.dataset.nmResolveTimer;
    clearTimeout(timers[key]);
    timers[key] = setTimeout(function () {
      resolveInput($(input));
    }, DEBOUNCE_MS);
  });
});
```

- [ ] **Step 3: Lint**

```bash
php -l lib/meta/cmb2-post-search-field.php && node --check lib/admin/js/post-resolve.js
```

- [ ] **Step 4: Verify manually on DevKinsta wp-admin**

1. ATF options page: pick a new post via the modal → hint updates to the new title without reload.
2. Type a garbage ID (e.g. `999999`) → after ~300ms hint turns red "No post with ID 999999". Don't save.
3. Clear the input → hint empties.
4. Multi-ID field (post Contributors): add a second post via modal → both titles shown.
5. Devtools → Network → block the `resolve-posts` request (or go offline) → typing changes the hint to empty, form still saves normally.
6. Regression: modal search/select/close, Save on the options page, post save — all unchanged.

- [ ] **Step 5: Commit**

```bash
git add lib/admin/js/post-resolve.js lib/meta/cmb2-post-search-field.php
git commit -m "feat: live title hints via nm/v1/resolve-posts"
```

---

### Task 5: Docs sync — CHANGELOG

**Files:**
- Modify: `CHANGELOG.md` (`[Unreleased]`)

**Interfaces:** none — documentation.

(The spec's incorrect claim about the modal not firing `change` was already
corrected in the spec/plan docs PR, 2026-08-20 — nothing to fix here.)

- [ ] **Step 1: CHANGELOG entry**

Under `## [Unreleased]` (create the section if absent, above the latest release heading):

```markdown
### Added

- Post pickers in admin meta boxes and options pages show the resolved post title beneath the ID, live as the value changes — red warning when the ID is missing or the post isn't published

### Changed

- CMB2 Post Search field addon forked into the theme (`lib/meta/`) — upstream unmaintained since 2019; composer dependency removed
```

- [ ] **Step 2: Commit, push, PR**

```bash
git add CHANGELOG.md
git commit -m "docs: changelog for picker fork and title hints"
git push -u origin feature/atf-picker-fork
gh pr create --base development --title "ATF options UX PR 1: picker fork, resolve endpoint, title hints"
```

PR body should summarise the three units and link the spec, `#591` (related front-end guard issue), and note PR 2 (preview module) follows. The user merges.

---

## Self-review notes (completed)

- **Spec coverage:** spec components 1–3 fully covered (fork, endpoint, hints); component 4 (preview) is PR 2 by design; failure handling covered in Task 4 JS catch + global constraint; rollout matches spec's PR split. Spec's "change event" fork improvement dropped — the spec was corrected in the docs PR after reading the source (the modal already fires `change`).
- **Type consistency:** `nm_resolve_post()` return shape identical in Task 2 definition, Task 3 consumption, Task 4 JS field names (`found`, `status`, `title`, `id`). Class names `nm-post-search-title` / `--broken` consistent across Tasks 3 and 4.
- **Placeholder scan:** clean — all steps carry runnable code or exact commands.

---

## Addendum: post-review restructure (2026-08-21)

Structural review found `lib/admin/js/post-resolve.js` was the field type's hint UI (CMB2 selectors, `cmb2_add_row` handling, `title_hint_html()` markup mirror) wearing the utility's name, while the field type also owned registration of the shared script handle. Restructured so the utility is truly standalone and consumers implement against it:

- `lib/admin/post-resolve.php` now also registers the `nm-post-resolve` script handle (endpoint + nonce on the `nmPostResolve` global) on both enqueue hooks — the utility owns its client.
- `lib/admin/js/post-resolve.js` rewritten as that client: `nmPostResolveClient.parseIds()` / `.resolve(ids)` / `.track(key)` (stale-response guard). UI-agnostic, no jQuery dependency.
- Hint UI moved to `lib/meta/js/cmb2-post-search-field-hints.js`, owned by the fork, enqueued with a dependency on `nm-post-resolve`; degrades silently to the server-rendered hint if the utility is absent.
- PR 2's `atf-preview.js` consumes the same client instead of duplicating fetch/nonce/stale-guard code.
