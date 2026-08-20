# ATF Options UX — PR 2: Mock-Layout Preview Module

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** A clickable mock layout of the above-the-fold section at the top of the "Above the Fold: Featured" options page, live-coloured by edit state, with collision detection and click-to-field.

**Architecture:** Three units. (1) `lib/admin/atf-preview.php` — hooks `cmb2_before_form` filtered to the ATF box, renders the zone skeleton server-filled from `nm_resolve_post()` (no flash on load), and enqueues the assets. (2) `lib/admin/js/atf-preview.js` — captures a baseline of the 8 field values at page load, repaints zones on field changes via the existing `nm/v1/resolve-posts` endpoint (per-ID cache), computes collisions, renders badges, wires click-to-field. (3) `lib/admin/css/atf-preview.css` — grid + state classes. No build system involvement; both assets are raw enqueued files.

**Tech Stack:** WordPress/CMB2 classic admin, jQuery, WP REST (`nm/v1/resolve-posts` from PR 1), plain CSS.

**Spec:** `docs/specs/2026-08-19-atf-options-ux-design.md` (component 4). PR 1 (`docs/plans/2026-08-20-atf-options-ux-pr1.md`) landed the fork, endpoint, and hints this builds on.

## Global Constraints

- Branch: work happens on `feature/atf-preview-module`, cut from `feature/atf-picker-fork` (PR 1's branch — the endpoint and `nmPostResolve` localization live there). PR targets `development` after PR 1 merges. Never commit to `development`; the user merges PRs.
- NO build-system (webpack) changes — CSS and JS are raw enqueued files under `lib/admin/`.
- Display-only: the preview is a map, not a second form. No editing inside it; the CMB2 save path stays untouched; JS/endpoint failure must never block form interaction or saving.
- Colour grammar is EDIT-STATE, not post-status (spec table): page load = neutral (no colour); green = value changed since page load; dashed grey = empty zone; red = broken (ID unresolvable OR resolved post status ≠ `publish`); amber = collision (same ID in two zones), both zones + a note strip naming them. Precedence red > amber > green.
- Zone visual grammar: primary + 2nd zones of each block show thumbnail + title; 3rd/4th are title-only lines. Latest-articles column is static grey ("automatic — latest News posts"), never editable.
- Display-string contract (matches the PR 1 hint renderers — keep in sync with `title_hint_html()` in `lib/meta/cmb2-post-search-field.php` and `renderHint()` in `lib/admin/js/post-resolve.js`): not found → `No post with ID {n}`; found non-publish → `{title} — {status_label}, won't display publicly` (em dash U+2014, curly apostrophe in "won't").
- Field IDs are fixed: `_cmb_above_the_fold_featured_1..4` = block 1 (1 = primary), `_cmb_above_the_fold_featured_5..8` = block 2 (5 = primary). CMB2 renders each field's row with class `cmb2-id-` + the field id with underscores replaced by dashes → `.cmb2-id--cmb-above-the-fold-featured-1` (note the double dash).
- Badge fields — block 1: `_cmb_above_the_fold_featured_1_show_related` (checkbox, "See Also"), `_cmb_above_the_fold_featured_1_more_on_section` (select, "More On: {selected label}", suppressed when value is `none` or empty), `_cmb_above_the_fold_featured_1_is_product_linked` (checkbox, "Product-linked"), `_cmb_above_the_fold_featured_1_has_embed` (checkbox, "Video embed"). Block 2: same pattern on `_5_` but NO `has_embed` field.
- `cmb2_before_form` fires as `do_action( 'cmb2_before_form', $cmb_id, $object_id, $object_type, $cmb )`; the ATF box id is `nm_above_the_fold_featured_options_page`. Saved values live in one option: `get_option( 'nm_front_page_above_the_fold_featured_options' )` → array keyed by field id.
- `nmPostResolve` (endpoint URL + nonce) is localized onto the `nm-post-resolve` script handle by the forked field; `nm-atf-preview` depends on that handle. If the global is missing, the preview stays static (server-rendered) — no errors.
- No i18n wrappers on new strings. Verification = `php -l` / `node --check` / logged-out curl smokes; manual wp-admin QA is controller/user-level.

---

### Task 1: Skeleton renderer + enqueues (`lib/admin/atf-preview.php`)

**Files:**
- Create: `lib/admin/atf-preview.php`
- Modify: `functions.php` (one `get_template_part` line, directly after the `lib/admin/post-resolve` line)

**Interfaces:**
- Consumes: `nm_resolve_post( $id )` from `lib/admin/post-resolve.php` → `{id, found, title, status, status_label, date, thumbnail}`.
- Produces: the DOM contract Task 3's JS drives — container `[data-nm-atf-preview]`; zones `[data-nm-zone]` with `data-field` (field id) and `data-thumb` ("1"/"0"); per-zone children `.nm-atf-preview__thumb` and `.nm-atf-preview__zone-title`; per-block badge containers `[data-nm-badges][data-block]`; collision note strip `[data-nm-collisions]` (hidden default); failure banner `[data-nm-banner]` (hidden default). State classes set by JS: `is-empty`, `is-broken`, `is-changed`, `is-collision` on the zone element. Server render also sets `is-empty`/`is-broken` for initial state (never `is-changed`/`is-collision` — collisions are JS-computed).

- [ ] **Step 1: Create `lib/admin/atf-preview.php`**

```php
<?php
/**
 * ATF options preview module.
 *
 * Renders a clickable mock layout of the front page's above-the-fold
 * section at the top of the "Above the Fold: Featured" options page
 * (Front Page > Above the Fold: Featured), server-filled from the saved
 * option so there is no flash of empty zones. lib/admin/js/atf-preview.js
 * keeps it live as fields change. Display-only: it never writes anything.
 *
 * Zone structure mirrors partials/front-page/above-the-fold.php: two
 * featured blocks (primary + 2nd with thumbnails, 3rd/4th title-only)
 * around the automatic latest-news column.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Zone map: preview zone => field id, block, visual style, thumbnail?
 * The single source for both the skeleton and the JS (via data attributes).
 *
 * @return array<string, array{field:string, block:int, style:string, thumb:bool, label:string}>
 */
function nm_atf_preview_zones() {
  return array(
    'b1-primary' => array( 'field' => '_cmb_above_the_fold_featured_1', 'block' => 1, 'style' => 'primary', 'thumb' => true, 'label' => 'Featured 1 — main' ),
    'b1-second'  => array( 'field' => '_cmb_above_the_fold_featured_2', 'block' => 1, 'style' => 'secondary', 'thumb' => true, 'label' => 'Featured 1 — 2nd' ),
    'b1-third'   => array( 'field' => '_cmb_above_the_fold_featured_3', 'block' => 1, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 1 — 3rd' ),
    'b1-fourth'  => array( 'field' => '_cmb_above_the_fold_featured_4', 'block' => 1, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 1 — 4th' ),
    'b2-primary' => array( 'field' => '_cmb_above_the_fold_featured_5', 'block' => 2, 'style' => 'primary', 'thumb' => true, 'label' => 'Featured 2 — main' ),
    'b2-second'  => array( 'field' => '_cmb_above_the_fold_featured_6', 'block' => 2, 'style' => 'secondary', 'thumb' => true, 'label' => 'Featured 2 — 2nd' ),
    'b2-third'   => array( 'field' => '_cmb_above_the_fold_featured_7', 'block' => 2, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 2 — 3rd' ),
    'b2-fourth'  => array( 'field' => '_cmb_above_the_fold_featured_8', 'block' => 2, 'style' => 'line', 'thumb' => false, 'label' => 'Featured 2 — 4th' ),
  );
}

/**
 * Display state for one zone value.
 *
 * String contract shared with title_hint_html() (PHP hints) and
 * renderHint()/paintZone() (JS) — keep the three in sync.
 *
 * @param string $value Raw saved field value (single post ID for ATF zones).
 * @return array{text:string, broken:bool, empty:bool, thumbnail:?string}
 */
function nm_atf_preview_zone_display( $value ) {
  $id = absint( is_scalar( $value ) ? $value : 0 );

  if ( ! $id ) {
    return array( 'text' => 'Empty — click to set', 'broken' => false, 'empty' => true, 'thumbnail' => null );
  }

  if ( ! function_exists( 'nm_resolve_post' ) ) {
    return array( 'text' => '', 'broken' => false, 'empty' => false, 'thumbnail' => null );
  }

  $info = nm_resolve_post( $id );

  if ( ! $info['found'] ) {
    return array( 'text' => 'No post with ID ' . $id, 'broken' => true, 'empty' => false, 'thumbnail' => null );
  }

  if ( 'publish' !== $info['status'] ) {
    return array(
      'text'      => $info['title'] . ' — ' . $info['status_label'] . ', won’t display publicly',
      'broken'    => true,
      'empty'     => false,
      'thumbnail' => $info['thumbnail'],
    );
  }

  return array( 'text' => $info['title'], 'broken' => false, 'empty' => false, 'thumbnail' => $info['thumbnail'] );
}

/**
 * Render the preview above the ATF options form and enqueue its assets.
 *
 * Hooked to cmb2_before_form and filtered to the ATF box, so it renders
 * exactly once, only on that options page.
 */
function nm_atf_preview_render( $cmb_id, $object_id, $object_type, $cmb ) {
  if ( 'nm_above_the_fold_featured_options_page' !== $cmb_id ) {
    return;
  }

  $theme_version = wp_get_theme()->get( 'Version' );

  wp_enqueue_style(
    'nm-atf-preview',
    get_template_directory_uri() . '/lib/admin/css/atf-preview.css',
    array(),
    $theme_version
  );

  // nm-post-resolve is registered later in this same request (the forked
  // field enqueues it from cmb2_after_form); WP resolves dependencies at
  // print time, so depending on it here is safe. It carries the
  // nmPostResolve endpoint+nonce global the preview JS reuses.
  wp_enqueue_script(
    'nm-atf-preview',
    get_template_directory_uri() . '/lib/admin/js/atf-preview.js',
    array( 'jquery', 'nm-post-resolve' ),
    $theme_version,
    true
  );

  $options = get_option( 'nm_front_page_above_the_fold_featured_options', array() );
  $zones   = nm_atf_preview_zones();

  $render_zone = function ( $key ) use ( $zones, $options ) {
    $zone    = $zones[ $key ];
    $value   = isset( $options[ $zone['field'] ] ) ? $options[ $zone['field'] ] : '';
    $display = nm_atf_preview_zone_display( $value );

    $classes = array( 'nm-atf-preview__zone', 'nm-atf-preview__zone--' . $zone['style'] );
    if ( $display['empty'] ) {
      $classes[] = 'is-empty';
    }
    if ( $display['broken'] ) {
      $classes[] = 'is-broken';
    }

    $thumb_style = ( $zone['thumb'] && $display['thumbnail'] )
      ? ' style="background-image:url(' . esc_url( $display['thumbnail'] ) . ')"'
      : '';

    echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-nm-zone data-field="' . esc_attr( $zone['field'] ) . '" data-thumb="' . ( $zone['thumb'] ? '1' : '0' ) . '" data-label="' . esc_attr( $zone['label'] ) . '" role="button" tabindex="0" title="Click to edit ' . esc_attr( $zone['label'] ) . '">';
    if ( $zone['thumb'] ) {
      echo '<span class="nm-atf-preview__thumb"' . $thumb_style . '></span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_url above.
    }
    echo '<span class="nm-atf-preview__zone-title">' . esc_html( $display['text'] ) . '</span>';
    echo '</div>';
  };
  ?>
  <div class="nm-atf-preview" data-nm-atf-preview>
    <p class="nm-atf-preview__heading">Above the fold — preview. Click a zone to edit it.</p>
    <p class="nm-atf-preview__banner" data-nm-banner hidden>Couldn&#8217;t load post data — preview may be stale</p>
    <p class="nm-atf-preview__collisions" data-nm-collisions hidden></p>
    <div class="nm-atf-preview__grid">
      <div class="nm-atf-preview__block" data-block="1">
        <?php $render_zone( 'b1-primary' ); ?>
        <div class="nm-atf-preview__badges" data-nm-badges data-block="1"></div>
        <?php $render_zone( 'b1-second' ); ?>
        <?php $render_zone( 'b1-third' ); ?>
        <?php $render_zone( 'b1-fourth' ); ?>
      </div>
      <div class="nm-atf-preview__latest">
        <span class="nm-atf-preview__latest-title">Latest</span>
        <span class="nm-atf-preview__latest-note">automatic — latest News posts</span>
      </div>
      <div class="nm-atf-preview__block" data-block="2">
        <?php $render_zone( 'b2-primary' ); ?>
        <div class="nm-atf-preview__badges" data-nm-badges data-block="2"></div>
        <?php $render_zone( 'b2-second' ); ?>
        <?php $render_zone( 'b2-third' ); ?>
        <?php $render_zone( 'b2-fourth' ); ?>
      </div>
    </div>
  </div>
  <?php
}
add_action( 'cmb2_before_form', 'nm_atf_preview_render', 10, 4 );
```

- [ ] **Step 2: Load it from functions.php**

Directly after the existing `get_template_part( 'lib/admin/post-resolve' );` line add:

```php
get_template_part( 'lib/admin/atf-preview' );
```

- [ ] **Step 3: Lint + smoke**

```bash
php -l lib/admin/atf-preview.php && php -l functions.php
curl -s -o /dev/null -w '%{http_code}' http://novaramediacom.local/   # expect 200
```

- [ ] **Step 4: Commit**

```bash
git add lib/admin/atf-preview.php functions.php
git commit -m "feat: ATF options preview skeleton, server-filled zones"
```

---

### Task 2: Preview styles (`lib/admin/css/atf-preview.css`)

**Files:**
- Create: `lib/admin/css/atf-preview.css`

**Interfaces:**
- Consumes: the DOM contract from Task 1 (class names verbatim).
- Produces: the state classes' visual grammar Task 3 toggles: `is-empty` dashed grey, `is-broken` red, `is-collision` amber, `is-changed` green, `is-flash` row highlight (used on the CMB2 field row, not a zone).

- [ ] **Step 1: Create `lib/admin/css/atf-preview.css`**

```css
/**
 * ATF options preview module (lib/admin/atf-preview.php).
 * State classes are toggled by lib/admin/js/atf-preview.js:
 * neutral (no class) = saved & untouched; is-changed = edited this
 * session; is-empty = no value; is-broken = won't work publicly;
 * is-collision = same post in two zones. Precedence (later rules win):
 * green < amber < red.
 */

.nm-atf-preview {
  margin: 12px 0 20px;
  padding: 12px;
  background: #fff;
  border: 1px solid #c3c4c7;
}

.nm-atf-preview__heading {
  margin: 0 0 8px;
  font-weight: 600;
}

.nm-atf-preview__banner {
  margin: 0 0 8px;
  padding: 6px 8px;
  background: #fcf9e8;
  border-left: 4px solid #dba617;
}

.nm-atf-preview__collisions {
  margin: 0 0 8px;
  padding: 6px 8px;
  background: #fcf0e8;
  border-left: 4px solid #d63638;
  color: #8a2424;
}

.nm-atf-preview__grid {
  display: grid;
  grid-template-columns: 5fr 3fr 5fr;
  gap: 10px;
  align-items: start;
}

.nm-atf-preview__block {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.nm-atf-preview__zone {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  border: 1px solid #dcdcde;
  background: #f6f7f7;
  cursor: pointer;
}

.nm-atf-preview__zone:hover,
.nm-atf-preview__zone:focus {
  border-color: #2271b1;
  outline: none;
}

.nm-atf-preview__zone--primary .nm-atf-preview__zone-title {
  font-size: 15px;
  font-weight: 700;
}

.nm-atf-preview__zone--secondary .nm-atf-preview__zone-title {
  font-size: 13px;
  font-weight: 600;
}

.nm-atf-preview__zone--line {
  padding: 5px 8px;
}

.nm-atf-preview__zone--line .nm-atf-preview__zone-title {
  font-size: 12px;
}

.nm-atf-preview__thumb {
  flex: 0 0 44px;
  height: 33px;
  background: #dcdcde center / cover no-repeat;
}

.nm-atf-preview__zone--primary .nm-atf-preview__thumb {
  flex-basis: 66px;
  height: 44px;
}

.nm-atf-preview__badges {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  min-height: 14px;
  margin: -2px 0 2px;
}

.nm-atf-preview__badge {
  padding: 1px 6px;
  background: #f0f0f1;
  border: 1px solid #dcdcde;
  border-radius: 9px;
  font-size: 11px;
  color: #50575e;
}

.nm-atf-preview__latest {
  display: flex;
  flex-direction: column;
  gap: 4px;
  align-self: stretch;
  justify-content: center;
  padding: 8px;
  border: 1px solid #dcdcde;
  background: #f0f0f1;
  color: #787c82;
  text-align: center;
}

.nm-atf-preview__latest-title {
  font-weight: 600;
}

.nm-atf-preview__latest-note {
  font-size: 11px;
  font-style: italic;
}

/* --- Edit-state grammar (order = precedence: green < amber < red) --- */

.nm-atf-preview__zone.is-empty {
  border-style: dashed;
  border-color: #a7aaad;
  background: #fff;
  color: #787c82;
  font-style: italic;
}

.nm-atf-preview__zone.is-changed {
  border-color: #00a32a;
  box-shadow: inset 3px 0 0 #00a32a;
}

.nm-atf-preview__zone.is-collision {
  border-color: #dba617;
  box-shadow: inset 3px 0 0 #dba617;
  background: #fcf9e8;
}

.nm-atf-preview__zone.is-broken {
  border-color: #d63638;
  box-shadow: inset 3px 0 0 #d63638;
  background: #fcf0f1;
}

.nm-atf-preview__zone.is-broken .nm-atf-preview__zone-title {
  color: #8a2424;
}

/* Flash applied to the CMB2 field row after click-to-field. */
.cmb-row.nm-atf-flash {
  background-color: #fff8c5;
  transition: background-color 1.2s ease-out;
}
```

- [ ] **Step 2: Verify served**

```bash
curl -s -o /dev/null -w '%{http_code}' http://novaramediacom.local/wp-content/themes/novaramedia-com/lib/admin/css/atf-preview.css   # expect 200
```

- [ ] **Step 3: Commit**

```bash
git add lib/admin/css/atf-preview.css
git commit -m "feat: ATF preview styles and edit-state grammar"
```

---

### Task 3: Live behaviour (`lib/admin/js/atf-preview.js`)

**Files:**
- Create: `lib/admin/js/atf-preview.js`

**Interfaces:**
- Consumes: DOM contract from Task 1; state classes from Task 2; `nmPostResolve` global ({endpoint, nonce}) localized on the `nm-post-resolve` handle; REST response items `{id, found, title, status, status_label, date, thumbnail}`; CMB2 row selector `.cmb2-id-` + field id with `_`→`-`.
- Produces: nothing consumed later — final unit.

- [ ] **Step 1: Create `lib/admin/js/atf-preview.js`**

```js
/**
 * ATF options preview — live behaviour.
 *
 * Paints the preview zones from the 8 picker fields' CURRENT values,
 * colours them by edit state (see atf-preview.css), detects collisions,
 * renders the primary-zone badges, and wires click-to-field. Display
 * strings share the contract of title_hint_html() (PHP) and
 * renderHint() (post-resolve.js) — keep the three in sync.
 * Display-only: failures show the banner and never block the form.
 */
jQuery(function ($) {
  'use strict';

  var $preview = $('[data-nm-atf-preview]');

  if (!$preview.length) {
    return;
  }

  var DEBOUNCE_MS = 300;
  var cache = {}; // id -> resolve result, shared across repaints
  var baseline = {}; // field id -> value at page load
  var timer = null;
  var paintSeq = 0;

  var BADGE_FIELDS = {
    1: [
      { field: '_cmb_above_the_fold_featured_1_show_related', type: 'checkbox', label: 'See Also ✓' },
      { field: '_cmb_above_the_fold_featured_1_more_on_section', type: 'select', label: 'More On: ' },
      { field: '_cmb_above_the_fold_featured_1_is_product_linked', type: 'checkbox', label: 'Product-linked ✓' },
      { field: '_cmb_above_the_fold_featured_1_has_embed', type: 'checkbox', label: 'Video embed ✓' }
    ],
    2: [
      { field: '_cmb_above_the_fold_featured_5_show_related', type: 'checkbox', label: 'See Also ✓' },
      { field: '_cmb_above_the_fold_featured_5_more_on_section', type: 'select', label: 'More On: ' },
      { field: '_cmb_above_the_fold_featured_5_is_product_linked', type: 'checkbox', label: 'Product-linked ✓' }
    ]
  };

  function rowSelector(fieldId) {
    return '.cmb2-id-' + fieldId.replace(/_/g, '-');
  }

  function fieldInput(fieldId) {
    return $(rowSelector(fieldId)).find('input[type="text"]').first();
  }

  function zoneValue($zone) {
    var raw = fieldInput($zone.attr('data-field')).val() || '';
    var id = parseInt(String(raw).trim(), 10);

    return id > 0 ? id : 0;
  }

  function collectZones() {
    return $preview.find('[data-nm-zone]').map(function () {
      var $zone = $(this);

      return { $zone: $zone, field: $zone.attr('data-field'), label: $zone.attr('data-label'), id: zoneValue($zone) };
    }).get();
  }

  // Shared display contract — see file docblock.
  function zoneText(entry) {
    var info = cache[entry.id];

    if (!info) {
      return { text: '', broken: false };
    }
    if (!info.found) {
      return { text: 'No post with ID ' + entry.id, broken: true };
    }
    if (info.status !== 'publish') {
      return { text: info.title + ' — ' + (info.status_label || info.status) + ', won’t display publicly', broken: true };
    }

    return { text: info.title, broken: false };
  }

  function paint() {
    var zones = collectZones();
    var counts = {};
    var collisions = [];

    zones.forEach(function (entry) {
      if (entry.id) {
        counts[entry.id] = (counts[entry.id] || 0) + 1;
      }
    });

    zones.forEach(function (entry) {
      var $zone = entry.$zone;
      var $title = $zone.find('.nm-atf-preview__zone-title');
      var changed = String(baseline[entry.field]) !== String(entry.id || '');

      $zone.removeClass('is-empty is-broken is-changed is-collision');

      if (!entry.id) {
        $zone.addClass('is-empty');
        if (changed) {
          $zone.addClass('is-changed');
        }
        $title.text('Empty — click to set');
        setThumb($zone, null);
        return;
      }

      var display = zoneText(entry);
      var colliding = counts[entry.id] > 1;

      $title.text(display.text);
      setThumb($zone, cache[entry.id] && cache[entry.id].thumbnail);

      // Precedence red > amber > green: broken wins outright; collision
      // suppresses changed; changed only when valid and unique.
      if (display.broken) {
        $zone.addClass('is-broken');
      } else if (colliding) {
        $zone.addClass('is-collision');
      } else if (changed) {
        $zone.addClass('is-changed');
      }

      if (colliding && collisions.indexOf(entry.id) === -1) {
        collisions.push(entry.id);
      }
    });

    var $strip = $preview.find('[data-nm-collisions]');

    if (collisions.length) {
      var lines = collisions.map(function (id) {
        var names = zones.filter(function (entry) { return entry.id === id; })
          .map(function (entry) { return entry.label; });

        return 'Same post in ' + names.join(' and ');
      });

      $strip.text(lines.join('. ')).prop('hidden', false);
    } else {
      $strip.prop('hidden', true).empty();
    }
  }

  function setThumb($zone, url) {
    if ($zone.attr('data-thumb') !== '1') {
      return;
    }

    $zone.find('.nm-atf-preview__thumb').css('background-image', url ? 'url(' + url + ')' : '');
  }

  function resolveAndPaint() {
    var zones = collectZones();
    var missing = [];

    zones.forEach(function (entry) {
      if (entry.id && !cache[entry.id] && missing.indexOf(entry.id) === -1) {
        missing.push(entry.id);
      }
    });

    if (!missing.length || typeof nmPostResolve === 'undefined') {
      paint();
      return;
    }

    var mySeq = ++paintSeq;
    var separator = nmPostResolve.endpoint.indexOf('?') === -1 ? '?' : '&';

    fetch(nmPostResolve.endpoint + separator + 'ids=' + missing.join(','), {
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
        results.forEach(function (info) {
          cache[info.id] = info;
        });
        if (mySeq !== paintSeq) {
          return; // A newer resolve superseded this one.
        }
        $preview.find('[data-nm-banner]').prop('hidden', true);
        paint();
      })
      .catch(function () {
        if (mySeq !== paintSeq) {
          return;
        }
        $preview.find('[data-nm-banner]').prop('hidden', false);
        paint(); // Paint what we know; unknown ids render with empty text.
      });
  }

  function renderBadges() {
    $preview.find('[data-nm-badges]').each(function () {
      var $container = $(this);
      var fields = BADGE_FIELDS[$container.attr('data-block')] || [];

      $container.empty();

      fields.forEach(function (badge) {
        var $row = $(rowSelector(badge.field));

        if (!$row.length) {
          return;
        }

        if (badge.type === 'checkbox') {
          if ($row.find('input[type="checkbox"]').is(':checked')) {
            $container.append($('<span>', { 'class': 'nm-atf-preview__badge', text: badge.label }));
          }
          return;
        }

        var $option = $row.find('select option:selected');
        var value = $row.find('select').val();

        if (value && value !== 'none' && $option.length) {
          $container.append($('<span>', { 'class': 'nm-atf-preview__badge', text: badge.label + $option.text() }));
        }
      });
    });
  }

  // --- Wiring ---

  // Baseline: field values at page load, for the changed/unchanged grammar.
  collectZones().forEach(function (entry) {
    baseline[entry.field] = String(entry.id || '');
  });

  resolveAndPaint();
  renderBadges();

  // Zone fields: repaint (debounced) on typing or modal pick.
  $(document).on('change input', '.cmb-type-post-search-text input[type="text"]', function () {
    clearTimeout(timer);
    timer = setTimeout(resolveAndPaint, DEBOUNCE_MS);
  });

  // Badge fields: cheap, re-render immediately.
  $(document).on('change', '.cmb2-wrap input[type="checkbox"], .cmb2-wrap select', renderBadges);

  // Click (or Enter/Space) on a zone: scroll to and focus its field row.
  function goToField($zone) {
    var $row = $(rowSelector($zone.attr('data-field')));

    if (!$row.length) {
      return;
    }

    $row[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    $row.addClass('nm-atf-flash');
    setTimeout(function () {
      $row.removeClass('nm-atf-flash');
    }, 1500);
    $row.find('input[type="text"]').first().trigger('focus');
  }

  $preview.on('click', '[data-nm-zone]', function () {
    goToField($(this));
  });

  $preview.on('keydown', '[data-nm-zone]', function (evt) {
    if (evt.key === 'Enter' || evt.key === ' ') {
      evt.preventDefault();
      goToField($(this));
    }
  });
});
```

- [ ] **Step 2: Lint + smoke**

```bash
node --check lib/admin/js/atf-preview.js
curl -s -o /dev/null -w '%{http_code}' http://novaramediacom.local/wp-content/themes/novaramedia-com/lib/admin/js/atf-preview.js   # expect 200
curl -s -o /dev/null -w '%{http_code}' http://novaramediacom.local/   # expect 200
```

- [ ] **Step 3: Commit**

```bash
git add lib/admin/js/atf-preview.js
git commit -m "feat: ATF preview live behaviour — edit-state colours, collisions, click-to-field"
```

---

### Task 4: CHANGELOG

**Files:**
- Modify: `CHANGELOG.md`

**Interfaces:** none.

- [ ] **Step 1: Add to the existing `### Added` block under `## [Unreleased]`**

```markdown
- Above the Fold options page shows a clickable mock layout of the section — zones display the chosen posts, colour-code unsaved/broken/duplicate choices, and jump to the matching field on click
```

Keep exactly one `### Added` heading in the section (a keep-a-changelog structure defect here was already fixed once in PR 1 — do not reintroduce it).

- [ ] **Step 2: Commit**

```bash
git add CHANGELOG.md
git commit -m "docs: changelog for ATF preview module"
```

---

## Manual QA (controller/user-level, after all tasks)

1. ATF options page loads: preview at top, zones show saved titles + thumbnails on primary/2nd zones, all neutral (no colour), badges match saved checkboxes/selects.
2. Change a picker via modal → zone repaints with new title within ~300ms, green edge.
3. Type garbage ID → zone red "No post with ID n".
4. Set the same post in a block-1 and a block-2 zone → both amber + note strip "Same post in Featured 1 — main and Featured 2 — 4th".
5. Clear a field → dashed "Empty — click to set" (+ green edge, since it changed).
6. Save → reload → new neutral baseline.
7. Click each zone (and Enter on a focused zone) → page scrolls to the right field row, flash highlight, input focused.
8. Toggle See Also / More On / Product-linked / Video embed → badges update immediately.
9. Block resolve-posts in devtools → banner appears, form still saves, no console errors.
10. Post edit screen (Contributors): preview absent, hints still work — no bleed outside the ATF page.

## Self-review notes (completed)

- **Spec coverage:** component 4 fully covered — injection via cmb2_before_form filtered to the box (Task 1), structure mirroring above-the-fold.php with thumb/line zone grammar + static latest column (Tasks 1-2), edit-state colour table incl. precedence red > amber > green and changed-suppression rules (Task 3 paint()), collision strip naming zones (Task 3), click-to-field with scroll/focus/flash (Task 3), badges (Task 3), liveness with baseline capture (Task 3), failure banner (Task 3). Failure handling matches spec's "never blocks saving".
- **Type consistency:** field ids, row selector derivation, state class names, and the display-string contract are identical across Tasks 1-3; `nm_atf_preview_zone_display()` (PHP) and `zoneText()` (JS) implement the same three branches.
- **Placeholder scan:** clean — every step carries runnable code or exact commands.
- Known accepted duplication: the display-string contract now exists in four renderers (hint PHP/JS, preview PHP/JS); each file's docblock names the others.
