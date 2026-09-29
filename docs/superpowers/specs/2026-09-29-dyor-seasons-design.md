# Do Your Own Research: seasons on the archive page

**Date:** 2026-09-29
**Status:** Implemented (this branch)
**Branch:** `fix/dyor-archive-box-margins` (PR #626), with `development` merged in so the season/episode meta from #629 is present.

## Problem

DYOR season 2 launches Saturday. It is themed, with its own FigJam map board. The archive page (`category-do-your-own-research.php`) has one map, one paged "Full Archive" grid and no idea of seasons. The map iframe loads eagerly: a heavy FigJam embed, and two of them would slow first load badly.

Season numbers now exist as post meta (`_nm_season`, `_nm_episode`, from #629). This page is their first real consumer.

## Decisions (agreed 2026-09-29)

1. **Blocks per season, newest season first.** Season 2 sits above season 1; season 3 will sit above season 2.
2. **Episodes newest first inside each season**, like the page today. There is no strict episode-to-episode narrative, only a thematic one.
3. **Each season has its own FigJam board**, so its own file key.
4. **Season config lives on a new Products options page**, not category term meta. A site-wide "Products" menu holds per-product settings; DYOR is its first sub-page. Per-season copy (title, description) lives there too.
5. **Click-to-load maps.** Nothing loads from Figma until the reader clicks. Built as a plain interaction now, shaped so the cookie consent gate (#523) can hook into it later. No cookie work in this PR.
6. **Per-episode map node IDs stay** (`_nm_dyor_figma_node_id`), applied per season. None are set on staging's copy of production today, but it's cheap to keep.
7. **No building for the future beyond the data shape.** A season block renders only when that season has published posts. A repeatable group is the simplest store for N seasons, not a season-3 feature.

## Components

### 1. Products options page

New file `lib/theme-options/options-products.php`, loaded after `options-fundraising` in `functions.php`.

- Top-level CMB2 options page **Products** (`option_key` `nm_products_options`, dashicon `dashicons-products`). One `title`-type field explains that per-product settings live in the sub-pages. CMB2 needs a box on the parent page.
- Sub-page **Do Your Own Research** (`option_key` `nm_products_dyor_options`, `parent_slug` `nm_products_options`).
- Repeatable group field `seasons`. Each entry has:
  - `number`: positive integer, sanitised like `_nm_season`
  - `title`: text, e.g. "Season 2" or a themed name
  - `description`: `textarea_small`
  - `figma_file_key`: text
  - `figma_default_node_id`: text

`nm_get_dyor_seasons(): array`

- Returns entries keyed by season number, sorted descending, with blank entries (no number) dropped.
- **Seed:** when the saved group is empty, it returns season 1 built from the legacy category term meta (`_nm_dyor_figma_file_key`, `_nm_dyor_figma_default_node_id`). The title is "Season 1" and the description is empty. This is computed on read, never written.

### 2. Deprecation of the category map fields

`lib/meta/meta-boxes-category-dyor.php` fields become `@deprecated 4.11.0`. The seed reads them, so they stay for this release.

- Field descriptions say "Deprecated: set per season under Products → Do Your Own Research".
- CHANGELOG `### Deprecated` entry.
- Post-deploy checklist step: open Products → Do Your Own Research and Save once, so the seeded season 1 persists. Then add season 2's file key.

### 3. Archive page layout

Top to bottom:

1. **Hero:** unchanged, including #626's margins fix.
2. **Latest Episode** (page 1 only today; see Queries). Adds a line above the title: `Season {n}, Episode {m}` from the post's meta. It shows `Season {n}` alone when there's no episode number, and nothing when there's no season.
3. **One block per season**, for seasons that have at least one published DYOR post, in descending season number. Each block has:
   - a heading: the season `title`, falling back to `Season {n}`. Plus the `description` if set.
   - the click-to-load map (component 4), if the season has a `figma_file_key`
   - the season's episodes, newest first, in the existing grid (`partials/post-layouts/archive-post`, `is-s-24 is-l-12 is-xxl-8`)
4. **Unseasoned posts:** DYOR posts with no `_nm_season`, in a final block headed "More from Do Your Own Research", only when any exist.
5. Support module, unchanged.

A season with posts but no options entry still renders (heading `Season {n}`, no map, no description). A season entry with no posts renders nothing.

The map is rendered inline in the season loop. There's no partial: it's used once, on this page.

### 4. Click-to-load map

Markup, per season:

- Container `.dyor-archive__map` with `data-click-to-load` and `data-click-to-load-src="<figma embed url>"`, plus `data-click-to-load-title`.
- Collapsed state: a short widescreen banner (fixed height, about 160px desktop, 120px mobile) with a centred `ui-button`, "Load the Season {n} map". No iframe in the DOM.
- The embed URL is built as today: `embed.figma.com/board/{file_key}/…` with `embed-host=share`, `footer=false`, `page-selector=false`, and `node-id` when set.

JS: new module `src/js/modules/ClickToLoad.js`, instantiated in `src/js/main.js` like the other modules. There is no webpack config change.

- On click of the button inside `[data-click-to-load]`, it creates the iframe with the stored `src`, `title` and `allowfullscreen`, and inserts it.
- It adds `is-loaded`, which transitions the container's height from the banner height to the full map height (the current `min-height: 450px`, `max-height: 85vh`). The transition goes on `height` / `max-height` in CSS.
- Consent hook: before loading, it dispatches a cancelable `nm:click-to-load` CustomEvent on the container. If a listener calls `preventDefault()`, loading stops. #523 can gate embeds through that event without touching this module. Until then nothing listens and the click just loads.

Styles go in `src/styl/pages/dyor-archive.styl`, replacing the eager `.dyor-archive__map-embed` rules. `dist/` is rebuilt and committed because JS and Stylus change.

The `TODO` comment on the map in the template is replaced with a pointer to the `nm:click-to-load` hook.

### 5. Queries

- One `get_posts` gets every published DYOR post (`posts_per_page => -1`, date DESC), grouped in PHP by `(int) _nm_season`. 0 means unseasoned.
- Latest Episode is the first post of that list. That removes the separate `WP_Query`.
- Each season's map node: the newest post **in that season** with a non-empty `_nm_dyor_figma_node_id`, else the season's `figma_default_node_id`, else none.
- The page is no longer paginated. A `template_redirect` hook 301s paged DYOR requests to page 1 (`get_pagenum_link( 1 )`). It matches on the `cat` / `category_name` query vars rather than `is_category()`, because a page number past the last page is already a 404 by then. The pagination partial is removed from the template.

## Local test data

Local only, via `docker exec devkinsta_fpm wp --allow-root …`: set the last three season 1 DYOR posts to `_nm_season = 2`, `_nm_episode = 1..3`. Also add a local season 2 entry on the Products page with a placeholder file key.

## Out of scope

- Cookie consent gating (#523 hooks in later).
- Season 3+ specifics.
- Per-season hero art.
- Removing the deprecated category fields (4.11.0).
- Other products on the Products page.

## Testing

- `php -l` on touched PHP. `npm run build` succeeds.
- Playwright `tests/e2e/dyor-archive.spec.js`. Structural only (see the structural e2e tests memory):
  - The page loads with at least one season block.
  - Season blocks are in descending season order (`data-season` attribute).
  - Each map banner has no iframe before the click.
  - Clicking the button creates an iframe whose `src` starts `https://embed.figma.com/board/`. Embeds are route-blocked by the fixture; asserting the attribute is enough.
  - `/category/do-your-own-research/page/2/` 301s to `/category/do-your-own-research/`. `/dyor/` is the vanity route to the same archive.
- Manual, local with fake season 2 data:
  - Season 2 above season 1.
  - The Latest Episode line reads "Season 2, Episode 3".
  - Both maps load on click, and the height animates.
  - Products page Save persists the seeded season 1.

## Changelog

- **Added:** Do Your Own Research archive splits episodes by season, each with its own map that loads on click.
- **Deprecated:** DYOR category map fields, replaced by Products → Do Your Own Research. Needs one Save after deploy.
