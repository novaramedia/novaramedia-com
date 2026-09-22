# Conditional Required Meta Validation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Require YouTube ID on video posts and Soundcloud URL on audio posts at publish time, while letting drafts and previews save with no validation at all.

**Architecture:** Extends the existing client-side CMB2 validation fork (`lib/meta/cmb2-validation.php`). Fields carry a category *slug* in a new `data-validation-required-category` attribute; the validator emits a slug → term-IDs map (self + descendants) at render time, so term-ID drift across environments doesn't matter. A gate at the top of `checkValidation` skips ALL validation for Save Draft and Preview submits.

**Tech Stack:** WordPress classic editor, CMB2, jQuery (inline admin script printed from PHP). No build step involved — these are PHP source files, `dist/` untouched.

**Spec:** `docs/specs/2026-07-16-conditional-required-meta-validation-design.md`

## Global Constraints

- Branch: `fix/required-post-meta-validation` (continues existing feature, per user)
- Production-quality code only; follow existing file style (2-space indent, snake_case helpers, jQuery idioms already in file)
- No changes to Webpack/build system; no `dist/` commits (no source assets change here)
- Fork version bumps 0.3.2 → 0.4.0 in the final task (0.3.2 was the branch state when this plan was written; PR #568 overall moves the header 0.2.0 → 0.4.0), with docblock updated in the same commit as the behaviour it documents (sync-docs rule)
- No automated test harness exists for this inline admin script — every task carries manual verification steps against the local DevKinsta site (`novaramediacom` local WP admin)
- Options-page forms (Links Bar `#nm_secondary_options_page`, fundraising `#nm_fundraising_options`) must keep current always-validate behaviour

---

### Task 1: Publish-only gate in `checkValidation`

**Files:**
- Modify: `lib/meta/cmb2-validation.php:114-123` (top of `checkValidation`)

**Interfaces:**
- Consumes: existing `checkValidation( event )` jQuery submit handler
- Produces: early-return gate; all later validation logic (including Task 2's) is automatically covered by it

- [ ] **Step 1: Add the gate**

In `lib/meta/cmb2-validation.php`, inside `function checkValidation( event )`, insert at the very top (before the `tinyMCE.triggerSave()` block):

```js
      // Drafts and previews save freely; validation only gates
      // Publish / Schedule / Update / Submit for Review.
      const submitter = event.originalEvent && event.originalEvent.submitter;

      if ( submitter && submitter.id === 'save-post' ) {
        return;
      }

      if ( $( '#wp-preview' ).val() === 'dopreview' ) {
        return;
      }
```

Notes for the implementer:
- `#save-post` is the classic-editor Save Draft button; `#publish` is Publish/Schedule/Update/Submit for Review. Only Save Draft skips.
- WP's preview triggers a jQuery `.submit()` (no `originalEvent.submitter`), which is why the `#wp-preview` hidden-field check exists as a second signal.
- Neither `#save-post` nor `#wp-preview` exists on the options-page forms, so both checks are no-ops there — options pages keep validating every submit.
- `submitter` undefined (older browser, programmatic submit) falls through to validation — safe default, same as current behaviour.

- [ ] **Step 2: Manual verification on local admin**

On the local DevKinsta site (`novaramediacom`), wp-admin → Posts → Add New (classic editor):

1. New post, title only, Standfirst and Short description empty → **Save Draft** → expected: saves with no alert, no red rows.
2. Same draft → **Preview** → expected: preview tab opens, no alert.
3. Same draft → **Publish** → expected: blocked; alert lists Standfirst + Short description; rows highlighted.
4. Fill Standfirst + Short description → **Publish** → expected: publishes.
5. Published post → blank out Standfirst → **Update** → expected: blocked.
6. Appearance/theme options → Links Bar options page → submit with a field that violates word-length validation → expected: still blocked (gate doesn't leak).

- [ ] **Step 3: Commit**

```bash
git add lib/meta/cmb2-validation.php
git commit -m "feat: skip meta validation on draft saves and previews

Save Draft (submitter #save-post) and Preview (#wp-preview = dopreview)
now bypass checkValidation entirely; Publish / Schedule / Update /
Submit for Review still validate. Options page forms unaffected (no
#save-post button there). Programmatic submits without a submitter fall
through to validation, preserving old behaviour as the safe default.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 2: Slug → term-IDs map + conditional required logic in validator

**Files:**
- Modify: `lib/meta/cmb2-validation.php` — PHP before the script block, and the required-check branch inside the `$toValidate.each` loop (currently lines 164-182)

**Interfaces:**
- Consumes: Task 1's gate (already in file); existing `is_empty_value()`, `add_failure()`, `remove_failure()`
- Produces: JS `categoryMap` (`{ "<slug>": [termId, ...], ... }`) and support for the `data-validation-required-category="<slug>"` attribute that Task 3's fields will carry

- [ ] **Step 1: Build the map in PHP and emit it**

In `cmb2_after_form_do_js_validation()`, after `$added = true;` and before the `?>` that opens the script, add:

```php
  // Slug-keyed map of category term IDs (self + descendants) so field
  // markup can name categories by slug — term IDs drift across installs.
  $category_map = array();

  foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
    $ids = array_map( 'intval', get_term_children( $category->term_id, 'category' ) );
    array_unshift( $ids, (int) $category->term_id );

    $category_map[ $category->slug ] = $ids;
  }
```

Then inside the script, after `const $htmlbody = $( 'html, body' );`, add:

```js
    const categoryMap = <?php echo wp_json_encode( $category_map ); ?>;
```

- [ ] **Step 2: Conditional required check in the field loop**

Replace the current required branch:

```js
        if ( $this.data( 'validation-required' ) === true ) { // Validate required if variable set
```

with logic that computes a single `isRequired` flag first. Full replacement for the block (keep the file-list and `is_empty_value` internals exactly as they are):

```js
        // Required either unconditionally, or conditionally when the post is
        // in the named category (or any of its descendants).
        // .attr() not .data(): jQuery data() would coerce numeric-looking
        // slugs (e.g. "2024") to numbers and break the map lookup.
        const requiredCategorySlug = $this.attr( 'data-validation-required-category' );

        let isRequired = $this.data( 'validation-required' ) === true;

        if ( ! isRequired && typeof requiredCategorySlug !== 'undefined' ) {
          const termIds = categoryMap[ requiredCategorySlug ] || [];

          isRequired = termIds.some( function( id ) {
            return $( 'input[name="post_category[]"][value="' + id + '"]' ).is( ':checked' );
          });
        }

        if ( isRequired ) {
          if ( $row.is( '.cmb-type-file-list' ) ) {

            var has_LIs = $row.find( 'ul.cmb-attach-list li' ).length > 0;

            if ( ! has_LIs ) {
              add_failure( $row, 'Meta field required' );
            } else {
              remove_failure( $row );
            }

          } else {
            if ( is_empty_value( val ) ) {
              add_failure( $row, 'Meta field required' );
            } else {
              remove_failure( $row );
            }
          }
        } else if ( typeof requiredCategorySlug !== 'undefined' ) {
          // Conditionally-required field whose category isn't ticked:
          // clear any stale highlight from a previous failed attempt.
          remove_failure( $row );
        }
```

Behaviour notes:
- Slug missing from `categoryMap` (category deleted) → `termIds` empty → not required. Safe fail per spec.
- No `#categorychecklist` in DOM (options pages) → no checkbox matches → not required.
- Unconditional `data-validation-required` wins if both are set.

- [ ] **Step 3: Syntax check**

```bash
php -l lib/meta/cmb2-validation.php
```
Expected: `No syntax errors detected`

- [ ] **Step 4: Manual verification**

Nothing user-visible changes yet (no field carries the new attribute until Task 3). Verify no regression:

1. Reload post edit screen; view page source of the edit screen and confirm `const categoryMap = {...}` renders with slug keys including `"articles"`, `"audio"`, `"video"`.
2. Publish attempt with empty Standfirst → still blocked (existing behaviour intact).

- [ ] **Step 5: Commit**

```bash
git add lib/meta/cmb2-validation.php
git commit -m "feat: support category-conditional required validation

New data-validation-required-category=\"<slug>\" attribute: field is
required only when the named category or any of its descendants is
ticked in the category checklist. Validator emits a slug -> term-IDs
map (built server-side per environment) so markup stays free of
term IDs, which drift between installs. Missing slug or absent
checklist resolves to not-required.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 3: Wire YouTube ID and Soundcloud URL fields

**Files:**
- Modify: `lib/meta/meta-boxes-post.php:138-145` (Soundcloud URL field) and `:188-195` (YouTube ID field)

**Interfaces:**
- Consumes: Task 2's `data-validation-required-category` attribute support
- Produces: end-user-visible feature; nothing downstream consumes this

- [ ] **Step 1: Add attributes to both fields**

Soundcloud URL field becomes:

```php
  $audio_metabox->add_field(
       array(
    'name'       => __( 'Soundcloud URL', 'cmb' ),
    'desc'       => __( 'Required on audio posts! Enter a full URL.', 'cmb' ),
    'id'         => $prefix . 'sc',
    'type'       => 'text_url',
    'attributes' => array(
      'data-validation'                   => 'true',
      'data-validation-required-category' => 'audio',
    ),
  )
      );
```

YouTube ID field becomes:

```php
  $video_metabox->add_field(
       array(
    'name'       => __( 'YouTube ID', 'cmb' ),
    'desc'       => __( 'Required on video posts! ID of YouTube video. For example if this is the url https://www.youtube.com/watch?v=CmuDcXfBqTg&feature=c4-overview&list=UUOzMAa6IhV6uwYQATYG_2kg then the ID is the value after the ?v= and before the &, for this link CmuDcXfBqTg', 'cmb' ),
    'id'         => $prefix . 'utube',
    'type'       => 'text',
    'attributes' => array(
      'data-validation'                   => 'true',
      'data-validation-required-category' => 'video',
    ),
  )
      );
```

(Desc copy changes "Required!" → "Required on audio/video posts!" so the label matches the new conditional behaviour — sync-docs rule.)

- [ ] **Step 2: Syntax check**

```bash
php -l lib/meta/meta-boxes-post.php
```
Expected: `No syntax errors detected`

- [ ] **Step 3: Manual verification — full spec test matrix**

Local admin, classic editor. Fill Standfirst + Short description in all cases so only the conditional fields are under test:

1. Post in `articles` category, YouTube ID + Soundcloud URL empty → **Publish** → publishes (fields not required).
2. Post in `video` category, YouTube ID empty → **Publish** → blocked; alert names "YouTube ID"; row highlighted.
3. Same post → **Save Draft** → saves freely (Task 1 gate).
4. Same post, fill YouTube ID → **Publish** → publishes.
5. Post ticked ONLY in a *child* category of `audio` (create a temp child category if none exists), Soundcloud URL empty → **Publish** → blocked. Delete temp category after.
6. Post in `video` category, publish blocked, then untick `video`, tick `articles` → **Publish** → publishes, and the previously red YouTube ID row is cleared.

- [ ] **Step 4: Commit**

```bash
git add lib/meta/meta-boxes-post.php
git commit -m "feat: require YouTube ID on video posts, Soundcloud URL on audio posts

Wire both fields to the category-conditional validation via
data-validation-required-category slugs. Field descriptions updated to
say which category makes them required.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 4: Docblock, version bump, changelog

**Files:**
- Modify: `lib/meta/cmb2-validation.php:1-33` (docblock)
- Modify: `CHANGELOG.md` (Unreleased section)

**Interfaces:**
- Consumes: everything above
- Produces: documentation only

- [ ] **Step 1: Update the fork docblock**

In `lib/meta/cmb2-validation.php`:
- `Version: 0.3.2` → `Version: 0.4.0`
- Append two lines to the change-notes list in the docblock:

```
 * Validation now skipped on Save Draft and Preview submits; only publish-type submits validate
 * Added data-validation-required-category="<slug>" for fields required only in a category (or its descendants)
```

- Extend the attributes example to document the new attribute:

```
 * 'attributes' => array(
 *   'data-validation' => 'true',
 *   'data-validation-word-length' => 14,
 *   'data-validation-required' => 'true',
 *   'data-validation-required-category' => 'video',
 * )
```

- [ ] **Step 2: Update CHANGELOG.md**

Follow terse changelog style (one line per feature, no implementation details). Extend the existing Unreleased entry for required meta validation with lines like:

```markdown
- Required meta validation only runs on publish — drafts and previews save freely
- YouTube ID required on video posts, Soundcloud URL required on audio posts
```

Check the existing entry from commit f6e489e6 first and keep its format; amend its wording if the publish-only change supersedes what it claims.

- [ ] **Step 3: Syntax check**

```bash
php -l lib/meta/cmb2-validation.php
```
Expected: `No syntax errors detected`

- [ ] **Step 4: Commit**

```bash
git add lib/meta/cmb2-validation.php CHANGELOG.md
git commit -m "docs: document conditional validation and publish-only gating, bump fork to 0.4.0

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```
