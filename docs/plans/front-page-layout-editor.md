# Front Page Layout Editor Plan

## Goal

Replace the hardcoded order of front-page sections (banners + product show-blocks)
with a single rearrangeable list in the WP admin. First real step toward a
WYSIWYG-ish front page editor, built by reusing patterns the theme already has.

Permanent bookends stay hardcoded: **above-the-fold** (top) and **mega-block**
(bottom). Everything between them becomes editable order.

## Current state (for context)

`front-page.php` renders a fixed sequence between the permanent bookends:

```
above-the-fold   (permanent, takes args)
banner 1         (NM_get_option nm_front_page_banner_option_1)
highlight-block  (optional, own subpage toggle, takes excluded_posts_ids arg)
novara-live
banner 2         (…option_2)
audio
banner 3         (…option_3)
downstream
banner 4         (…option_4)
acfm             (partials/front-page/show-blocks/audio-acfm.php)
mega-block       (permanent)
```

Two relevant existing mechanics we reuse:

1. **Banner slots** — 4 fixed `select` fields, each picks a partial path from one
   hardcoded `$banner_options` map (`partial path => label`), plus dynamic
   newsletter-signup options. Rendered by `render_front_page_banner($partial)`,
   which just includes the partial. (`lib/theme-options/options-front-page.php`)
2. **Products Bar** (`front_page_links_bar`) — a CMB2 `group` field with
   `'sortable' => true`, add/remove buttons. Proof that drag-to-reorder
   repeatable groups already work in this codebase.

Insight that makes this cheap: **banners and product blocks are both just
partials rendered by path.** They unify into one ordered list of partial picks.

## Design decision (settled)

Two ways to handle blocks that need extra config (e.g. highlight-block needs a
Section taxonomy term):

- **Option 1 — separate metaboxes (chosen for v1):** layout list stores only the
  block slug + order. Per-block config lives on its own settings subpage, exactly
  like today's Highlight Section / Products Bar pages. Config is **per block-type**
  (one config slot per block), not per-instance.
- **Option 2 — inline conditional fields (deferred):** per-row conditional fields
  reveal a block's config inside the sortable row. Gives per-instance config and
  multiple configured instances of the same block. Cost: CMB2 has **no native
  per-row conditionals** — needs the third-party `cmb2-conditionals` add-on or
  custom JS, and repeatable-group + conditional + reorder is the fiddliest corner
  of CMB2.

**Decision:** ship Option 1. Every product block today is a singleton (one audio,
one acfm) so per-type config covers every real case. The **block registry**
(below) is the backbone — moving from Option 1 to Option 2 later sources the
config field from a different place, not a rewrite. Picking cheap now is not a trap.

### Highlight block: out of scope for v1

It is off by default, has never been used, has no editorial capacity behind it,
and is the only block with awkward arg-passing (`excluded_posts_ids` dedupe
against above-the-fold). Leave it hardcoded-and-disabled where it is. Do **not**
put it in the v1 registry. Fold it in later only if someone actually wants it.

That removes the single hard case from v1: every block in the picker is an
**arg-less singleton partial**.

## The block registry (backbone)

A single source of truth, PHP array, defined once. The layout editor builds its
select options from it; rendering loops the saved order and calls each partial.

```php
/**
 * @return array<string, array{label:string, partial:string, type:string}>
 *   Keyed by stable slug. `type` is just for optgroup grouping in the select.
 */
function nm_get_front_page_block_registry() {
  $blocks = array(
    // Product show-blocks (arg-less singleton partials)
    'novara-live' => array(
      'label'   => 'Show block: Novara Live',
      'partial' => 'partials/front-page/show-blocks/novara-live',
      'type'    => 'product',
    ),
    'audio' => array(
      'label'   => 'Show block: Audio (Novara FM + ACFM)',
      'partial' => 'partials/front-page/show-blocks/audio',
      'type'    => 'product',
    ),
    'audio-acfm' => array(
      'label'   => 'Show block: ACFM',
      'partial' => 'partials/front-page/show-blocks/audio-acfm',
      'type'    => 'product',
    ),
    'downstream' => array(
      'label'   => 'Show block: Downstream',
      'partial' => 'partials/front-page/show-blocks/downstream',
      'type'    => 'product',
    ),
  );

  // Banners: fold the existing $banner_options map in under type 'banner',
  // keyed by a slug derived from the partial path. Includes the dynamic
  // newsletter-signup options. (Refactor $banner_options out of
  // nm_register_front_page_options_metabox into a shared function so both the
  // registry and any legacy code use one list.)
  foreach ( nm_get_front_page_banner_options() as $partial => $label ) {
    if ( ! $partial ) { continue; } // skip the 'None' entry
    $blocks[ 'banner:' . $partial ] = array(
      'label'   => $label,
      'partial' => $partial,
      'key'     => $partial, // original banner key, passed to render_front_page_banner()
      'type'    => 'banner',
    );
  }

  return $blocks;
}
```

Notes:
- Slugs are **stable identifiers** — saved order references slugs, not labels or
  array indices, so relabelling or reordering the registry never corrupts saved
  layouts.
- Newsletter-signup banners are dynamic (per newsletter ID) — already handled by
  `get_newsletter_signup_options()`; they flow in automatically.

## Data model

Store the layout as an ordered CMB2 `group` (sortable), one row per section.
v1 row has a single field: a `select` of registry slugs, grouped by `type` into
optgroups (Product blocks / Banners).

- Option key: dedicated `nm_front_page_layout_options` subpage ("Layout") — keeps
  the main Front Page page readable. **(decided)**
- Field id: `nm_front_page_layout` (group), each row stores `block` => slug.
- **Disabling a slot = remove the row.** No per-row "None" option. **(decided)**
- **Capped row count** — soft max of ~12 rows (current between-bookend sections =
  8 in v1, so 12 gives headroom). **(decided)** Note: CMB2 groups have no native
  max-rows; enforce by hiding the "Add" button past the cap via a small admin JS,
  or accept it as a documented soft limit for v1.

Rendering in `front-page.php` (between the permanent bookends):

```php
$registry = nm_get_front_page_block_registry();
$layout   = NM_get_option( 'nm_front_page_layout', 'nm_front_page_layout_options', array() );

foreach ( $layout as $row ) {
  $slug = $row['block'] ?? '';
  if ( ! isset( $registry[ $slug ] ) ) {
    continue;
  }
  $block = $registry[ $slug ];

  if ( $block['type'] === 'banner' ) {
    // Banners keep their existing render path — it handles newsletter-signup
    // partials, retired-option no-ops, and a path-traversal security guard.
    render_front_page_banner( $block['key'] );
  } else {
    get_template_part( $block['partial'] );
  }
}
```

**`render_front_page_banner` stays** (`lib/renderers.php:537`). It is not a dumb
wrapper — it special-cases `newsletter-signup-{id}` (loads `partials/email-signup`
with the newsletter ID + mailchimp-key check), no-ops retired option slugs
(`email-the-cortado`, `email-the-pick`), and enforces a `partials/`-only,
no-`..` security guard. Banner registry entries therefore store the original
banner **key** (partial path or `newsletter-signup-{id}`) in a `key` field, and
the loop passes it straight to `render_front_page_banner`. **(decided)**

## Migration

The current layout must seed the new group so nothing changes visually on launch.

1. **Refactor** `$banner_options` out of `nm_register_front_page_options_metabox`
   into a shared `nm_get_front_page_banner_options()` (used by both the legacy
   banner selects and the registry).
2. **One-shot default seed** for `nm_front_page_layout`: if the layout option is
   empty, default it to the current hardcoded order, reading the existing
   `nm_front_page_banner_option_1..4` values for the banner rows:

   ```
   [highlight-block is excluded — stays hardcoded/disabled]
   novara-live
   banner_option_1 value  (if not 'None')
   audio
   banner_option_2 value
   downstream
   banner_option_3 value
   audio-acfm
   banner_option_4 value
   ```

   Implement as a default-on-read (compute the seed when option is empty) rather
   than a destructive write, so it is reversible and safe to deploy.
3. **Keep** the old 4 banner `select` fields for one release as a fallback / until
   the seed is confirmed working in production, then remove them in a follow-up.

## v1 / v2 split

**v1 (this plan):**
- Block registry.
- `nm_get_front_page_banner_options()` refactor.
- Layout subpage: one sortable group, single select per row.
- `front-page.php` loops the layout between above-the-fold and mega-block.
- Default-seed migration preserving current order.
- highlight-block stays hardcoded + disabled, NOT in the registry.

**v2 (later, only if needed):**
- Per-instance / conditional config (Option 2) via `cmb2-conditionals` or custom
  JS — only if a real need for per-instance config or duplicate configured blocks
  appears.
- Fold highlight-block into the registry with its Section-taxonomy config (decide:
  per-type subpage vs inline conditional). Resolve the `excluded_posts_ids` dedupe
  (e.g. always pass the above-the-fold IDs regardless of the block's position).
- Possibly split the `audio` block (currently hardcodes Novara FM + ACFM) now that
  ACFM is its own selectable block.

## Resolved decisions

- **Keep `render_front_page_banner`** and route banner rows through it (carries
  newsletter handling, retired-option no-ops, security guard).
- **Dedicated "Layout" subpage** under Front Page options.
- **Disable a slot by removing its row** — no per-row "None".
- **Capped at ~12 rows** — soft limit (no native CMB2 max; hide Add button via JS
  or document as soft cap).

## Risk / cost

- No new dependencies in v1 — pure CMB2 group + existing partial-include render.
- Reuses two proven in-repo patterns (sortable group, banner partial select).
- Main risk is the migration seed; mitigated by default-on-read + keeping legacy
  banner fields one release.
