# Search suggestions

**Date:** 2026-10-01
**Branch:** `feature/search-destinations`
**Status:** Design approved, not yet implemented
**Builds on:** uncommitted "Jump to" destinations work on this branch
(`lib/functions-search.php`, `partials/search-destinations.php`)

## Problem

The first pass at search destinations renders matching category archives,
newsletters and site sections as a row of red buttons under a "Jump to"
heading. Reviewed on DevKinsta, it has four problems:

1. **Buttons read as secondary.** Next to the post cards below them, a row of
   small buttons looks less important than the results, which is the opposite
   of the intent (drive readers to shows and projects).
2. **Redirect duplicates.** The Cortado newsletter permalink 301s to the
   Cortado category archive (`lib/functions-rewrites.php`), so a search for
   "the cortado" shows two destinations that land on the same page.
3. **Name-only matching.** Categories and newsletters match on their name
   only, so short names and aliases ("dyor") find nothing.
4. **No way to refine.** The search page shows a "Search Results for: X"
   heading but no form; the only search form is the header overlay.

## Decisions

### Naming and layout

- User-facing label is **"Suggested"**. Code keeps the term *destinations*
  (`nm_get_search_destination_matches()` etc.) — the set includes jobs,
  events and support, so it is not a "products" concept.
- Search page order, top to bottom:
  1. Search form pre-filled with the current query (replaces the
     "Search Results for: X" heading).
  2. "Suggested" heading + suggestion cards. Page 1 only, as now.
  3. "Results" heading + the existing post grid, unchanged.
- Limit stays at **6** suggestions (`NM_SEARCH_DESTINATIONS_LIMIT`).

### Search form on the results page

- Extend `searchform.php` to read `get_search_form( $args )` arguments: a
  pre-filled value and an id prefix. The header overlay keeps its existing
  ids (`search-form`, `search-input`, `search-submit`); the results-page form
  uses a distinct prefix so the page never has duplicate ids.
- One template, two call sites. The 404 page's inline form is out of scope.

### Suggestion cards

- New partial `partials/post-layouts/archive-destination.php`, taking a
  destination array via `$args`. Markup and classes mirror
  `partials/post-layouts/archive-post.php` so a suggestion reads as a peer of
  a post: same grid classes (`is-s-24 is-l-12 is-xxl-8 mb-4`), same rounded
  16:9 thumbnail frame, same `index-post-title` heading.
- Differences from a post card:
  - **Tag** in the thumbnail corner (where posts show category tags):
    "Show" for categories, "Newsletter" for newsletters, "Section" for fixed
    site sections.
  - **Title:** the category or newsletter name, or the section label.
  - **Description:** category description or newsletter excerpt, trimmed.
    Omitted when empty.
- Keep `data-testid="search-destinations"` on the section and
  `data-destination-type` on each card for Playwright.

### Card images — fallback chain

Each destination resolves an image in this order; the first hit wins:

1. **Own image.**
   - Category: Open Graph share image (`_nm_category_og_image_id` term meta).
     The microbrand logo (`_nm_category_logo`) is not used — it is a logo,
     not a 16:9 image. The splash image field belongs to the `focus`
     taxonomy, not categories.
   - Newsletter: featured image.
   - Fixed section: none.
2. **Newest post thumbnail** in the category (categories only; one
   `posts_per_page => 1` query with `fields => 'ids'`).
3. **Branded tile:** 16:9 rounded block in brand red with the name in large
   bold white type. Static CSS in `src/styl/` (not inline), built to `dist/`.

Existing own-images may turn out to be poor; the tile is built first so it
can be judged on DevKinsta before the chain is tuned.

### Redirect-aware de-duplication

- Every redirect lives in theme code (`lib/functions-rewrites.php`,
  `nm_serial_podcast_redirect()` in `lib/functions-custom.php`); no redirect
  plugin is installed.
- Move the newsletter → category map and the external redirect map out of
  their `$nm_*` globals/closures into getters
  (`nm_get_newsletter_category_redirects()`,
  `nm_get_external_redirects()`), matching the existing
  `nm_get_serial_podcast_slugs()` pattern. The `template_redirect` handlers
  and the search matcher both read the getters, so they cannot drift.
- New `nm_resolve_canonical_url( $url )` applies those maps. Search
  de-duplicates by **resolved** URL.
- When two matches resolve to the same URL, the surviving card is the one
  whose own URL already equals the resolved URL (the category, for the
  Cortado), so name and image describe where the reader lands.
- Server-level redirects configured in Kinsta are invisible to the theme and
  out of scope.

### Search keywords (aliases)

- Code-level map in `lib/functions-search.php`: category or newsletter slug
  → list of keywords, filterable via `nm_search_destination_keywords`.
  Seeded with `do-your-own-research` => `dyor`; further aliases are added by
  release.
- Categories and newsletters match on name **or** any keyword, using the same
  matching rules as fixed sections (query contains keyword, or keyword
  contains query; minimum query length 3).
- No admin field. Keyword changes are code-reviewed and versioned.

## Testing

Playwright, `tests/e2e/search.spec.js`. Structural assertions only — never
exact editorial content.

- Results-page search form is present and its input value equals the query.
- "Suggested" section precedes the post results in the DOM.
- Suggestion cards share the post card's grid classes and each contains
  either an image or a branded tile.
- `the cortado` yields exactly one suggestion pointing at the Cortado
  category archive (redirect de-dup).
- `dyor` yields a category suggestion (keyword match).
- `merch` still links to the shop; page 2 still shows no suggestions.

Manual on DevKinsta:

- Cortado newsletter permalink still 301s to the category after the
  redirect-map refactor; `/shop` still redirects externally.
- phpcs clean; `npm run build` and commit `dist/` with the tile CSS.

## Rollout

- No admin data, no post-deploy checklist step (keywords live in code).
- CHANGELOG: one line — search suggests shows, newsletters and site sections
  above results, with a pre-filled search form.

## Out of scope

- 404 page search form.
- Kinsta server-level redirects.
- Serial podcast single-post redirects (search never suggests single posts).
