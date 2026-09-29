# DYOR Seasons Archive Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** The Do Your Own Research archive shows one block per season (newest first), each with its own click-to-load FigJam map and its episodes. Season config lives on a new Products options page.

**Architecture:**
- **Config:** a CMB2 options page (`lib/theme-options/options-products.php`) stores a repeatable Seasons group. `nm_get_dyor_seasons()` reads it, and seeds season 1 from the legacy category meta when it's empty.
- **Template:** `category-do-your-own-research.php` makes one query for every DYOR post and groups the posts by `_nm_season`.
- **Maps:** a generic `ClickToLoad` JS module inserts each map iframe on click. It fires a cancelable `nm:click-to-load` event first, which is the hook the cookie consent gate will use later.

**Tech Stack:** PHP 8 / WordPress, CMB2 options pages, vanilla JS module in the webpack entry, Stylus, Playwright.

**Spec:** `docs/superpowers/specs/2026-09-29-dyor-seasons-design.md`

## Global Constraints

- Branch `fix/dyor-archive-box-margins` (PR #626), with `development` already merged in. Never commit to `development`. The user merges.
- No webpack config changes. A new module in `src/js/modules/` imported in `src/js/main.js` is fine. `dist/` is rebuilt (`npm run build`) and committed, because JS and Stylus change.
- Season blocks are ordered by descending season number. Episodes inside a season are newest first.
- A season block renders only when the season has published posts.
- No Figma request until the reader clicks. There is no iframe in the DOM before that.
- No cookie-consent code. The only consent seam is the cancelable `nm:click-to-load` event.
- Map markup stays inline in the season loop. No partial.
- Legacy category fields `_nm_dyor_figma_file_key` / `_nm_dyor_figma_default_node_id` are marked `@deprecated 4.11.0` and stay in place as the seed source.
- WordPress coding standards: two-space indent, `esc_*` on output. Static CSS lives in `src/styl/pages/`, never inline.
- E2E specs are structural: no hardcoded episode lists or counts.
- Local data changes run via `docker exec devkinsta_fpm wp --allow-root …` from `/www/kinsta/public/novaramediacom`. Local DB only.

## Review Focus

1. **Products page never saved on production.** Expect season 1 to render with today's map, via the seed. Pinned by Task 1 Step 2.
2. **A season has posts but no options entry, or an entry with no file key.** Expect the block with a "Season N" heading and no map, not a PHP notice. Pinned by Task 2 Step 5.
3. **A DYOR post with no `_nm_season`.** Expect it in the "More from Do Your Own Research" block, not dropped. Pinned by Task 2 Step 5.
4. **Old `/page/2/` links**, from search engines or social. Expect a 301 to the unpaged archive, not a duplicate page. Pinned by Task 2's Playwright redirect test.
5. **JS fails or is slow.** Expect the banner and button to stay visible and harmless, with no broken empty box. Pinned by Task 3 Step 6, which checks the collapsed state without JS.

---

### Task 1: Products options page and season config

**Files:**
- Create: `lib/theme-options/options-products.php`
- Modify: `functions.php` (load after `options-fundraising`)
- Modify: `lib/meta/meta-boxes-category-dyor.php` (deprecation notes)

**Interfaces:**
- Consumes:
  - `nm_sanitize_positive_int_meta( $value ): string` (`lib/meta/meta-boxes-post-episode.php`)
  - `NM_get_option( $key, $key_group, $default )` (`lib/theme-options/options-front-page.php`)
- Produces:
  - `nm_get_dyor_seasons(): array`. Keyed by season number, sorted descending. Each value is `array( 'number' => int, 'title' => string, 'description' => string, 'figma_file_key' => string, 'figma_default_node_id' => string )`.
  - `nm_get_dyor_seasons_seed(): array`, same shape.
  - Option `nm_products_dyor_options` holds the `seasons` group.

- [ ] **Step 1: Write the failing test**

Create the scratch file `<workspace>/t1-seasons.php`. It is not committed.

```php
<?php
// Run: wp --allow-root eval-file <this> from /www/kinsta/public/novaramediacom
$fail = 0;
$check = function ( $label, $ok ) use ( &$fail ) { echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n"; $fail += $ok ? 0 : 1; };

$backup = get_option( 'nm_products_dyor_options', null );
delete_option( 'nm_products_dyor_options' );

$term     = get_category_by_slug( 'do-your-own-research' );
$legacy   = get_term_meta( $term->term_id, '_nm_dyor_figma_file_key', true );
$seasons  = function_exists( 'nm_get_dyor_seasons' ) ? nm_get_dyor_seasons() : null;
$check( 'function exists', is_array( $seasons ) );
$check( 'empty option seeds season 1 only', is_array( $seasons ) && array( 1 ) === array_keys( $seasons ) );
$check( 'seed carries legacy file key', is_array( $seasons ) && isset( $seasons[1] ) && $legacy === $seasons[1]['figma_file_key'] );
$check( 'seed title', is_array( $seasons ) && isset( $seasons[1] ) && 'Season 1' === $seasons[1]['title'] );

update_option( 'nm_products_dyor_options', array( 'seasons' => array(
  array( 'number' => '1', 'title' => 'Season 1', 'figma_file_key' => 'AAA' ),
  array( 'number' => '', 'title' => 'blank row' ),
  array( 'number' => '2', 'title' => 'Two', 'description' => 'Desc', 'figma_file_key' => 'BBB', 'figma_default_node_id' => '1-2' ),
) ) );
$seasons = function_exists( 'nm_get_dyor_seasons' ) ? nm_get_dyor_seasons() : null;
$check( 'saved seasons sorted newest first, blank dropped', is_array( $seasons ) && array( 2, 1 ) === array_keys( $seasons ) );
$check( 'saved values read', is_array( $seasons ) && isset( $seasons[2] ) && 'BBB' === $seasons[2]['figma_file_key'] && '' === $seasons[1]['description'] );

null === $backup ? delete_option( 'nm_products_dyor_options' ) : update_option( 'nm_products_dyor_options', $backup );
echo $fail ? "$fail failed\n" : "all pass\n";
```

- [ ] **Step 2: Run it to see it fail (Review Focus 1)**

Run: `docker exec devkinsta_fpm sh -c 'cd /www/kinsta/public/novaramediacom && wp --allow-root eval-file wp-content/themes/novaramedia-com/<workspace>/t1-seasons.php'`
Expected: `FAIL function exists` and the checks that follow, ending `N failed`.

- [ ] **Step 3: Create the options page file**

```php
<?php
/**
 * Products options: per-product settings that don't belong on every
 * category. Top-level "Products" menu; each product is a sub-page.
 */
function nm_register_products_options_metabox() {
  $products_options = new_cmb2_box(
    array(
      'id'           => 'nm_products_options',
      'title'        => 'Products',
      'object_types' => array( 'options-page' ),
      'option_key'   => 'nm_products_options',
      'icon_url'     => 'dashicons-products',
      'capability'   => 'edit_posts',
    )
  );

  $products_options->add_field(
    array(
      'name' => 'Product settings',
      'desc' => 'Settings for individual products live in the sub-pages of this menu.',
      'id'   => 'products_intro',
      'type' => 'title',
    )
  );

  $dyor_options = new_cmb2_box(
    array(
      'id'           => 'nm_products_dyor_options',
      'title'        => 'Do Your Own Research',
      'object_types' => array( 'options-page' ),
      'option_key'   => 'nm_products_dyor_options',
      'parent_slug'  => 'nm_products_options',
      'capability'   => 'edit_posts',
    )
  );

  $seasons_group = $dyor_options->add_field(
    array(
      'id'          => 'seasons',
      'type'        => 'group',
      'description' => 'One entry per season. The archive shows a block for each season that has published episodes, newest season first. Set each episode\'s season in its Season / Episode box.',
      'options'     => array(
        'group_title'   => 'Season entry {#}',
        'add_button'    => 'Add another season',
        'remove_button' => 'Remove season',
        'sortable'      => true,
        'closed'        => true,
      ),
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name'            => 'Season number',
      'id'              => 'number',
      'type'            => 'text_small',
      'sanitization_cb' => 'nm_sanitize_positive_int_meta',
      'attributes'      => array(
        'type' => 'number',
        'min'  => '1',
        'step' => '1',
      ),
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name' => 'Title',
      'desc' => 'Heading for this season\'s block. Defaults to "Season N".',
      'id'   => 'title',
      'type' => 'text',
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name' => 'Description',
      'id'   => 'description',
      'type' => 'textarea_small',
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name' => 'Figma file key',
      'desc' => 'The file key from this season\'s FigJam board URL (e.g. Twc9z7w8yaEzaO6m0PM1Kj). The map only shows when set.',
      'id'   => 'figma_file_key',
      'type' => 'text',
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name' => 'Default map node ID',
      'desc' => 'Node the map opens on when no episode in this season has its own node ID. Use an invisible bounding rectangle to control the zoom level.',
      'id'   => 'figma_default_node_id',
      'type' => 'text',
    )
  );
}
add_action( 'cmb2_admin_init', 'nm_register_products_options_metabox' );

/**
 * DYOR seasons from Products → Do Your Own Research, keyed by season number,
 * newest first. Entries without a season number are dropped. Falls back to
 * nm_get_dyor_seasons_seed() until the page has been saved once.
 *
 * @return array[] { number, title, description, figma_file_key, figma_default_node_id }
 */
function nm_get_dyor_seasons() {
  $saved   = NM_get_option( 'seasons', 'nm_products_dyor_options', array() );
  $seasons = array();

  if ( is_array( $saved ) ) {
    foreach ( $saved as $entry ) {
      $number = isset( $entry['number'] ) ? (int) $entry['number'] : 0;

      if ( $number < 1 ) {
        continue;
      }

      $seasons[ $number ] = array(
        'number'                => $number,
        'title'                 => isset( $entry['title'] ) ? trim( $entry['title'] ) : '',
        'description'           => isset( $entry['description'] ) ? trim( $entry['description'] ) : '',
        'figma_file_key'        => isset( $entry['figma_file_key'] ) ? trim( $entry['figma_file_key'] ) : '',
        'figma_default_node_id' => isset( $entry['figma_default_node_id'] ) ? trim( $entry['figma_default_node_id'] ) : '',
      );
    }
  }

  if ( empty( $seasons ) ) {
    $seasons = nm_get_dyor_seasons_seed();
  }

  krsort( $seasons );

  return $seasons;
}

/**
 * Season 1 built from the legacy DYOR category map fields, so the archive
 * keeps today's map until Products → Do Your Own Research is saved once.
 * Computed on read, never written.
 *
 * @deprecated 4.11.0 Remove with the category map fields once production has saved the Products page.
 * @return array[]
 */
function nm_get_dyor_seasons_seed() {
  $term = get_category_by_slug( 'do-your-own-research' );

  if ( ! $term ) {
    return array();
  }

  return array(
    1 => array(
      'number'                => 1,
      'title'                 => 'Season 1',
      'description'           => '',
      'figma_file_key'        => (string) get_term_meta( $term->term_id, '_nm_dyor_figma_file_key', true ),
      'figma_default_node_id' => (string) get_term_meta( $term->term_id, '_nm_dyor_figma_default_node_id', true ),
    ),
  );
}
```

- [ ] **Step 4: Load it and deprecate the category fields**

In `functions.php`, after `get_template_part( 'lib/theme-options/options-fundraising' );`:

```php
get_template_part( 'lib/theme-options/options-products' );
```

In `lib/meta/meta-boxes-category-dyor.php`:
- Add ` * @deprecated 4.11.0 Map settings moved to Products → Do Your Own Research (per season). Kept as the seed source for nm_get_dyor_seasons_seed().` to the docblock of `nm_cmb_dyor_metaboxes`.
- Prefix both field `desc` strings with `Deprecated: set per season under Products → Do Your Own Research. `

- [ ] **Step 5: Run the test to see it pass**

Run the Step 2 command. Also `php -l lib/theme-options/options-products.php && php -l lib/meta/meta-boxes-category-dyor.php && php -l functions.php`.
Expected: every line `ok`, then `all pass`, and no syntax errors.

- [ ] **Step 6: Commit**

```bash
git add lib/theme-options/options-products.php functions.php lib/meta/meta-boxes-category-dyor.php
git commit -m "feat: Products options page with DYOR season config"
```

---

### Task 2: Season blocks on the DYOR archive

**Files:**
- Modify: `category-do-your-own-research.php` (whole file restructure, below the hero)
- Modify: `lib/functions-hooks.php` (paged redirect)
- Create: `tests/e2e/dyor-archive.spec.js`

**Interfaces:**
- Consumes: `nm_get_dyor_seasons()` from Task 1; meta `_nm_season`, `_nm_episode`, `_nm_dyor_figma_node_id`.
- Produces markup that Task 3 relies on:
  - `[data-testid="dyor-season"][data-season="N"]` per season block.
  - `.dyor-archive__map[data-click-to-load][data-click-to-load-src][data-click-to-load-title][data-testid="dyor-season-map"]` with a child `button[data-click-to-load-button]`.

- [ ] **Step 1: Local fake season 2 data**

Run in `docker exec devkinsta_fpm sh -c 'cd /www/kinsta/public/novaramediacom && …'`:

```bash
wp --allow-root post list --category_name=do-your-own-research --post_status=publish --orderby=date --order=DESC --posts_per_page=3 --field=ID
```

For the three IDs returned (newest first), set `_nm_season` 2 and `_nm_episode` 3, 2, 1 with `wp --allow-root post meta update <ID> _nm_season 2` and `… _nm_episode <n>`. Then save a local options entry so season 2 has a map (the same board as season 1 is fine for local):

```bash
wp --allow-root option update nm_products_dyor_options '{"seasons":[{"number":"2","title":"Season 2","description":"Local test copy for season 2.","figma_file_key":"<season 1 key>"},{"number":"1","title":"Season 1","figma_file_key":"<season 1 key>"}]}' --format=json
```

`<season 1 key>` comes from `wp --allow-root term meta get <dyor term id> _nm_dyor_figma_file_key`.

- [ ] **Step 2: Write the failing Playwright spec**

```js
/**
 * Do Your Own Research archive
 *
 * Structural checks only: one block per season with published episodes,
 * newest season first; maps wait for a click; the archive is unpaged.
 * Figma is in the fixture's blocked embed hosts, so a loaded map is
 * asserted by its iframe src, not by what Figma renders.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const ARCHIVE = '/category/do-your-own-research/';

test.describe('Do Your Own Research archive', () => {
  test('shows season blocks, newest season first', async ({ page }) => {
    await gotoFresh(page, ARCHIVE);

    const seasons = await page.getByTestId('dyor-season').evaluateAll((blocks) =>
      blocks.map((block) => Number(block.dataset.season))
    );

    expect(seasons.length).toBeGreaterThan(0);
    expect(seasons).toEqual([...seasons].sort((a, b) => b - a));
  });

  test('season maps load only on click', async ({ page }) => {
    await gotoFresh(page, ARCHIVE);

    const map = page.getByTestId('dyor-season-map').first();

    await expect(map).toBeVisible();
    await expect(page.locator('[data-testid="dyor-season-map"] iframe')).toHaveCount(0);

    await map.locator('[data-click-to-load-button]').click();

    await expect(map.locator('iframe')).toHaveAttribute('src', /^https:\/\/embed\.figma\.com\/board\//);
  });

  test('paged archive URLs redirect to the archive', async ({ page }) => {
    await gotoFresh(page, `${ARCHIVE}page/2/`, { failOnStatusCode: false });

    expect(new URL(page.url()).pathname).toBe(ARCHIVE);
  });
});
```

- [ ] **Step 3: Run it to see it fail**

Run: `PLAYWRIGHT_BASE_URL=https://novaramediacom.local npx playwright test --config=<scratchpad>/playwright.local.config.js dyor-archive`
Expected: all 3 FAIL. There are no `dyor-season` blocks, no `dyor-season-map`, and `/page/2/` either stays paged or 404s.

- [ ] **Step 4: Add the paged redirect**

Append to `lib/functions-hooks.php`:

```php
/**
 * The DYOR archive lists every episode on one page, grouped by season, so
 * paged URLs 301 to page 1 rather than repeating it. get_pagenum_link( 1 )
 * keeps the requested path, so /dyor/page/2/ lands on /dyor/.
 */
function nm_dyor_unpaged_redirect() {
  if ( is_category( 'do-your-own-research' ) && is_paged() ) {
    wp_safe_redirect( get_pagenum_link( 1 ), 301 );
    exit;
  }
}
add_action( 'template_redirect', 'nm_dyor_unpaged_redirect' );
```

- [ ] **Step 5: Restructure the template (Review Focus 2, 3)**

In `category-do-your-own-research.php`, replace everything from `// Map embed settings` through the end of the `// Node ID priority…` block (the `$figma_file_key` / `$map_node_id` computation) with:

```php
// Every published episode once, newest first, grouped by season below.
$dyor_posts = get_posts( array(
  'cat'            => $category->term_id,
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'date',
  'order'          => 'DESC',
  'no_found_rows'  => true,
) );

$dyor_seasons = nm_get_dyor_seasons();

// Season number => posts. 0 collects posts with no season set.
$posts_by_season = array();
foreach ( $dyor_posts as $dyor_post ) {
  $posts_by_season[ (int) get_post_meta( $dyor_post->ID, '_nm_season', true ) ][] = $dyor_post;
}

$unseasoned_posts = isset( $posts_by_season[0] ) ? $posts_by_season[0] : array();
unset( $posts_by_season[0] );
krsort( $posts_by_season );

/**
 * Figma embed URL for a season's board, opened on the newest episode in the
 * season that has its own node ID, else the season default, else no node.
 */
$dyor_map_src = function ( $season, $season_posts ) {
  $node_id = '';

  foreach ( $season_posts as $season_post ) {
    $node_id = (string) get_post_meta( $season_post->ID, '_nm_dyor_figma_node_id', true );

    if ( '' !== $node_id ) {
      break;
    }
  }

  if ( '' === $node_id ) {
    $node_id = $season['figma_default_node_id'];
  }

  $embed_params = array(
    'embed-host'    => 'share',
    'footer'        => 'false',
    'page-selector' => 'false',
  );

  if ( '' !== $node_id ) {
    $embed_params['node-id'] = $node_id;
  }

  return 'https://embed.figma.com/board/' . rawurlencode( $season['figma_file_key'] ) . '/Do-Your-Own-Research-Map?' . http_build_query( $embed_params );
};

$latest_post = ! empty( $dyor_posts ) ? $dyor_posts[0] : null;
```

Replace the Latest Episode section (from `<?php // ── Section 3: Latest Episode (page 1 only) ── ?>` through its closing `<?php } ?>`) with:

```php
  <?php // ── Latest Episode ── ?>
  <?php
  if ( $latest_post ) {
    global $post;
    $post = $latest_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- restored by wp_reset_postdata() below.
    setup_postdata( $post );

    $latest_meta        = get_post_meta( get_the_ID() );
    $latest_description = ! empty( $latest_meta['_cmb_short_desc'][0] ) ? $latest_meta['_cmb_short_desc'][0] : get_the_excerpt();
    $latest_season      = (int) get_post_meta( get_the_ID(), '_nm_season', true );
    $latest_episode     = (int) get_post_meta( get_the_ID(), '_nm_episode', true );
    ?>
  <section class="container mb-5">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 text-align-center mb-4">
        <h4 class="ui-boxed-title">The Latest Episode</h4>
      </div>
    </div>
    <div class="dyor-archive__latest-episode grid-row">
      <div class="dyor-archive__latest-episode-image grid-item is-xxl-16 is-s-24 mb-s-4">
        <div class="ui-embed-container ui-rounded-box">
          <?php if ( ! empty( $latest_meta['_cmb_utube'][0] ) ) { ?>
            <?php echo render_youtube_embed_iframe( $latest_meta['_cmb_utube'][0], false, 'eager', get_the_title() ); ?>
          <?php } else { ?>
            <?php render_thumbnail( get_the_ID(), 'col16-16to9', array( 'class' => 'ui-rounded-box' ) ); ?>
          <?php } ?>
        </div>
      </div>
      <div class="dyor-archive__latest-episode-text grid-item is-xxl-8 is-s-24">
        <?php if ( $latest_season > 0 ) { ?>
        <h4 class="font-size-9 text-uppercase font-weight-bold mb-2" data-testid="latest-episode-label"><?php echo esc_html( 'Season ' . $latest_season . ( $latest_episode > 0 ? ', Episode ' . $latest_episode : '' ) ); ?></h4>
        <?php } ?>
        <h2 class="font-size-14 font-size-s-13 font-weight-bold text-wrap-pretty">
          <?php the_title(); ?>
        </h2>
        <h3 class="font-size-12 font-size-s-11 font-weight-bold mt-3 mt-s-2 text-wrap-pretty">
          <?php render_standfirst( get_the_ID() ); ?>
        </h3>
        <div class="font-size-10 mt-3 mt-s-2 text-wrap-pretty">
          <?php echo wp_kses_post( $latest_description ); ?>
        </div>
      </div>
    </div>
  </section>
    <?php
    wp_reset_postdata();
  }
  ?>
```

Replace the map section (`<?php // ── Section 4: Explore the Map ── ?>` through its closing `<?php } ?>`) and the post grid section (`<?php // ── Section 6: Post Grid ── ?>` through its `</section>`) with the season loop. Keep the `// ── Section 5: Newsletter Signup` comment line and the support module as they are.

```php
  <?php
  // ── Seasons, newest first ──
  // Maps load on click (ClickToLoad.js). Before loading it fires a cancelable
  // nm:click-to-load event on the container — the hook for the cookie
  // consent gate when it lands.
  foreach ( $posts_by_season as $season_number => $season_posts ) {
    $season = isset( $dyor_seasons[ $season_number ] ) ? $dyor_seasons[ $season_number ] : array(
      'number'                => $season_number,
      'title'                 => '',
      'description'           => '',
      'figma_file_key'        => '',
      'figma_default_node_id' => '',
    );

    $season_title = '' !== $season['title'] ? $season['title'] : 'Season ' . $season_number;
    ?>
  <section class="container mb-5 dyor-archive__season" data-testid="dyor-season" data-season="<?php echo esc_attr( $season_number ); ?>">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 text-align-center mb-4">
        <h4 class="ui-boxed-title"><?php echo esc_html( $season_title ); ?></h4>
        <?php if ( '' !== $season['description'] ) { ?>
        <div class="dyor-archive__season-description font-size-11 mt-4">
          <?php echo wp_kses_post( wpautop( $season['description'] ) ); ?>
        </div>
        <?php } ?>
      </div>
    </div>

    <?php if ( '' !== $season['figma_file_key'] ) { ?>
    <div class="grid-row background-white ui-rounded-box pt-4 pb-4 mb-4">
      <div class="grid-item is-xxl-24">
        <div
          class="dyor-archive__map ui-rounded-box"
          data-testid="dyor-season-map"
          data-click-to-load
          data-click-to-load-src="<?php echo esc_url( $dyor_map_src( $season, $season_posts ) ); ?>"
          data-click-to-load-title="<?php echo esc_attr( 'Do Your Own Research – ' . $season_title . ' map' ); ?>"
        >
          <button type="button" class="ui-button ui-button--black dyor-archive__map-button" data-click-to-load-button>
            <?php echo esc_html( 'Load the ' . $season_title . ' map' ); ?>
          </button>
        </div>
      </div>
    </div>
    <?php } ?>

    <div class="grid-row">
      <?php
      foreach ( $season_posts as $season_post ) {
        global $post;
        $post = $season_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- restored by wp_reset_postdata() below.
        setup_postdata( $post );

        get_template_part(
          'partials/post-layouts/archive-post',
          null,
          array(
            'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
            'image-size'        => 'col12-16to9',
            'text-size'         => 'large',
          )
        );
      }
      wp_reset_postdata();
      ?>
    </div>
  </section>
    <?php
  }
  ?>

  <?php if ( ! empty( $unseasoned_posts ) ) { ?>
  <section class="container mb-5" data-testid="dyor-unseasoned">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 text-align-center mb-4">
        <h4 class="ui-boxed-title">More from Do Your Own Research</h4>
      </div>
      <?php
      foreach ( $unseasoned_posts as $unseasoned_post ) {
        global $post;
        $post = $unseasoned_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- restored by wp_reset_postdata() below.
        setup_postdata( $post );

        get_template_part(
          'partials/post-layouts/archive-post',
          null,
          array(
            'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
            'image-size'        => 'col12-16to9',
            'text-size'         => 'large',
          )
        );
      }
      wp_reset_postdata();
      ?>
    </div>
  </section>
  <?php } ?>
```

The existing grid's `data-testid="post-list"` wrapper goes away with the old grid. If `verifyCriticalPageStructure` or another spec requires `post-list` on this page, put `data-testid="post-list"` on a `<div>` wrapping all the season sections instead. Check with `grep -rn "post-list" tests/e2e`.

Local check for Review Focus 2 and 3: clear `_nm_season` on one local season 1 post and confirm it appears under "More from Do Your Own Research". Temporarily set one post to `_nm_season 3` (no options entry) and confirm a "Season 3" block with no map and no PHP notice in `wp-content/debug.log`. Then restore both.

- [ ] **Step 6: Run the spec**

Run the Step 3 command.
Expected:
- `shows season blocks` PASS, with `[2, 1]` locally.
- `paged archive URLs redirect` PASS.
- `season maps load only on click` still FAILS on the click, because the JS lands in Task 3. The no-iframe-before-click assertion passes.

- [ ] **Step 7: Commit**

```bash
git add category-do-your-own-research.php lib/functions-hooks.php tests/e2e/dyor-archive.spec.js
git commit -m "feat: DYOR archive grouped by season, newest first, unpaged"
```

---

### Task 3: Click-to-load maps

**Files:**
- Create: `src/js/modules/ClickToLoad.js`
- Modify: `src/js/main.js`
- Modify: `src/styl/pages/dyor-archive.styl` (replace `// Map embed` rules)
- Modify: `tests/e2e/helpers/fixtures.js` (add `figma.com` to `EMBED_HOSTS`)
- Modify: `dist/` (rebuild)

**Interfaces:**
- Consumes: markup from Task 2 (`[data-click-to-load]`, `data-click-to-load-src`, `data-click-to-load-title`, `[data-click-to-load-button]`).
- Produces:
  - Class `ClickToLoad` with `onReady()` and `load( container )`.
  - Event `nm:click-to-load`: bubbles, cancelable, dispatched on the container.
  - Class `is-loaded` on the container once the iframe is inserted.

- [ ] **Step 1: Block Figma in the test fixture**

In `tests/e2e/helpers/fixtures.js`, add `'figma.com',` to `EMBED_HOSTS` after `'spotify.com',`. That stops the loaded map from making real Figma requests in tests.

- [ ] **Step 2: Confirm the click test still fails**

Run: `PLAYWRIGHT_BASE_URL=https://novaramediacom.local npx playwright test --config=<scratchpad>/playwright.local.config.js dyor-archive -g "click"`
Expected: FAIL. After the click, no iframe appears.

- [ ] **Step 3: Write the module**

```js
/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

/**
 * Click-to-load embeds. A container marked data-click-to-load holds the
 * embed URL in data-click-to-load-src and a [data-click-to-load-button];
 * nothing is requested from the embed host until that button is clicked.
 *
 * Before loading, a cancelable, bubbling `nm:click-to-load` event fires on
 * the container. A listener that calls preventDefault() stops the load —
 * the seam for a cookie consent gate.
 */
export class ClickToLoad {
  onReady() {
    document.querySelectorAll('[data-click-to-load]').forEach((container) => {
      const button = container.querySelector('[data-click-to-load-button]');

      if (!button) {
        return;
      }

      button.addEventListener('click', () => this.load(container));
    });
  }

  load(container) {
    if (container.classList.contains('is-loaded') || !container.dataset.clickToLoadSrc) {
      return;
    }

    const allowed = container.dispatchEvent(
      new CustomEvent('nm:click-to-load', { bubbles: true, cancelable: true })
    );

    if (!allowed) {
      return;
    }

    const iframe = document.createElement('iframe');

    iframe.src = container.dataset.clickToLoadSrc;
    iframe.title = container.dataset.clickToLoadTitle || '';
    iframe.setAttribute('allowfullscreen', '');

    container.appendChild(iframe);
    container.classList.add('is-loaded');
  }
}
```

In `src/js/main.js`:
- Add `import { ClickToLoad } from './modules/ClickToLoad.js';` after the `AudioPlayers` import.
- Add `this.clickToLoad = new ClickToLoad();` after `this.audioPlayers = new AudioPlayers();`.
- Add `this.clickToLoad.onReady();` after `this.audioPlayers.onReady();`.

- [ ] **Step 4: Styles**

In `src/styl/pages/dyor-archive.styl`, replace the `// Map embed` block (`.dyor-archive__map-embed` and its nested `iframe`) with:

```stylus
// Season map: a short banner with a centred button until clicked, then it
// opens to full map height with the Figma iframe filling it.
.dyor-archive__map
  position: relative
  display: flex
  align-items: center
  justify-content: center
  height: 160px
  background-color: var(--color-gray-light)
  overflow: hidden
  transition: height 0.4s ease

  iframe
    position: absolute
    inset: 0
    width: 100%
    height: 100%
    border: 1px solid rgba(0, 0, 0, 0.1)

  &.is-loaded
    height: clamp(450px, 70vh, 85vh)

    .dyor-archive__map-button
      display: none

@media screen and (max-width: $breakpoint-m)
  .dyor-archive__map
    height: 120px

    &.is-loaded
      height: 450px

@media (prefers-reduced-motion: reduce)
  .dyor-archive__map
    transition: none
```

- [ ] **Step 5: Build and run the spec**

Run: `npm run build > <workspace>/build.log 2>&1; tail -5 <workspace>/build.log`, then the Step 2 command without `-g`.
Expected: the build completes with no errors, and all 3 dyor-archive tests PASS.

- [ ] **Step 6: Manual check (Review Focus 5)**

On the local site, `/dyor/`:
- Season 2's block sits above season 1's.
- The Latest Episode label reads "Season 2, Episode 3".
- Each banner shows its button. Clicking one animates it open and loads the map; clicking the other loads the second map.
- With JS disabled (Chrome DevTools → disable JavaScript), the banners still show their buttons at banner height, with no empty full-height box.

- [ ] **Step 7: Commit**

```bash
git add src/js/modules/ClickToLoad.js src/js/main.js src/styl/pages/dyor-archive.styl tests/e2e/helpers/fixtures.js dist
git commit -m "feat: DYOR season maps load on click"
```

---

### Task 4: Docs, changelog, PR

**Files:**
- Modify: `CHANGELOG.md`
- Modify: `docs/post-deploy-checklist.md` (`## Unreleased`)
- Modify: `docs/superpowers/specs/2026-09-29-dyor-seasons-design.md` (status)
- PR #626 title and body (via `gh api`; `gh pr edit` silently no-ops on this repo)

- [ ] **Step 1: Changelog**

Under `## [Unreleased]` → `### Added`:

```markdown
- Do Your Own Research archive splits episodes by season, each with its own map that loads on click
```

Add a `### Deprecated` section after `### Fixed`:

```markdown
### Deprecated

- Do Your Own Research category map fields, replaced by Products → Do Your Own Research. Migration: open that page and Save once after deploy
```

- [ ] **Step 2: Checklist**

Append to `## Unreleased`, numbered after the existing last item:

```markdown
### N. Save the Do Your Own Research seasons
**Admin > Products > Do Your Own Research.** The page shows nothing saved yet; the archive meanwhile builds season 1 from the old category map fields. Add a season 1 entry (number 1, title "Season 1", the file key and default node from the category's Do Your Own Research box) and Save. Then add season 2 with its FigJam file key and Save again.

Verify: `/dyor/` shows a Season 2 block above Season 1 once season 2 episodes have their Season / Episode box set, and each map loads on click.
```

- [ ] **Step 3: Spec status**

Change `**Status:** Agreed design, pre-implementation` to `**Status:** Implemented (this branch)`.

- [ ] **Step 4: Commit and push**

```bash
git add CHANGELOG.md docs/post-deploy-checklist.md docs/superpowers/specs/2026-09-29-dyor-seasons-design.md
git commit -m "docs: DYOR seasons changelog, checklist and spec status"
git push
```

- [ ] **Step 5: Retitle and rewrite PR #626**

Write the body to `<scratchpad>/pr-626.md`, covering:
- summary: margins fix plus seasons
- the Products page and seed
- the season blocks
- click-to-load and the consent seam
- the unpaged redirect
- test plan
- deploy steps

Then:

```bash
gh api -X PATCH repos/novaramedia/novaramedia-com/pulls/626 -f "title=Feature: Do Your Own Research seasons" -F "body=@<scratchpad>/pr-626.md" -q '.title'
```

Expected: prints `Feature: Do Your Own Research seasons`.
