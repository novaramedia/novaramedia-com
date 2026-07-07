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
mega-block       (permanent)
```

(A standalone ACFM show-block is being built in parallel on
`feature/front-page-acfm-block`; it will be added to the registry when that
branch is rebuilt on top of this editor. It does not exist on this branch.)

Two relevant existing mechanics we reuse:

1. **Banner slots** — 4 fixed `select` fields, each picks a partial path from one
   hardcoded `$banner_options` map (`partial path => label`), plus dynamic
   newsletter-signup options. Rendered by `render_front_page_banner($partial)`,
   which just includes the partial. (`lib/theme-options/options-front-page.php`)
2. **Products Bar** (`front_page_links_bar`) — a CMB2 `group` field with
   `'sortable' => true`, add/remove buttons. Proof that drag-to-reorder
   repeatable groups already work in this codebase.

Insight that makes this cheap: **banners and product blocks are both just
partials.** They unify into one ordered list keyed by stable slug. (The render
path resolves each slug to its partial via the trusted registry — the slug
itself is never a path, so no stored data reaches `get_template_part()`.)

> **Revised in 4.7.0 (final review, pre-ship).** The code snippets below were
> updated to the shipped design after review flagged the original two-registry
> split as overbuilt. Key changes: a **single typed registry** (no separate
> `nm_get_front_page_product_blocks()` half), **opaque banner slugs**
> (`banner-<name>`) instead of `banner:<partial-path>`, **render dispatches on
> the `type` field** (no `strpos`/prefix routing), and `render_front_page_banner`
> is **replaced** by `nm_render_newsletter_signup` — banners now render straight
> from the registry, so the path-traversal guard is no longer needed (paths come
> from code, never from saved data).

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

**Decision:** ship Option 1. Every product block today is a singleton (one audio
block, one downstream, etc.) so per-type config covers every real case. The
**block registry** (below) is the backbone — moving from Option 1 to Option 2
later sources the config field from a different place, not a rewrite. Picking
cheap now is not a trap.

### Highlight block: included as a registry block

Earlier draft kept highlight-block hardcoded and out of the registry. Reversed
during review: leaving it pinned outside the list meant enabling it would change
the historic order relative to the seed. Instead it is a normal registry block
(Option 1): it appears in the Layout list, its content config stays on its
existing Highlight Section subpage, and its on/off toggle there still governs
visibility (disabled → renders nothing).

Its one wrinkle — it needs `excluded_posts_ids` to dedupe against the
above-the-fold posts — is handled generically: the render loop passes a shared
`$context` array to every product block via `get_template_part`'s args, and
blocks that don't need it simply ignore it. No per-block special-casing.

## The block registry (backbone)

A single PHP array, the source of truth for every **statically-defined** section
(products + banners), keyed by stable opaque slug. It is **DB-free**, so the same
registry is consulted on both the admin select (for labels) and the front-end
render path (for `type` + `partial`) without ever triggering a query. Newsletter
signups are *not* in the registry — they are dynamic posts, enumerated only when
building the admin select and rendered by ID (see below).

```php
/**
 * @return array<string, array{type:string, label:string, partial:string}>
 *   Keyed by stable opaque slug. `type` ('product'|'banner') drives rendering.
 */
function nm_get_front_page_block_registry() {
  $blocks = array(
    // Product blocks (singleton partials; receive the shared render context)
    'highlight-block' => array( 'type' => 'product', 'label' => 'Show block: Highlight section (configured on its own subpage)', 'partial' => 'partials/front-page/highlight-block' ),
    'novara-live'     => array( 'type' => 'product', 'label' => 'Show block: Novara Live', 'partial' => 'partials/front-page/show-blocks/novara-live' ),
    'dyor'            => array( 'type' => 'product', 'label' => 'Show block: Do Your Own Research', 'partial' => 'partials/front-page/show-blocks/dyor' ),
    'dyor-alt'        => array( 'type' => 'product', 'label' => 'Show block: Do Your Own Research (ALT — design comparison)', 'partial' => 'partials/front-page/show-blocks/dyor-alt' ),
    'audio'           => array( 'type' => 'product', 'label' => 'Show block: Audio (Novara FM + ACFM)', 'partial' => 'partials/front-page/show-blocks/audio' ),
    'audio-acfm'      => array( 'type' => 'product', 'label' => 'Show block: ACFM (standalone)', 'partial' => 'partials/front-page/show-blocks/audio-acfm' ),
    'downstream'      => array( 'type' => 'product', 'label' => 'Show block: Downstream', 'partial' => 'partials/front-page/show-blocks/downstream' ),
  );

  // Static banners fold in under type 'banner', keyed by opaque slug
  // (`banner-<name>`) from the single nm_get_front_page_static_banners() list.
  // No partial path is ever stored in a layout — only the slug.
  foreach ( nm_get_front_page_static_banners() as $slug => $banner ) {
    $blocks[ $slug ] = array(
      'type'    => 'banner',
      'label'   => 'Banner: ' . $banner['label'],
      'partial' => $banner['partial'],
    );
  }

  // DYOR blocks are gated off production here; because render resolves against
  // this same registry, a gated slug never renders even if a saved layout names it.
  if ( nm_is_production() ) {
    unset( $blocks['dyor'], $blocks['dyor-alt'] );
  }

  return $blocks;
}
```

Notes:
- Slugs are **stable, opaque identifiers** — saved order references slugs, not
  labels, paths or indices, so relabelling, re-pathing or reordering the registry
  never corrupts saved layouts, and no path from saved data reaches
  `get_template_part()`.
- Newsletter-signup banners are dynamic (per newsletter ID). They are appended to
  the admin select by `nm_get_front_page_layout_select_options()` (the one place
  `get_newsletter_signup_options()` runs) and resolved by ID at render time.

## Data model

Store the layout as an ordered CMB2 `group` (sortable), one row per section.
v1 row has a single field: a flat `select` of registry slugs (labels prefixed
"Show block:" / "Banner:" for scannability — CMB2's select has no native
optgroup support, so a flat list is what shipped).

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
// $block_context carries shared args (e.g. excluded_posts_ids) for product blocks.
foreach ( nm_get_front_page_layout() as $block_slug ) {
  nm_render_front_page_block( $block_slug, $block_context );
}
```

`nm_get_front_page_layout()` returns the saved slug order (or the default seed),
and `nm_render_front_page_block()` does the routing:

```php
function nm_render_front_page_block( $slug, $context = array() ) {
  if ( ! is_string( $slug ) || $slug === '' ) {
    return;
  }

  $registry = nm_get_front_page_block_registry();

  // Statically-defined sections: resolve in the DB-free registry, dispatch on
  // type. Partial comes from the trusted registry, never from the slug.
  if ( isset( $registry[ $slug ] ) ) {
    $block = $registry[ $slug ];
    if ( $block['type'] === 'product' ) {
      get_template_part( $block['partial'], null, $context );
    } else {
      get_template_part( $block['partial'] );
    }
    return;
  }

  // The one dynamic family: a reference to a newsletter post, keyed by ID.
  if ( str_starts_with( $slug, 'newsletter-signup-' ) ) {
    nm_render_newsletter_signup( $slug );
  }
}
```

**`render_front_page_banner` was removed** in the 4.7.0 final review. With banners
rendered from the trusted registry, its partial-path handling and `partials/`-only
path-traversal guard are no longer needed (no path is ever stored). Its one piece
of real logic — the `newsletter-signup-{id}` case (loads `partials/email-signup`
with the newsletter ID + mailchimp-key check) — was extracted to
`nm_render_newsletter_signup()` in `lib/renderers.php`. Retired option slugs
(`email-the-cortado`, `email-the-pick`) are no longer special-cased: they simply
fail the registry lookup and the newsletter-prefix check, and are ignored.

## Migration

The current layout must seed the new group so nothing changes visually on launch.

1. **Refactor** `$banner_options` out of `nm_register_front_page_options_metabox`
   into a shared `nm_get_front_page_banner_options()` (used by both the legacy
   banner selects and the registry).
2. **One-shot default seed** for `nm_front_page_layout`: if the layout option is
   empty, default it to the current hardcoded order, reading the existing
   `nm_front_page_banner_option_1..4` values for the banner rows:

   ```
   banner_option_1 value  (if not 'None')
   highlight-block
   novara-live
   banner_option_2 value
   audio
   banner_option_3 value
   downstream
   banner_option_4 value
   ```

   Implement as a default-on-read (compute the seed when option is empty) rather
   than a destructive write, so it is reversible and safe to deploy. Legacy banner
   values (partial paths or `newsletter-signup-<id>`) are mapped to current layout
   slugs by `nm_legacy_banner_value_to_layout_slug()`; retired/unknown values drop out.
3. **Keep** the old 4 banner `select` fields for one release as a fallback / until
   the seed is confirmed working in production, then remove them in a follow-up.

## v1 / v2 split

**v1 (this plan):**
- Block registry.
- `nm_get_front_page_banner_options()` refactor.
- Layout subpage: one sortable group, single select per row.
- `front-page.php` loops the layout between above-the-fold and mega-block,
  passing a shared `$context` (incl. `excluded_posts_ids`) to product blocks.
- Default-seed migration preserving current order.
- highlight-block included as a registry block; config stays on its subpage,
  visibility still governed by its existing toggle.

**v2 (later, only if needed):**
- Per-instance / conditional config (Option 2) via `cmb2-conditionals` or custom
  JS — only if a real need for per-instance config or duplicate configured blocks
  appears.
- Add the standalone `audio-acfm` block to the registry once
  `feature/front-page-acfm-block` is rebuilt on top of this editor.
- Possibly split the `audio` block (currently hardcodes Novara FM + ACFM) now that
  ACFM is becoming its own selectable block.

## Resolved decisions

- ~~**Keep `render_front_page_banner`** and route banner rows through it~~
  **Reversed in 4.7.0 final review:** removed it; banners render from the trusted
  registry and the newsletter case moved to `nm_render_newsletter_signup()`. The
  path-traversal guard became unnecessary (no path is stored in a layout).
- **Dedicated "Layout" subpage** under Front Page options.
- **Disable a slot by removing its row** — no per-row "None".
- **Capped at ~12 rows** — soft limit (no native CMB2 max; hide Add button via JS
  or document as soft cap).
- **highlight-block is a registry block**, not pinned/hardcoded — config on its
  subpage, `excluded_posts_ids` passed via the shared render context.

## Risk / cost

- No new dependencies in v1 — pure CMB2 group + existing partial-include render.
- Reuses two proven in-repo patterns (sortable group, banner partial select).
- Main risk is the migration seed; mitigated by default-on-read + keeping legacy
  banner fields one release.
