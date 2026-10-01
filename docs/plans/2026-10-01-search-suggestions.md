# Search Suggestions Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the "Jump to" button row on the search page with post-style "Suggested" cards, a pre-filled search form, redirect-aware de-duplication and code-level search keywords.

**Architecture:** Matching and image resolution stay in `lib/functions-search.php` and return plain arrays. Redirect maps move to getters in `lib/functions-rewrites.php` so the redirect handlers and the search de-dup read one source. Rendering is two partials (section + card) and one results-page form partial; tile styles live in a new `src/styl/layouts/search.styl`.

**Tech Stack:** WordPress theme PHP, CMB2 term meta, Stylus via nm-stylus-library, Webpack build, Playwright e2e.

**Spec:** `docs/specs/2026-10-01-search-suggestions-design.md`

## Global Constraints

- User-facing labels: "Suggested", "Results". Code term stays *destinations*.
- `NM_SEARCH_DESTINATIONS_LIMIT` stays 6; `NM_SEARCH_DESTINATIONS_MIN_LENGTH` stays 3.
- Suggestions on page 1 only.
- Card grid classes: `grid-item is-s-24 is-l-12 is-xxl-8 mb-4` (same as post cards in `index.php`).
- Image chain: product logo tile → own image (category `_nm_category_og_image_id`, newsletter featured image) → newest post thumbnail (categories only) → branded red tile.
- Logo tile background: mid grey (`--color-gray-mid`). Branded tile: brand red (`--color-red`), white bold name.
- Static CSS never inline; lives in `src/styl/`, built to `dist/`.
- Meta key `_nm_category_logo` unchanged; only label/description renamed to "Product Logo".
- Keywords live in code, filterable via `nm_search_destination_keywords`. Seed: `do-your-own-research` => `dyor`.
- Playwright assertions structural only; never exact editorial content.
- Do not modify build config (webpack/release).
- No push without asking (push runs Playwright CI against staging).

## Review Focus

- Category with a logo **and** an OG image → logo tile wins (chain order). Pinned in Task 3 manual check on Downstream/Novara Live (have logos).
- Newsletter redirected to a category that does not exist locally → newsletter card keeps its own URL, no fatal. Pinned in Task 1 (resolver returns input unchanged when category missing).
- Query containing HTML/quotes (`"><script>`) → form value escaped, no suggestions break. Pinned in Task 5 e2e.
- Destination with no description → card renders without an empty `<div>`. Pinned in Task 3 (conditional output).
- Fixed section (Shop, external URL) → card is a link to the external URL with a branded tile and "Section" tag. Pinned in Task 5 e2e (merch).

---

### Task 1: Redirect map getters + canonical URL resolver

**Files:**
- Modify: `lib/functions-rewrites.php` (external redirects block, newsletter redirects block)

**Interfaces:**
- Produces: `nm_get_external_redirects(): array` (path => URL), `nm_get_newsletter_category_redirects(): array` (newsletter slug => category slug), `nm_resolve_canonical_url( string $url ): string`.

- [ ] **Step 1: Baseline the redirects**

Run:
```bash
curl -sk -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramediacom.local/shop/
curl -sk -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramediacom.local/newsletters/the-cortado/
```
Expected: `301 https://shop.novaramedia.com/` and `301 …/category/…/the-cortado/`.

- [ ] **Step 2: Replace the external redirect global with a getter**

```php
/**
 * Paths that 301 to an external URL. Format: 'path' => 'https://example.com'.
 *
 * @return string[]
 */
function nm_get_external_redirects() {
  return array(
    'shop' => 'https://shop.novaramedia.com',
  );
}

add_action(
  'template_redirect',
  function () {
    handle_external_redirects( nm_get_external_redirects() );
  },
  1
);

add_filter(
  'allowed_redirect_hosts',
  function ( $hosts ) {
    $external_hosts = array_unique(
      array_map(
        function ( $url ) {
          return wp_parse_url( $url, PHP_URL_HOST );
        },
        array_values( nm_get_external_redirects() )
      )
    );
    return array_merge( $hosts, $external_hosts );
  }
);
```

- [ ] **Step 3: Replace the newsletter redirect global with a getter**

```php
/**
 * Newsletter CPT permalinks that 301 to a category archive. The newsletter
 * record stays as the source of signup metadata; the category archive is the
 * canonical destination for readers. Format: 'newsletter-slug' => 'category-slug'.
 *
 * @return string[]
 */
function nm_get_newsletter_category_redirects() {
  return array(
    'the-cortado' => 'the-cortado',
  );
}

add_action(
  'template_redirect',
  function () {
    handle_newsletter_category_redirects( nm_get_newsletter_category_redirects() );
  }
);
```

- [ ] **Step 4: Add the resolver after `handle_newsletter_category_redirects()`**

```php
/**
 * Resolve a URL to where the theme's redirects actually send it.
 *
 * Applies the external and newsletter → category maps above, so callers that
 * list links (search suggestions) can de-duplicate by final destination.
 * Returns the input unchanged when no redirect applies or its target is
 * missing.
 *
 * @param string $url Absolute URL.
 * @return string
 */
function nm_resolve_canonical_url( $url ) {
  $path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );

  $external = nm_get_external_redirects();
  if ( isset( $external[ $path ] ) ) {
    return $external[ $path ];
  }

  foreach ( nm_get_newsletter_category_redirects() as $newsletter_slug => $category_slug ) {
    $newsletter = get_page_by_path( $newsletter_slug, OBJECT, 'newsletter' );

    if ( ! $newsletter || get_permalink( $newsletter ) !== $url ) {
      continue;
    }

    $category = get_category_by_slug( $category_slug );
    $link     = $category ? get_term_link( $category ) : '';

    return ( $link && ! is_wp_error( $link ) ) ? $link : $url;
  }

  return $url;
}
```

- [ ] **Step 5: Verify redirects unchanged + resolver**

Re-run Step 1 curls; expected identical output. Then:
```bash
wp eval 'echo nm_resolve_canonical_url( get_permalink( get_page_by_path( "the-cortado", OBJECT, "newsletter" ) ) ), "\n", nm_resolve_canonical_url( home_url( "/shop/" ) ), "\n", nm_resolve_canonical_url( home_url( "/jobs/" ) ), "\n";'
```
(run from the site root via DevKinsta's WP-CLI, or via a temporary `?nm_debug` is NOT allowed — use `wp eval` only.) Expected: category URL, `https://shop.novaramedia.com`, jobs URL unchanged.

- [ ] **Step 6: phpcs + commit**

```bash
phpcs lib/functions-rewrites.php
git add lib/functions-rewrites.php
git commit -m "refactor: redirect maps as getters, add canonical URL resolver"
```

---

### Task 2: Matcher — keywords, card data, canonical de-dup

**Files:**
- Create/commit: `lib/functions-search.php` (currently untracked), `functions.php` include, `lib/functions-filters.php` removal of `nm_search_include_cpts` (moved)

**Interfaces:**
- Consumes: `nm_resolve_canonical_url()` (Task 1).
- Produces: `nm_get_search_destination_keywords(): array` (slug => string[]); `nm_get_search_destination_matches( string $query ): array[]`, each match:
  `array( 'label' => string, 'url' => string, 'type' => 'category'|'newsletter'|'destination', 'object_id' => int, 'description' => string )`.

- [ ] **Step 1: Add the keyword map** (after `nm_get_search_destinations()`)

```php
/**
 * Extra search keywords for categories and newsletters, keyed by slug.
 *
 * Short names and aliases a reader might type that are not in the name.
 * Filterable via `nm_search_destination_keywords`.
 *
 * @since 4.11.0
 *
 * @return array<string, string[]>
 */
function nm_get_search_destination_keywords() {
  $keywords = array(
    'do-your-own-research' => array( 'dyor' ),
  );

  return apply_filters( 'nm_search_destination_keywords', $keywords );
}

/**
 * Whether a query matches any keyword: query contains keyword, or keyword
 * contains query.
 *
 * @param string   $query    Lowercased query.
 * @param string[] $keywords Keywords.
 * @return bool
 */
function nm_search_query_matches_keywords( $query, $keywords ) {
  foreach ( $keywords as $keyword ) {
    $keyword = mb_strtolower( $keyword );

    if ( str_contains( $query, $keyword ) || str_contains( $keyword, $query ) ) {
      return true;
    }
  }

  return false;
}
```

- [ ] **Step 2: Rewrite `nm_get_search_destination_matches()`**

```php
function nm_get_search_destination_matches( $query ) {
  $query = mb_strtolower( trim( $query ) );

  if ( mb_strlen( $query ) < NM_SEARCH_DESTINATIONS_MIN_LENGTH ) {
    return array();
  }

  $matches  = array();
  $keywords = nm_get_search_destination_keywords();

  $terms = get_terms(
    array(
      'taxonomy'   => 'category',
      'hide_empty' => true,
      'exclude'    => array( (int) get_option( 'default_category' ) ),
    )
  );

  if ( ! is_wp_error( $terms ) ) {
    foreach ( $terms as $term ) {
      $term_keywords = array_merge( array( $term->name ), $keywords[ $term->slug ] ?? array() );

      if ( ! nm_search_query_matches_keywords( $query, $term_keywords ) ) {
        continue;
      }

      $matches[] = array(
        'label'       => $term->name,
        'url'         => get_term_link( $term ),
        'type'        => 'category',
        'object_id'   => $term->term_id,
        'description' => wp_strip_all_tags( $term->description ),
      );
    }
  }

  $newsletters = get_posts(
    array(
      'post_type'      => 'newsletter',
      'posts_per_page' => -1,
      'no_found_rows'  => true,
    )
  );

  foreach ( $newsletters as $newsletter ) {
    $label = get_the_title( $newsletter );

    $newsletter_keywords = array_merge( array( $label ), $keywords[ $newsletter->post_name ] ?? array() );

    if ( ! nm_search_query_matches_keywords( $query, $newsletter_keywords ) ) {
      continue;
    }

    // Newsletters often share a name with their category, so say which is which.
    if ( ! str_contains( mb_strtolower( $label ), 'newsletter' ) ) {
      $label .= ' newsletter';
    }

    $matches[] = array(
      'label'       => $label,
      'url'         => get_permalink( $newsletter ),
      'type'        => 'newsletter',
      'object_id'   => $newsletter->ID,
      'description' => wp_strip_all_tags( get_the_excerpt( $newsletter ) ),
    );
  }

  foreach ( nm_get_search_destinations() as $destination ) {
    if ( empty( $destination['url'] ) ) {
      continue;
    }

    if ( ! nm_search_query_matches_keywords( $query, array_merge( array( $destination['label'] ), $destination['keywords'] ) ) ) {
      continue;
    }

    $matches[] = array(
      'label'       => $destination['label'],
      'url'         => $destination['url'],
      'type'        => 'destination',
      'object_id'   => 0,
      'description' => '',
    );
  }

  // Stable sort: closest label match first, source order otherwise.
  $ranked = array();
  foreach ( $matches as $index => $match ) {
    $ranked[] = array( nm_search_destination_rank( $match['label'], $query ), $index, $match );
  }
  sort( $ranked );

  // De-duplicate by where the link really lands. When two matches resolve to
  // the same place, keep the one that already lives there (the category, not
  // a newsletter that redirects to it), so its name and image describe it.
  $results = array();
  foreach ( $ranked as $entry ) {
    $match = $entry[2];

    if ( is_wp_error( $match['url'] ) ) {
      continue;
    }

    $canonical = nm_resolve_canonical_url( $match['url'] );

    if ( isset( $results[ $canonical ] ) && $results[ $canonical ]['url'] === $canonical ) {
      continue;
    }

    if ( isset( $results[ $canonical ] ) && $match['url'] !== $canonical ) {
      continue;
    }

    $results[ $canonical ] = $match;
  }

  return array_slice( array_values( $results ), 0, NM_SEARCH_DESTINATIONS_LIMIT );
}
```

Note: category matching moves from `get_terms( 'search' )` to PHP-side keyword matching so aliases work; the category list is small (low hundreds) and hide_empty. `sort()` on arrays containing match arrays compares rank then index first, which are unique, so match arrays are never compared.

Update the file docblock to say "suggestions" render on the search page via `partials/search-destinations.php`, and `NM_SEARCH_DESTINATIONS_LIMIT` docblock to "shown as Suggested".

- [ ] **Step 3: Verify matcher**

```bash
wp eval 'foreach ( array( "the cortado", "dyor", "merch", "downstream" ) as $q ) { echo $q, ": ", implode( " | ", wp_list_pluck( nm_get_search_destination_matches( $q ), "url" ) ), "\n"; }'
```
Expected: `the cortado` → one Cortado URL (the category); `dyor` → do-your-own-research category; `merch` → shop; `downstream` → categories + newsletter.

- [ ] **Step 4: phpcs + commit**

```bash
phpcs lib/functions-search.php
git add lib/functions-search.php functions.php lib/functions-filters.php
git commit -m "feat: search destination keywords and redirect-aware de-dup"
```

---

### Task 3: Card partial, image chain, tiles

**Files:**
- Create: `partials/post-layouts/archive-destination.php`
- Create: `src/styl/layouts/search.styl`
- Modify: `src/styl/site.styl` (import), `lib/functions-search.php` (image + tag helpers)

**Interfaces:**
- Consumes: match array from Task 2.
- Produces: `nm_get_search_destination_image( array $match ): array` returning `array( 'kind' => 'logo'|'image'|'tile', 'attachment_id' => int )`; `nm_get_search_destination_tag( array $match ): string`. Partial takes `$args['destination']` (match array) and `$args['grid-item-classes']`.

- [ ] **Step 1: Image + tag helpers in `lib/functions-search.php`**

```php
/**
 * Pick the image for a search suggestion card.
 *
 * Product logo tile, then the item's own image, then the newest post's
 * thumbnail (categories only), then a branded tile.
 *
 * @since 4.11.0
 *
 * @param array $match Destination match.
 * @return array 'kind' (logo|image|tile) and 'attachment_id'.
 */
function nm_get_search_destination_image( $match ) {
  $id = (int) $match['object_id'];

  if ( 'category' === $match['type'] ) {
    $logo_id = (int) get_term_meta( $id, '_nm_category_logo_id', true );
    if ( $logo_id ) {
      return array( 'kind' => 'logo', 'attachment_id' => $logo_id );
    }

    $og_id = (int) get_term_meta( $id, '_nm_category_og_image_id', true );
    if ( $og_id ) {
      return array( 'kind' => 'image', 'attachment_id' => $og_id );
    }

    $latest = get_posts(
      array(
        'cat'            => $id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
      )
    );

    if ( $latest ) {
      $alt_id   = (int) get_post_meta( $latest[0], '_cmb_alt_thumb_id', true );
      $thumb_id = $alt_id ? $alt_id : (int) get_post_thumbnail_id( $latest[0] );

      if ( $thumb_id ) {
        return array( 'kind' => 'image', 'attachment_id' => $thumb_id );
      }
    }
  }

  if ( 'newsletter' === $match['type'] ) {
    $thumb_id = (int) get_post_thumbnail_id( $id );
    if ( $thumb_id ) {
      return array( 'kind' => 'image', 'attachment_id' => $thumb_id );
    }
  }

  return array( 'kind' => 'tile', 'attachment_id' => 0 );
}

/**
 * Corner tag for a search suggestion card.
 *
 * @param array $match Destination match.
 * @return string
 */
function nm_get_search_destination_tag( $match ) {
  $tags = array(
    'category'    => 'Show',
    'newsletter'  => 'Newsletter',
    'destination' => 'Section',
  );

  return $tags[ $match['type'] ] ?? 'Section';
}
```

- [ ] **Step 2: Card partial `partials/post-layouts/archive-destination.php`**

```php
<?php
/**
 * Search suggestion card: a category, newsletter or site section styled as a
 * peer of the archive post card (partials/post-layouts/archive-post.php).
 *
 * @since 4.11.0
 *
 * $args['destination']       Match from nm_get_search_destination_matches().
 * $args['grid-item-classes'] Grid classes for the card.
 */

if ( empty( $args['destination'] ) || empty( $args['grid-item-classes'] ) ) {
  return;
}

$destination = $args['destination'];
$image       = nm_get_search_destination_image( $destination );
?>
<article class="<?php echo esc_attr( $args['grid-item-classes'] ); ?>" data-testid="search-destination" data-destination-type="<?php echo esc_attr( $destination['type'] ); ?>">
  <a href="<?php echo esc_url( $destination['url'] ); ?>" class="ui-hover u-display-block">
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <span class="ui-tag-block ui-tag-block--no-border"><span class="ui-tag"><?php echo esc_html( nm_get_search_destination_tag( $destination ) ); ?></span></span>
      </div>
      <?php if ( 'logo' === $image['kind'] ) { ?>
        <div class="search-destination-tile search-destination-tile--logo ui-rounded-box" data-testid="search-destination-image">
          <?php echo wp_get_attachment_image( $image['attachment_id'], 'col12', false, array( 'class' => 'search-destination-tile__logo' ) ); ?>
        </div>
      <?php } elseif ( 'image' === $image['kind'] ) { ?>
        <?php echo wp_get_attachment_image( $image['attachment_id'], 'col12-16to9', false, array( 'class' => 'ui-rounded-box u-display-block', 'data-testid' => 'search-destination-image' ) ); ?>
      <?php } else { ?>
        <div class="search-destination-tile search-destination-tile--brand ui-rounded-box" data-testid="search-destination-image">
          <span class="search-destination-tile__name font-weight-bold"><?php echo esc_html( $destination['label'] ); ?></span>
        </div>
      <?php } ?>
    </div>
    <h5 class="index-post-title font-size-9 font-weight-bold mt-2 text-wrap-pretty"><?php echo esc_html( $destination['label'] ); ?></h5>
    <?php if ( ! empty( $destination['description'] ) ) { ?>
      <div class="font-size-9 mt-1"><?php echo esc_html( wp_trim_words( $destination['description'], 24 ) ); ?></div>
    <?php } ?>
  </a>
</article>
```

- [ ] **Step 3: Tile styles `src/styl/layouts/search.styl`**

```stylus
// Search suggestion cards (partials/post-layouts/archive-destination.php).
// Tiles stand in for a 16:9 thumbnail when a destination has no photo.

.search-destination-tile
  display: flex
  align-items: center
  justify-content: center
  aspect-ratio: 16 / 9
  width: 100%
  overflow: hidden

.search-destination-tile--logo
  // Placeholder colour until the design phase picks fixed grey vs per-product.
  background-color: var(--color-gray-mid)
  padding: 12%

.search-destination-tile__logo
  display: block
  max-width: 100%
  max-height: 100%
  width: auto
  height: auto

.search-destination-tile--brand
  background-color: var(--color-red)
  color: var(--color-white-soft)
  padding: 1.5rem
  text-align: center

.search-destination-tile__name
  font-size: 2rem
  line-height: 1.1
```

Add `@import "layouts/search"` to `src/styl/site.styl` LAYOUTS block (alphabetical, after `products-bar`). Confirm the three custom properties exist: `grep -o -- '--color-gray-mid\|--color-red:\|--color-white-soft' dist/main.css | sort -u` after build; if a name differs, use the name the library emits.

- [ ] **Step 4: Build**

```bash
npm run build
```
Expected: success; `dist/` CSS contains `.search-destination-tile`.

- [ ] **Step 5: Commit**

```bash
phpcs lib/functions-search.php partials/post-layouts/archive-destination.php
git add lib/functions-search.php partials/post-layouts/archive-destination.php src/styl/layouts/search.styl src/styl/site.styl dist/
git commit -m "feat: search suggestion card with logo and brand tiles"
```

---

### Task 4: Search page layout + results form

**Files:**
- Create: `partials/search-results-form.php`
- Modify: `partials/search-destinations.php`, `index.php`, `docs/specs/2026-10-01-search-suggestions-design.md` (form section)

**Interfaces:**
- Consumes: `nm_get_search_destination_matches()` (Task 2), card partial (Task 3).
- Produces: DOM hooks `data-testid="search-results-form"`, `search-destinations`, `search-results-heading`.

- [ ] **Step 1: Results form partial**

Separate from `searchform.php` because `Header.js` binds every `.site-header-search__input` and the header ids (`search-input`) are used by `Search.js`.

```php
<?php
/**
 * Search form at the top of the search results page, pre-filled with the
 * current query. Separate from searchform.php (header overlay), whose classes
 * and ids are bound by Header.js and Search.js.
 *
 * @since 4.11.0
 */
?>
<form role="search" method="get" class="grid-row mb-4" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-testid="search-results-form">
  <div class="grid-item is-xxl-24 u-visuallyhidden">
    <label for="search-results-input">Search this site</label>
  </div>
  <div class="grid-item is-s-20 is-xxl-22">
    <input id="search-results-input" class="ui-input" type="text" placeholder="Search" value="<?php echo esc_attr( get_search_query( false ) ); ?>" name="s" required aria-required="true">
  </div>
  <div class="grid-item is-s-4 is-xxl-2">
    <button type="submit" class="ui-button ui-button--fill-width" aria-label="Submit Search"><i class="icon-search"></i></button>
  </div>
</form>
```

- [ ] **Step 2: Rewrite `partials/search-destinations.php`**

```php
<?php
/**
 * Search suggestions: category archives, newsletters and site sections
 * matching the current search, shown as cards above the post results.
 *
 * @since 4.11.0
 */

$destinations = nm_get_search_destination_matches( get_search_query( false ) );

if ( empty( $destinations ) ) {
  return;
}
?>
<div data-testid="search-destinations">
  <div class="grid-row mb-3">
    <div class="grid-item is-xxl-24">
      <h4 class="font-size-10 font-weight-bold">Suggested</h4>
    </div>
  </div>
  <div class="grid-row mb-4">
    <?php
    foreach ( $destinations as $destination ) {
      get_template_part(
        'partials/post-layouts/archive-destination',
        null,
        array(
          'destination'       => $destination,
          'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
        )
      );
    }
    ?>
  </div>
</div>
```

- [ ] **Step 3: `index.php` search header**

Replace the `is_search()` branch of the heading block and the destinations include so the search page renders: form → suggestions (page 1) → "Results" heading. Tag pages keep "Tag: X".

```php
<?php
if ( is_search() ) {
  get_template_part( 'partials/search-results-form' );

  if ( ! is_paged() ) {
    get_template_part( 'partials/search-destinations' );
  }
  ?>
    <div class="grid-row mb-3">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-10 font-weight-bold" data-testid="search-results-heading">Results</h4>
      </div>
    </div>
  <?php
} elseif ( is_tag() ) {
  ?>
    <div class="grid-row mb-5">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-10 font-weight-bold">Tag: <?php single_tag_title(); ?></h4>
      </div>
    </div>
  <?php
}
?>
```

- [ ] **Step 4: Spec sync** — in the spec's "Search form on the results page" section, replace the `searchform.php`-args approach with the separate `partials/search-results-form.php` and the Header.js reason.

- [ ] **Step 5: Look at it** — load `https://novaramediacom.local/?s=downstream`, `?s=the+cortado`, `?s=merch`, `?s=dyor` in Chrome; screenshot each.

- [ ] **Step 6: Commit**

```bash
phpcs index.php partials/search-destinations.php partials/search-results-form.php
git add index.php partials/search-destinations.php partials/search-results-form.php docs/specs/2026-10-01-search-suggestions-design.md
git commit -m "feat: Suggested section and pre-filled form on search results"
```

---

### Task 5: Product Logo rename, e2e, changelog

**Files:**
- Modify: `lib/meta/meta-boxes-taxonomy.php:48-49`, `tests/e2e/search.spec.js`, `CHANGELOG.md`

- [ ] **Step 1: Rename label**

```php
      'name' => esc_html__( 'Product Logo', 'cmb2' ),
      'desc' => esc_html__( 'if this category is for a product with a logo set it here (optional)', 'cmb2' ),
```

- [ ] **Step 2: Rewrite `tests/e2e/search.spec.js`**

```js
/**
 * Search Results Tests
 *
 * Verifies the search page: a pre-filled search form, Suggested cards
 * (category archives, newsletters, site sections) above the post results,
 * and redirect-aware de-duplication. Assertions are structural: which kinds
 * of suggestion appear and where they point, never exact post content.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const CARDS = '[data-testid="search-destinations"] [data-testid="search-destination"]';

test.describe('Search Results', () => {
  test('should pre-fill the results form with the query', async ({ page }) => {
    await gotoFresh(page, '/?s=novara+live');

    await expect(
      page.getByTestId('search-results-form').locator('input[name="s"]')
    ).toHaveValue('novara live');
  });

  test('should escape markup in the query', async ({ page }) => {
    await gotoFresh(page, '/?s=%22%3E%3Cb%3Ex');

    await expect(
      page.getByTestId('search-results-form').locator('input[name="s"]')
    ).toHaveValue('">x');
    await expect(page.locator('main b', { hasText: 'x' })).toHaveCount(0);
  });

  test('should show suggestions before the results', async ({ page }) => {
    await gotoFresh(page, '/?s=novara+live');

    const order = await page.evaluate(() => {
      const suggested = document.querySelector('[data-testid="search-destinations"]');
      const heading = document.querySelector('[data-testid="search-results-heading"]');
      return suggested && heading
        ? suggested.compareDocumentPosition(heading) & Node.DOCUMENT_POSITION_FOLLOWING
        : 0;
    });

    expect(order).toBeTruthy();
    expect(await page.getByTestId('post-list').locator('article').count()).toBeGreaterThan(0);
  });

  test('should render suggestions as cards with an image or tile', async ({ page }) => {
    await gotoFresh(page, '/?s=novara+live');

    const cards = page.locator(CARDS);
    await expect(cards.first()).toBeVisible();
    await expect(cards.first()).toHaveClass(/is-xxl-8/);

    const count = await cards.count();
    for (let i = 0; i < count; i++) {
      await expect(cards.nth(i).getByTestId('search-destination-image')).toHaveCount(1);
    }
  });

  test('should link to a category archive when searching its name', async ({ page }) => {
    await gotoFresh(page, '/?s=novara+live');

    await expect(
      page.locator(`${CARDS}[data-destination-type="category"] a[href*="/novara-live/"]`)
    ).toHaveCount(1);
  });

  test('should collapse a redirected newsletter into its category', async ({ page }) => {
    await gotoFresh(page, '/?s=the+cortado');

    await expect(page.locator(`${CARDS} a[href*="the-cortado"]`)).toHaveCount(1);
    await expect(
      page.locator(`${CARDS}[data-destination-type="category"] a[href*="/the-cortado/"]`)
    ).toHaveCount(1);
  });

  test('should match a category by its short name', async ({ page }) => {
    await gotoFresh(page, '/?s=dyor');

    await expect(
      page.locator(`${CARDS}[data-destination-type="category"] a[href*="do-your-own-research"]`)
    ).toHaveCount(1);
  });

  test('should link to the shop when searching for merch', async ({ page }) => {
    await gotoFresh(page, '/?s=merch');

    await expect(
      page.locator(`${CARDS}[data-destination-type="destination"] a[href^="https://shop.novaramedia.com"]`)
    ).toHaveCount(1);
  });

  test('should not show suggestions on later results pages', async ({ page }) => {
    await gotoFresh(page, '/page/2/?s=novara+live');

    await expect(page.getByTestId('search-destinations')).toHaveCount(0);
  });
});
```

- [ ] **Step 3: Run e2e locally** (system Chrome config in scratchpad, see `docs/testing/testing.md` "Running locally")

```bash
NODE_TLS_REJECT_UNAUTHORIZED=0 PLAYWRIGHT_BASE_URL=https://novaramediacom.local npx playwright test search --config <scratchpad>/pw.local.config.js --reporter=line
```
Expected: 9 passed. Then run the full suite the same way, without the `search` filter; expected no new failures vs `development`.

- [ ] **Step 4: Changelog** — replace the existing Added line with:

```
- Search suggests matching shows, newsletters and site sections above results, with a pre-filled search form
```

- [ ] **Step 5: Commit**

```bash
phpcs lib/meta/meta-boxes-taxonomy.php
git add lib/meta/meta-boxes-taxonomy.php tests/e2e/search.spec.js CHANGELOG.md
git commit -m "feat: search suggestions e2e, Product Logo label, changelog"
```

- [ ] **Step 6: Stop.** Ask before pushing (push runs Playwright CI → staging).
