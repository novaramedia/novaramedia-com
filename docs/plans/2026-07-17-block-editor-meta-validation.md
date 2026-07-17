# Block Editor Meta Validation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make required/conditional meta field validation work in the block editor by refactoring the classic-only inline validator into a shared rule core with classic and Gutenberg adapters.

**Architecture:** Pure rule logic in `src/admin/meta-validation/core.js` (node-testable), DOM helpers in `dom.js`, two adapter entries (`classic.js` binds native form submit; `block-editor.js` uses `wp.data` `lockPostSaving` + error notices). Built by a new `webpack.admin.config.js` on `@wordpress/scripts` (mirrors the existing `webpack.blocks.config.js` precedent) so `@wordpress/*` imports externalise to `wp.*` globals and each entry emits an `.asset.php`. `lib/meta/cmb2-validation.php` is renamed to `lib/meta/meta-validation.php` and becomes a thin loader.

**Tech Stack:** PHP (WordPress/CMB2), vanilla JS + `@wordpress/data` / `@wordpress/notices`, `@wordpress/scripts` build, `node:test` for the pure core.

**Spec:** `docs/specs/2026-07-17-block-editor-meta-validation-design.md`

## Global Constraints

- **GATE: Task 1 adds `webpack.admin.config.js` + npm scripts — team approval required per CLAUDE.md before starting implementation.** Existing `webpack.config.js` must NOT be touched.
- Production-quality code only; no console.logs.
- `dist/` commits only when source actually changed; run `npm run build` to verify.
- Branch: `feature/block-editor-meta-validation` (off `fix/required-post-meta-validation`; rebase onto `development` after #568 merges).
- Behaviour parity: classic editor must match PR #568 behaviour exactly (regression matrix in Task 4).
- Blocked-row highlight colour everywhere: `rgb(255, 170, 170)` via inline `background-color`.
- Lock/notice id string: `nm-meta-validation`.
- Localized data global: `window.nmMetaValidation = { categoryMap }`.
- Publish-type statuses: `[ 'publish', 'future', 'private' ]`.

---

### Task 1: Admin build scaffold

**Files:**
- Create: `webpack.admin.config.js`
- Create: `src/admin/meta-validation/package.json` (ESM marker so `node --test` can import the source)
- Create: `src/admin/meta-validation/classic.js` (stub)
- Create: `src/admin/meta-validation/block-editor.js` (stub)
- Modify: `package.json` (scripts block, lines 6–18)

**Interfaces:**
- Produces: `npm run build:admin` → `dist/admin/meta-validation-classic.js`, `dist/admin/meta-validation-block-editor.js`, each with a sibling `.asset.php`. Later tasks fill the stubs.

- [ ] **Step 1: Confirm team approval for the new build config exists.** If not recorded (PR comment, Slack, Notion), STOP and ask the user.

- [ ] **Step 2: Create `webpack.admin.config.js`**

```js
/**
 * Webpack configuration for admin-only JS (editor tooling).
 *
 * Uses @wordpress/scripts default configuration, like webpack.blocks.config.js,
 * so @wordpress/* imports are externalised to wp.* globals and each entry
 * emits an .asset.php carrying its dependency list and version hash.
 * Exists to avoid touching the theme's main webpack.config.js.
 */

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    'meta-validation-classic': path.resolve(__dirname, 'src/admin/meta-validation/classic.js'),
    'meta-validation-block-editor': path.resolve(__dirname, 'src/admin/meta-validation/block-editor.js'),
  },
  output: {
    ...defaultConfig.output,
    path: path.resolve(__dirname, 'dist/admin'),
  },
};
```

- [ ] **Step 3: Create ESM marker `src/admin/meta-validation/package.json`**

```json
{
  "type": "module"
}
```

(Lets `node --test` import `core.js` as ESM in Task 2 without touching the root package.json module type; webpack is unaffected.)

- [ ] **Step 4: Create stub entries**

`src/admin/meta-validation/classic.js`:
```js
// Classic editor adapter — implemented in a later task.
export {};
```

`src/admin/meta-validation/block-editor.js`:
```js
// Block editor adapter — implemented in a later task.
export {};
```

- [ ] **Step 5: Add npm scripts** — in root `package.json` scripts block:

```json
"dev:admin": "wp-scripts start --config webpack.admin.config.js",
"build:admin": "wp-scripts build --config webpack.admin.config.js",
```

and change the `build` script to:

```json
"build": "npm run build:theme && npm run build:blocks && npm run build:admin",
```

- [ ] **Step 6: Verify build**

Run: `npm run build:admin`
Expected: exit 0; `dist/admin/` contains `meta-validation-classic.js`, `meta-validation-classic.asset.php`, `meta-validation-block-editor.js`, `meta-validation-block-editor.asset.php`.

Run: `npm run build`
Expected: exit 0; existing `dist/main.js` and `blocks/` output unchanged apart from timestamps (`git status` shows no unexpected modifications outside `dist/admin/`).

- [ ] **Step 7: Commit**

```bash
git add webpack.admin.config.js src/admin/ dist/admin/ package.json
git commit -m "build: add wp-scripts admin JS build (dist/admin, meta-validation entries)"
```

---

### Task 2: Pure rule core (`core.js`) with node:test

**Files:**
- Create: `src/admin/meta-validation/core.js`
- Test: `src/admin/meta-validation/core.test.js`

**Interfaces:**
- Produces:
  - `countWords( stringInput: string ): number`
  - `isEmptyText( text: string ): boolean`
  - `validateField( field: { value: string, text: string, rules: { required?: boolean, requiredCategory?: string, wordLength?: number } }, activeCategoryIds: number[], categoryMap: Record<string, number[]> ): { valid: boolean, failures: string[] }`
- No DOM, no `wp.*`, no jQuery — must run under bare node.

- [ ] **Step 1: Write the failing tests** — `src/admin/meta-validation/core.test.js`:

```js
import test from 'node:test';
import assert from 'node:assert/strict';
import { countWords, isEmptyText, validateField } from './core.js';

test( 'countWords matches historic behaviour', () => {
  assert.equal( countWords( '' ), 0 );
  assert.equal( countWords( 'one two three' ), 3 );
} );

test( 'isEmptyText treats whitespace as empty', () => {
  assert.equal( isEmptyText( '' ), true );
  assert.equal( isEmptyText( '   ' ), true );
  assert.equal( isEmptyText( 'x' ), false );
} );

test( 'required field with empty text fails', () => {
  const result = validateField(
    { value: '<p></p>', text: '', rules: { required: true } }, [], {}
  );
  assert.equal( result.valid, false );
  assert.deepEqual( result.failures, [ 'Meta field required' ] );
} );

test( 'required field with content passes', () => {
  const result = validateField(
    { value: 'hello', text: 'hello', rules: { required: true } }, [], {}
  );
  assert.equal( result.valid, true );
} );

test( 'category-conditional: required when a mapped term id is active', () => {
  const map = { video: [ 12, 34 ] };
  const rules = { requiredCategory: 'video' };

  const inCategory = validateField( { value: '', text: '', rules }, [ 34 ], map );
  assert.equal( inCategory.valid, false );

  const notInCategory = validateField( { value: '', text: '', rules }, [ 99 ], map );
  assert.equal( notInCategory.valid, true );
} );

test( 'category slug missing from map means not required (safe fail)', () => {
  const result = validateField(
    { value: '', text: '', rules: { requiredCategory: 'gone' } }, [ 1 ], {}
  );
  assert.equal( result.valid, true );
} );

test( 'word length over limit fails with the historic message', () => {
  const result = validateField(
    { value: 'a b c d', text: 'a b c d', rules: { wordLength: 3 } }, [], {}
  );
  assert.equal( result.valid, false );
  assert.deepEqual( result.failures, [ 'Excess word length. Must be less than 3 words.' ] );
} );

test( 'word length and required can both fail', () => {
  const result = validateField(
    { value: '<p> </p> a b', text: '', rules: { wordLength: 1, required: true } }, [], {}
  );
  assert.equal( result.failures.length, 2 );
} );
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `node --test src/admin/meta-validation/`
Expected: FAIL — cannot find module `./core.js`.

- [ ] **Step 3: Implement `src/admin/meta-validation/core.js`**

```js
/**
 * Pure validation rules — no DOM, no wp.* globals, runs under bare node.
 * Markup stripping happens in dom.js; `text` arrives already stripped.
 */

// Split regex matches the historic classic implementation exactly.
export const countWords = ( stringInput ) =>
  ( stringInput.length && stringInput.split( /\s+\b/ ).length ) || 0;

export const isEmptyText = ( text ) => ! text || ! text.trim();

/**
 * Validate one field against its rules.
 *
 * @param {Object}   field                     Shape produced by dom.js readField().
 * @param {string}   field.value               Raw field value (may contain HTML).
 * @param {string}   field.text                Markup-stripped text of the value.
 * @param {Object}   field.rules
 * @param {boolean}  [field.rules.required]          Required regardless of category.
 * @param {string}   [field.rules.requiredCategory]  Required only in this category slug.
 * @param {number}   [field.rules.wordLength]        Maximum word count.
 * @param {number[]} activeCategoryIds         Term IDs currently on the post.
 * @param {Object}   categoryMap               slug => term IDs (self + descendants).
 * @return {{ valid: boolean, failures: string[] }}
 */
export const validateField = ( { value, text, rules }, activeCategoryIds, categoryMap ) => {
  const failures = [];

  if ( typeof rules.wordLength !== 'undefined' && countWords( value ) > rules.wordLength ) {
    failures.push( `Excess word length. Must be less than ${ rules.wordLength } words.` );
  }

  // Required either unconditionally, or conditionally when the post is in
  // the named category (or any of its descendants — the map carries both).
  let required = rules.required === true;

  if ( ! required && typeof rules.requiredCategory !== 'undefined' ) {
    const termIds = categoryMap[ rules.requiredCategory ] || [];

    required = termIds.some( ( id ) => activeCategoryIds.includes( id ) );
  }

  if ( required && isEmptyText( text ) ) {
    failures.push( 'Meta field required' );
  }

  return { valid: failures.length === 0, failures };
};
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `node --test src/admin/meta-validation/`
Expected: all 8 tests PASS.

- [ ] **Step 5: Commit**

```bash
git add src/admin/meta-validation/core.js src/admin/meta-validation/core.test.js
git commit -m "feat: pure meta validation rule core with node:test coverage"
```

---

### Task 3: Shared DOM helpers (`dom.js`)

**Files:**
- Create: `src/admin/meta-validation/dom.js`

**Interfaces:**
- Consumes: nothing (browser globals only).
- Produces (used by both adapters):
  - `bridgeWysiwygMarkers(): void`
  - `scanFields( root?: ParentNode ): HTMLElement[]` — runs the bridge, then returns `[data-validation]` elements
  - `syncTinyMce(): void`
  - `getRow( el: HTMLElement ): HTMLElement | null`
  - `getLabel( row: HTMLElement | null ): string`
  - `toText( value: string ): string` — DOMParser markup strip
  - `readField( el: HTMLElement ): { el, row, value: string, text: string, rules: { required?, requiredCategory?, wordLength? } }`
  - `addFailureHighlight( row )`, `removeFailureHighlight( row )`, `clearAllHighlights()`

- [ ] **Step 1: Implement `src/admin/meta-validation/dom.js`**

```js
/**
 * Shared DOM helpers for both editor adapters. Everything that touches the
 * document lives here so core.js stays pure and node-testable.
 */

const FAILURE_COLOR = 'rgb(255, 170, 170)';

// Non-group wysiwyg fields render via wp_editor() which drops CMB2
// 'attributes'; they are marked with editor_class instead (nm- prefixed
// because classes share wp-admin's global namespace with core and other
// plugins). Copy the marker onto the data attributes the scanner reads.
export const bridgeWysiwygMarkers = () => {
  document.querySelectorAll( 'textarea.nm-validation-required' ).forEach( ( el ) => {
    el.setAttribute( 'data-validation', 'true' );
    el.setAttribute( 'data-validation-required', 'true' );
  } );
};

export const scanFields = ( root = document ) => {
  bridgeWysiwygMarkers();

  return Array.from( root.querySelectorAll( '[data-validation]' ) );
};

// Wysiwyg fields edited in Visual mode only sync to their underlying
// textarea on save; force the sync so .value reads current content.
export const syncTinyMce = () => {
  if ( typeof window.tinyMCE !== 'undefined' && typeof window.tinyMCE.triggerSave === 'function' ) {
    window.tinyMCE.triggerSave();
  }
};

// closest() returns the nearest ancestor .cmb-row — in field groups there
// can be several ancestors; nearest matches the old parents().first().
export const getRow = ( el ) => el.closest( '.cmb-row' );

export const getLabel = ( row ) => {
  const label = row && row.querySelector( '.cmb-th label' );

  return label ? label.textContent : '';
};

// Markup-only values (e.g. an empty <p></p> from a wysiwyg) must read as
// empty. DOMParser never executes scripts.
export const toText = ( value ) =>
  new DOMParser().parseFromString( String( value || '' ), 'text/html' ).body.textContent;

/**
 * Read one field element into the shape core.validateField() consumes.
 * File-list rows have no text value — attached items stand in for content.
 */
export const readField = ( el ) => {
  const row = getRow( el );
  const isFileList = row && row.classList.contains( 'cmb-type-file-list' );
  const value = el.value || '';

  let text = toText( value );

  if ( isFileList ) {
    text = row.querySelectorAll( 'ul.cmb-attach-list li' ).length ? 'attached' : '';
  }

  const rules = {};

  if ( el.dataset.validationRequired === 'true' ) {
    rules.required = true;
  }

  // dataset values are always strings, so numeric-looking category slugs
  // (e.g. "2024") survive intact — no jQuery .data() coercion hazard.
  if ( typeof el.dataset.validationRequiredCategory !== 'undefined' ) {
    rules.requiredCategory = el.dataset.validationRequiredCategory;
  }

  if ( typeof el.dataset.validationWordLength !== 'undefined' ) {
    rules.wordLength = parseInt( el.dataset.validationWordLength, 10 );
  }

  return { el, row, value, text, rules };
};

export const addFailureHighlight = ( row ) => {
  if ( row ) {
    row.style.backgroundColor = FAILURE_COLOR;
  }
};

export const removeFailureHighlight = ( row ) => {
  if ( row ) {
    row.style.backgroundColor = '';
  }
};

export const clearAllHighlights = () => {
  scanFields().forEach( ( el ) => removeFailureHighlight( getRow( el ) ) );
};
```

- [ ] **Step 2: Verify it builds** (imported by nothing yet, so compile it via a temporary import)

Run: `npm run build:admin`
Expected: exit 0 (tree-shaken out of the stubs is fine — this step is a syntax check; full verification happens in Tasks 4–5).

- [ ] **Step 3: Commit**

```bash
git add src/admin/meta-validation/dom.js
git commit -m "feat: shared DOM helpers for meta validation adapters"
```

---

### Task 4: Classic adapter + PHP loader swap

**Files:**
- Modify: `src/admin/meta-validation/classic.js` (replace stub)
- Create: `lib/meta/meta-validation.php`
- Delete: `lib/meta/cmb2-validation.php` (`git mv` then rewrite)
- Modify: `functions.php:130`
- Modify: `dist/admin/` (rebuild)

**Interfaces:**
- Consumes: `validateField` from `core.js`; everything from `dom.js`; `window.nmMetaValidation.categoryMap` (localized by the PHP below).
- Produces: `nm_meta_validation_category_map(): array` and `nm_meta_validation_enqueue( string $handle, string $entry ): void` in `lib/meta/meta-validation.php` (Task 5 reuses the enqueue helper).

- [ ] **Step 1: Implement `src/admin/meta-validation/classic.js`**

```js
/**
 * Classic editor adapter. Binds the native form submit and blocks it with
 * an alert + row highlights when validation fails.
 *
 * Gating: drafts and previews save freely; validation only runs for
 * publish-type submits (Publish / Schedule / Update / Submit for Review).
 * Options page forms (Links Bar, fundraising) have neither gate element and
 * always validate.
 */

import { validateField } from './core';
import {
  scanFields,
  readField,
  syncTinyMce,
  getLabel,
  addFailureHighlight,
  removeFailureHighlight,
  clearAllHighlights,
} from './dom';

const FORM_IDS = [ 'post', 'nm_secondary_options_page', 'nm_fundraising_options' ];

const getCategoryMap = () =>
  ( window.nmMetaValidation && window.nmMetaValidation.categoryMap ) || {};

const getActiveCategoryIds = () =>
  Array.from( document.querySelectorAll( 'input[name="post_category[]"]:checked' ) )
    .map( ( el ) => parseInt( el.value, 10 ) );

const checkValidation = ( event ) => {
  // SubmitEvent.submitter is the button that triggered this submit; in the
  // classic editor Save Draft is id="save-post" (Publish and Update submit
  // via id="publish", which falls through to validate). Clear highlights
  // left by an earlier failed Publish attempt so the gated save doesn't
  // look like it still has errors.
  const submitter = event.submitter;

  if ( submitter && submitter.id === 'save-post' ) {
    clearAllHighlights();

    return;
  }

  const preview = document.getElementById( 'wp-preview' );

  if ( preview && preview.value === 'dopreview' ) {
    clearAllHighlights();

    return;
  }

  syncTinyMce();

  const fields = scanFields();

  if ( ! fields.length ) {
    return;
  }

  const categoryMap = getCategoryMap();
  const activeCategoryIds = getActiveCategoryIds();
  const messages = [];

  let firstErrorRow = null;

  fields.forEach( ( el ) => {
    const field = readField( el );
    const { valid, failures } = validateField( field, activeCategoryIds, categoryMap );

    if ( valid ) {
      removeFailureHighlight( field.row );

      return;
    }

    addFailureHighlight( field.row );
    firstErrorRow = firstErrorRow || field.row;

    failures.forEach( ( reason ) => {
      messages.push( `\nField "${ getLabel( field.row ) }": ${ reason }` );
    } );
  } );

  if ( firstErrorRow ) {
    event.preventDefault();

    window.alert( `The following validation errors occured: ${ messages.join( '' ) }` );

    window.scrollTo( {
      top: firstErrorRow.getBoundingClientRect().top + window.scrollY - 200,
      behavior: 'smooth',
    } );
  }
};

const init = () => {
  const form = FORM_IDS.map( ( id ) => document.getElementById( id ) ).find( Boolean );

  if ( ! form || ! scanFields().length ) {
    return;
  }

  form.addEventListener( 'submit', checkValidation );
};

if ( document.readyState === 'loading' ) {
  document.addEventListener( 'DOMContentLoaded', init );
} else {
  init();
}
```

- [ ] **Step 2: Rename and rewrite the PHP**

```bash
git mv lib/meta/cmb2-validation.php lib/meta/meta-validation.php
```

New content of `lib/meta/meta-validation.php` (full replacement):

```php
<?php
/**
 * Meta field validation loader.
 *
 * Required / conditional validation of CMB2 meta fields runs client-side;
 * the JS lives in src/admin/meta-validation/ and builds to dist/admin/.
 * This file builds the category slug => term-IDs map both editor adapters
 * consume and enqueues the right bundle per editor.
 *
 * To enable on a CMB2 meta field set the attributes parameters
 * [note that booleans must be strings]
 *
 * 'attributes' => array(
 *   'data-validation' => 'true',
 *   'data-validation-word-length' => 14,
 *   'data-validation-required' => 'true',
 *   'data-validation-required-category' => 'video',
 * )
 *
 * Non-group wysiwyg fields render via wp_editor(), which does not output the
 * CMB2 'attributes' array — mark those with an editor class instead:
 *
 * 'options' => array(
 *   'editor_class' => 'nm-validation-required'
 * )
 *
 * Rules: data-validation-required = must be non-empty (whitespace-only and
 * markup-only count as empty); data-validation-required-category = required
 * only when the post is in that category slug or a descendant;
 * data-validation-word-length = maximum word count. Drafts and previews
 * save freely; only publish-type saves validate.
 */

/**
 * Slug-keyed map of category term IDs (self + descendants) so field markup
 * can name categories by slug — term IDs drift across installs. Only post
 * edit screens can use category-conditional rules, so the map stays empty
 * elsewhere rather than querying terms on options pages.
 *
 * @return array<string, int[]>
 */
function nm_meta_validation_category_map() {
  $category_map = array();

  $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

  if ( ! $screen || 'post' !== $screen->base ) {
    return $category_map;
  }

  foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
    $ids = array_map( 'intval', get_term_children( $category->term_id, 'category' ) );

    array_unshift( $ids, (int) $category->term_id );

    $category_map[ $category->slug ] = $ids;
  }

  return $category_map;
}

/**
 * Enqueue one admin validation bundle with its wp-scripts asset metadata.
 *
 * @param string $handle Script handle.
 * @param string $entry  Entry basename under dist/admin/.
 */
function nm_meta_validation_enqueue( $handle, $entry ) {
  $asset_path = get_template_directory() . '/dist/admin/' . $entry . '.asset.php';

  if ( ! file_exists( $asset_path ) ) {
    return;
  }

  $asset = require $asset_path;

  wp_enqueue_script(
    $handle,
    get_template_directory_uri() . '/dist/admin/' . $entry . '.js',
    $asset['dependencies'],
    $asset['version'],
    true
  );

  wp_localize_script(
    $handle,
    'nmMetaValidation',
    array( 'categoryMap' => nm_meta_validation_category_map() )
  );
}

/**
 * Classic bundle: hook cmb2_after_form so the script loads exactly where a
 * CMB2 form rendered (post edit screens, Links Bar and fundraising options
 * pages) — same reach as the old inline print. The bundle self-no-ops when
 * no known form / no validated fields exist, and the block editor page has
 * no #post form, but skip it there explicitly anyway.
 */
function nm_meta_validation_enqueue_classic( $post_id, $cmb ) {
  $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

  if ( $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
    return;
  }

  nm_meta_validation_enqueue( 'nm-meta-validation-classic', 'meta-validation-classic' );
}

add_action( 'cmb2_after_form', 'nm_meta_validation_enqueue_classic', 10, 2 );
```

- [ ] **Step 3: Update the include** — `functions.php:130`:

```php
get_template_part( 'lib/meta/meta-validation' );
```

- [ ] **Step 4: Lint and build**

Run: `php -l lib/meta/meta-validation.php`
Expected: no syntax errors.

Run: `npm run build:admin`
Expected: exit 0, `dist/admin/meta-validation-classic.js` non-stub (size grows).

- [ ] **Step 5: Manual classic regression matrix** — local wp-admin (classic editor, DevKinsta):

1. Draft save with everything empty → saves freely, no alert.
2. Publish article post, empty YouTube ID → publishes.
3. Publish video post, empty YouTube ID → blocked, row highlighted, alert.
4. Publish post ticked only in a child category of audio, empty Soundcloud URL → blocked.
5. Preview a draft with empty required fields → preview opens.
6. Publish with empty standfirst → blocked (short description wysiwyg: also verify whitespace-only content blocks).
7. Failed publish (red rows) then Save Draft → saves, highlights cleared.
8. Links Bar options page save with invalid field → blocked.

Expected: identical behaviour to PR #568.

- [ ] **Step 6: Commit**

```bash
git add src/admin/meta-validation/classic.js lib/meta/meta-validation.php functions.php dist/admin/
git commit -m "refactor: classic meta validation served from shared admin bundle

lib/meta/cmb2-validation.php renamed to meta-validation.php as a thin
loader; inline JS replaced by dist/admin/meta-validation-classic.js
built from the shared core. Behaviour unchanged."
```

---

### Task 5: Block editor adapter

**Files:**
- Modify: `src/admin/meta-validation/block-editor.js` (replace stub)
- Modify: `lib/meta/meta-validation.php` (add block editor enqueue)
- Modify: `dist/admin/` (rebuild)

**Interfaces:**
- Consumes: `validateField` from `core.js`; `dom.js` helpers; `nm_meta_validation_enqueue()` from Task 4; `@wordpress/data` (`select`, `dispatch`, `subscribe`).
- Produces: user-facing behaviour only.

- [ ] **Step 1: Implement `src/admin/meta-validation/block-editor.js`**

```js
/**
 * Block editor adapter. Gutenberg saves via REST — there is no cancellable
 * form submit — so gating uses the supported wp.data mechanism: while
 * required metabox fields are invalid AND the post is (or is becoming)
 * published, saving is locked and an error notice lists the failures.
 *
 * Drafts save and preview freely: the lock only engages when the saved or
 * edited status is publish-type or the publish sidebar is open. "Switch to
 * draft" changes the edited status, so it unlocks and proceeds.
 *
 * Known limitation (see spec §5): with the "Enable pre-publish checks"
 * preference turned off, a draft's first publish may race the subscriber.
 */

import { select, dispatch, subscribe } from '@wordpress/data';
import { validateField } from './core';
import {
  scanFields,
  readField,
  syncTinyMce,
  getLabel,
  addFailureHighlight,
  removeFailureHighlight,
} from './dom';

const LOCK_ID = 'nm-meta-validation';
const NOTICE_ID = 'nm-meta-validation';
const PUBLISH_STATUSES = [ 'publish', 'future', 'private' ];

const getCategoryMap = () =>
  ( window.nmMetaValidation && window.nmMetaValidation.categoryMap ) || {};

const debounce = ( fn, ms ) => {
  let timer;

  return () => {
    clearTimeout( timer );
    timer = setTimeout( fn, ms );
  };
};

const gatherEditorState = () => {
  const editor = select( 'core/editor' );

  return {
    savedStatus: editor.getCurrentPostAttribute( 'status' ),
    editedStatus: editor.getEditedPostAttribute( 'status' ),
    sidebarOpen: editor.isPublishSidebarOpened(),
    categories: ( editor.getEditedPostAttribute( 'categories' ) || [] ).map( Number ),
  };
};

const gateActive = ( state ) =>
  PUBLISH_STATUSES.includes( state.savedStatus ) ||
  PUBLISH_STATUSES.includes( state.editedStatus ) ||
  state.sidebarOpen;

const revalidate = () => {
  const state = gatherEditorState();
  const active = gateActive( state );
  const failures = [];

  syncTinyMce();

  scanFields().forEach( ( el ) => {
    const field = readField( el );
    const result = validateField( field, state.categories, getCategoryMap() );

    if ( result.valid || ! active ) {
      removeFailureHighlight( field.row );

      return;
    }

    addFailureHighlight( field.row );

    result.failures.forEach( ( reason ) => {
      failures.push( `${ getLabel( field.row ) }: ${ reason }` );
    } );
  } );

  if ( failures.length && active ) {
    dispatch( 'core/editor' ).lockPostSaving( LOCK_ID );
    dispatch( 'core/notices' ).createErrorNotice(
      `Required post information is missing — ${ failures.join( '; ' ) }`,
      { id: NOTICE_ID, isDismissible: false }
    );
  } else {
    dispatch( 'core/editor' ).unlockPostSaving( LOCK_ID );
    dispatch( 'core/notices' ).removeNotice( NOTICE_ID );
  }
};

const debouncedRevalidate = debounce( revalidate, 200 );

const init = () => {
  // Store churn is constant; only revalidate when the inputs the gate and
  // rules depend on actually change.
  let lastKey = '';

  subscribe( () => {
    const state = gatherEditorState();
    const key = JSON.stringify( [
      state.savedStatus,
      state.editedStatus,
      state.sidebarOpen,
      state.categories,
    ] );

    if ( key !== lastKey ) {
      lastKey = key;
      debouncedRevalidate();
    }
  } );

  // Live re-validation while typing in metabox fields. Capture phase so
  // events inside the metabox area are seen regardless of jQuery handlers.
  document.addEventListener(
    'input',
    ( event ) => {
      if ( event.target.matches && event.target.matches( '[data-validation], textarea.nm-validation-required' ) ) {
        debouncedRevalidate();
      }
    },
    true
  );

  // TinyMCE (Visual mode) edits never fire DOM input events on the
  // textarea; bind editor events as editors register. If tinyMCE isn't
  // present yet, the subscribe path still covers the publish flow.
  if ( window.tinyMCE && typeof window.tinyMCE.on === 'function' ) {
    window.tinyMCE.on( 'AddEditor', ( { editor } ) => {
      editor.on( 'input change', debouncedRevalidate );
    } );
  }
};

if ( document.readyState === 'loading' ) {
  document.addEventListener( 'DOMContentLoaded', init );
} else {
  init();
}
```

- [ ] **Step 2: Add the block editor enqueue** — append to `lib/meta/meta-validation.php`:

```php
/**
 * Block editor bundle. enqueue_block_editor_assets fires only on block
 * editor screens, where the classic bundle is skipped.
 */
function nm_meta_validation_enqueue_block_editor() {
  nm_meta_validation_enqueue( 'nm-meta-validation-block-editor', 'meta-validation-block-editor' );
}

add_action( 'enqueue_block_editor_assets', 'nm_meta_validation_enqueue_block_editor' );
```

- [ ] **Step 3: Lint and build**

Run: `php -l lib/meta/meta-validation.php`
Expected: no syntax errors.

Run: `npm run build:admin`
Expected: exit 0; `meta-validation-block-editor.asset.php` dependency list includes `wp-data` and `wp-notices`.

- [ ] **Step 4: Manual block editor matrix** — local wp-admin. Reach the block editor by editing a post and choosing "Switch to block editor" (Classic Editor plugin allows user switching), or append `&classic-editor__forget` to the edit URL:

1. Draft save with empty required fields → saves freely, no lock, no highlights.
2. Open publish flow (click Publish, sidebar opens) with empty standfirst → Publish button disabled, error notice lists field, row highlighted red in the metabox panel.
3. Fill the field while locked → unlocks live, notice clears, highlight clears.
4. Video post via category checkbox in the sidebar, empty YouTube ID → locked; untick video category → unlocks live.
5. Update an already-published post with a required field emptied → save locked until fixed.
6. Locked published post → "Switch to draft" → unlocks and reverts.
7. Short description (wysiwyg) whitespace-only in Visual mode → still counts as empty, locks.
8. Preview a draft with empty fields → preview works.

- [ ] **Step 5: Commit**

```bash
git add src/admin/meta-validation/block-editor.js lib/meta/meta-validation.php dist/admin/
git commit -m "feat: block editor meta validation via lockPostSaving

Live-locks publish-type saves while required metabox fields are empty,
with an error notice and row highlights; drafts save freely. Closes the
silent block editor bypass documented in PR #568."
```

---

### Task 6: Documentation sync

**Files:**
- Modify: `CHANGELOG.md` (Unreleased > Added)
- Modify: `docs/specs/2026-07-16-conditional-required-meta-validation-design.md` (Known limitation section)
- Modify: `docs/specs/2026-07-17-block-editor-meta-validation-design.md` (Status line)

**Interfaces:** none.

- [ ] **Step 1: CHANGELOG** — the existing Unreleased line ends with `(classic editor only — block editor support to follow)`. Replace that suffix so the line reads:

```markdown
- Required validation on post meta fields on publish and update — standfirst and short description on all posts, YouTube ID on video, Soundcloud URL on audio (classic and block editors)
```

- [ ] **Step 2: Parent spec** — in `docs/specs/2026-07-16-conditional-required-meta-validation-design.md`, replace the "Known limitation: classic editor only — follow-up REQUIRED" section's final paragraph with:

```markdown
**Resolved:** the follow-up shipped — see
`docs/specs/2026-07-17-block-editor-meta-validation-design.md` for the
block editor validation path.
```

(Keep the rest of the section as historical context for why it was needed.)

- [ ] **Step 3: New spec status** — change the Status line in `docs/specs/2026-07-17-block-editor-meta-validation-design.md` to:

```markdown
**Status:** Implemented (this branch)
```

- [ ] **Step 4: Full build check**

Run: `npm run build`
Expected: exit 0; `git status` shows no unexpected `dist/` changes beyond `dist/admin/`.

- [ ] **Step 5: Commit**

```bash
git add CHANGELOG.md docs/specs/
git commit -m "docs: mark block editor meta validation implemented"
```

---

## Self-Review Notes

- Spec coverage: build (§1→Task 1), core/dom/adapters (§2→Tasks 2–5), PHP loader + rename (§3→Task 4), lock behaviour (§4→Task 5), limitation documented in block-editor.js header comment (§5), edge-case table → Task 4 Step 5 + Task 5 Step 4 matrices, docs (→Task 6).
- Classic parity deltas (intentional, behaviour-equivalent): jQuery dropped (native `closest`/`querySelectorAll`); scroll uses `window.scrollTo` smooth instead of jQuery animate; per-field clear now applies on any valid field rather than per-rule-branch — supersets the old stale-highlight clearing, verified by matrix cases 4 and 7.
- Word-length highlight when gate inactive (block editor drafts): cleared like required failures — classic never validated drafts either.
