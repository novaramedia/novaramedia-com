# Season / Episode Post Meta Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Optional season, episode number and episode label meta on every post, with the capsule podcast archives (Committed, Foreign Agent, Death in Westminster) switched from standfirst-as-label to that meta.

**Architecture:** One new CMB2 side box (`lib/meta/meta-boxes-post-episode.php`) with three fields. One new generic rule in the validation fork (`data-validation-required-with`). The three capsule templates render the label from meta. A one-off WP-CLI script backfills existing posts. The two drifted serial-podcast slug lists merge into one function.

**Tech Stack:** PHP 8 / WordPress, CMB2, jQuery (validation fork), WP-CLI (`wp eval-file`), Playwright (`tests/e2e/`).

**Spec:** `docs/superpowers/specs/2026-09-29-season-episode-meta-design.md`

## Global Constraints

- Branch `feature/season-episode-meta`, one PR against `development`. Never commit to `development`. The user merges.
- No build-system (webpack) changes and no `dist/` commit. This plan touches no Stylus or bundled JS.
- Meta keys, exactly: `_nm_season`, `_nm_episode`, `_nm_episode_label`.
- Number fields store a positive integer as a string, or nothing. Label stores `sanitize_text_field` output, or nothing.
- A season is required (on publish) whenever an episode number or label is set. A season alone is allowed.
- Order by publish date everywhere. Never `orderby => meta_value_num` on `_nm_episode`.
- No shared label-formatting helper. Each template writes its own wording.
- No parsing of standfirsts in templates. The one-off backfill script may read them, once.
- WordPress coding standards per `phpcs.xml`: two-space indent, `esc_*` on output.
- `CHANGELOG.md` entry under `[Unreleased]`, terse (feature + one user-facing fact).

## Review Focus

1. **Stray input in number fields** (`0`, `-1`, `3.5`, `abc`, ` 4 `). Expect only `4` from ` 4 ` to be stored; everything else is stored as nothing. Pinned by Task 1 Step 4.
2. **Label or episode set, season blank, Publish clicked.** Expect publish blocked with the Season row highlighted. Save Draft still saves. Pinned by Task 2 Step 4.
3. **Capsule post with neither episode nor label.** Expect no empty `<h4>` in the archive. Pinned by the guard in Task 3 and by Task 3 Step 5.
4. **CI runs against staging before staging has the backfill.** The Playwright label test would fail. Expect the backfill applied to staging before the PR's CI run. Pinned by Task 4 Step 5 ordering.
5. **A DYOR post that isn't an episode (trailer, clip) gets an episode number from date order.** Expect the dry run to show it so it can be handled before `apply`. Pinned by Task 4 Step 3.

---

### Task 1: Season / Episode meta box

**Files:**
- Create: `lib/meta/meta-boxes-post-episode.php`
- Modify: `functions.php:146` (load the new file after `meta-boxes-post-dyor`)

**Interfaces:**
- Produces:
  - Meta keys `_nm_season`, `_nm_episode` (positive-int strings) and `_nm_episode_label` (string).
  - Input element ids equal to those keys (CMB2 uses the field id as the input id). Task 2 relies on this.
  - `nm_sanitize_positive_int_meta( $value ): string`

- [ ] **Step 1: Create the meta box file**

```php
<?php
add_action( 'cmb2_init', 'nm_cmb_post_episode_metaboxes' );

/**
 * Sanitise a CMB2 number field to a positive integer string.
 * Returns '' for blank or invalid input so CMB2 deletes the meta
 * rather than storing it. filter_var trims surrounding whitespace.
 *
 * @param mixed $value Raw submitted value.
 * @return string
 */
function nm_sanitize_positive_int_meta( $value ) {
  $int = filter_var( $value, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );

  return false === $int ? '' : (string) $int;
}

/**
 * Declares the Season / Episode box for posts that are part of an episodic
 * series (capsule podcasts, seasonal shows, article series). The show is the
 * post's category; these fields only number the post within it.
 *
 * Templates format the values themselves; there is deliberately no shared
 * label helper. Order queries by date, never by _nm_episode: bonus and
 * trailer posts have a label but no number.
 */
function nm_cmb_post_episode_metaboxes() {
  $prefix = '_nm_';

  $cmb_episode = new_cmb2_box( array(
    'id'           => $prefix . 'episode_post_edit',
    'title'        => esc_html__( 'Season / Episode', 'cmb2' ),
    'object_types' => array( 'post' ),
    'context'      => 'side',
    'priority'     => 'default',
  ) );

  $cmb_episode->add_field( array(
    'name'            => esc_html__( 'Season', 'cmb2' ),
    'desc'            => esc_html__( 'Leave blank unless this post is part of an episodic series. Required if an episode number or label is set.', 'cmb2' ),
    'id'              => $prefix . 'season',
    'type'            => 'text_small',
    'sanitization_cb' => 'nm_sanitize_positive_int_meta',
    'attributes'      => array(
      'type'                          => 'number',
      'min'                           => '1',
      'step'                          => '1',
      'data-validation'               => 'true',
      'data-validation-required-with' => $prefix . 'episode,' . $prefix . 'episode_label',
    ),
  ) );

  $cmb_episode->add_field( array(
    'name'            => esc_html__( 'Episode number', 'cmb2' ),
    'id'              => $prefix . 'episode',
    'type'            => 'text_small',
    'sanitization_cb' => 'nm_sanitize_positive_int_meta',
    'attributes'      => array(
      'type' => 'number',
      'min'  => '1',
      'step' => '1',
    ),
  ) );

  $cmb_episode->add_field( array(
    'name'            => esc_html__( 'Episode label', 'cmb2' ),
    'desc'            => esc_html__( 'For posts that are not a numbered episode, e.g. "Bonus 1", "Trailer", "Credits". Shown instead of the number when set.', 'cmb2' ),
    'id'              => $prefix . 'episode_label',
    'type'            => 'text',
    'sanitization_cb' => 'sanitize_text_field',
  ) );
}
```

- [ ] **Step 2: Load it**

In `functions.php`, after `get_template_part( 'lib/meta/meta-boxes-post-dyor' );` add:

```php
get_template_part( 'lib/meta/meta-boxes-post-episode' );
```

- [ ] **Step 3: Lint**

Run: `php -l lib/meta/meta-boxes-post-episode.php && php -l functions.php`
Expected: `No syntax errors detected` twice.

- [ ] **Step 4: Verify the sanitiser against stray input (Review Focus 1)**

Run:

```bash
php -r 'require "lib/meta/meta-boxes-post-episode.php"; foreach ( array( "4", " 4 ", "0", "-1", "3.5", "abc", "" ) as $v ) { var_export( array( $v => nm_sanitize_positive_int_meta( $v ) ) ); echo "\n"; }' 2>&1 | grep -v add_action
```

`add_action` is undefined outside WordPress, so stub it if the require fatals: prefix the `-r` code with `function add_action(){}`.
Expected: `'4' => '4'`, `' 4 ' => '4'`, and `''` for `0`, `-1`, `3.5`, `abc` and blank.

- [ ] **Step 5: Manual check in wp-admin**

On the local site, edit any post. The "Season / Episode" box appears in the sidebar. Enter season `2`, episode `7`, label blank, and Save Draft. Reload: values persist. Clear all three, save, and reload: fields are empty (meta deleted, not stored as `''`).

- [ ] **Step 6: Commit**

```bash
git add lib/meta/meta-boxes-post-episode.php functions.php
git commit -m "feat: season, episode and episode label post meta"
```

---

### Task 2: `data-validation-required-with` rule

**Files:**
- Modify: `lib/meta/cmb2-validation.php` (header docblock lines 3–47, rule evaluation around the `requiredCategorySlug` block)

**Interfaces:**
- Consumes: the `_nm_season` attribute `data-validation-required-with="_nm_episode,_nm_episode_label"` from Task 1. Target ids are element ids.
- Produces: generic rule `data-validation-required-with="<id>[,<id>…]"`, meaning required when any named field is non-empty.

- [ ] **Step 1: Document the rule**

In the header, bump `Version: 0.5.0` to `Version: 0.6.0`. Add a bullet after the `data-validation-not-required-category` bullet:

```php
 * - data-validation-required-with="<id>[,<id>…]": required when any of the
 *   named fields (by element id — CMB2 uses the field id) has a value.
 *   Combines with the category rules: the exempt category still wins.
```

- [ ] **Step 2: Evaluate the rule**

After the block that applies `requiredCategorySlug` (`if ( ! isRequired && typeof requiredCategorySlug !== 'undefined' ) { … }`) and **before** the exempt-category block, insert:

```js
        // Required when any named sibling field has a value, e.g. season is
        // required once an episode number or label is filled in.
        const requiredWith = $this.attr( 'data-validation-required-with' );

        if ( ! isRequired && typeof requiredWith !== 'undefined' ) {
          isRequired = requiredWith.split( ',' ).some( function( id ) {
            const el = document.getElementById( id.trim() );

            return el !== null && ! is_empty_value( $( el ).val() );
          });
        }
```

- [ ] **Step 3: Clear stale highlights for the new rule**

Change the final `else if` condition from:

```js
        } else if ( typeof requiredCategorySlug !== 'undefined' || typeof exemptCategorySlug !== 'undefined' ) {
```

to:

```js
        } else if ( typeof requiredCategorySlug !== 'undefined' || typeof exemptCategorySlug !== 'undefined' || typeof requiredWith !== 'undefined' ) {
```

Update the comment above it to read: "Conditionally-required field whose condition isn't met (category not ticked, sibling fields empty), or a field exempted by a ticked exempt category: clear any stale highlight from a previous failed attempt."

- [ ] **Step 4: Manual check (Review Focus 2)**

`php -l lib/meta/cmb2-validation.php`, then on the local site, editing a draft post:

| Season | Episode | Label | Click | Expected |
|---|---|---|---|---|
| blank | 3 | blank | Publish | Blocked; alert names "Season"; row red |
| blank | blank | Bonus 1 | Publish | Blocked; same |
| blank | 3 | blank | Save Draft | Saves; no highlight |
| 1 | 3 | blank | Publish | Publishes; highlight from earlier attempt cleared |
| 1 | blank | blank | Publish | Publishes |
| blank | blank | blank | Publish | Publishes |

Revert the test post to draft afterwards. Existing rules still fire: blank standfirst on a non-Novara Live post blocks Publish.

- [ ] **Step 5: Commit**

```bash
git add lib/meta/cmb2-validation.php
git commit -m "feat: validation rule for fields required alongside another"
```

---

### Task 3: Capsule archive labels from meta

**Files:**
- Modify: `category-committed.php:254`
- Modify: `category-foreign-agent.php:183`
- Modify: `category-death-in-westminster.php:267`
- Create: `tests/e2e/capsule-podcast-archives.spec.js`

**Interfaces:**
- Consumes: `_nm_episode`, `_nm_episode_label` from Task 1.
- Produces: `<h4 … data-testid="episode-label">` per labelled post, and no `<h4>` for an unlabelled one.

- [ ] **Step 1: Write the failing Playwright test**

```js
/**
 * Capsule podcast archives
 *
 * Episode labels come from season/episode meta (_nm_episode,
 * _nm_episode_label), not the standfirst. Expected strings are the labels
 * live on 2026-09-29, so the switch must be invisible to readers.
 * Requires the season/episode backfill on the target environment.
 */

const { test, expect } = require('./helpers/fixtures');
const gotoFresh = require('./helpers/gotoFresh');

const ARCHIVES = [
  {
    path: '/category/committed/',
    labels: ['Episode 1', 'Episode 2', 'Episode 3', 'Episode 4'],
  },
  {
    path: '/category/foreign-agent/',
    labels: [
      'Episode 1', 'Episode 2', 'Episode 3', 'Bonus 1', 'Episode 4',
      'Episode 5', 'Bonus 2', 'Episode 6', 'The producers', 'Credits',
    ],
  },
  {
    path: '/category/death-in-westminster/',
    labels: [
      'Episode 1', 'Episode 2', 'Episode 3',
      'Episode 4', 'Episode 5', 'Episode 6',
    ],
  },
];

for (const archive of ARCHIVES) {
  test.describe(`Capsule archive ${archive.path}`, () => {
    test('renders episode labels from meta in date order', async ({ page }) => {
      await gotoFresh(page, archive.path);

      await expect(page.getByTestId('episode-label')).toHaveText(archive.labels);
    });

    test('renders no empty episode label', async ({ page }) => {
      await gotoFresh(page, archive.path);

      const labels = await page.getByTestId('episode-label').allTextContents();

      expect(labels.every((text) => text.trim() !== '')).toBe(true);
    });
  });
}
```

- [ ] **Step 2: Run it to see it fail**

Run: `npx playwright test tests/e2e/capsule-podcast-archives.spec.js`
Expected: FAIL. No element has `data-testid="episode-label"` yet.

- [ ] **Step 3: Replace the standfirst `<h4>` in each template**

`category-committed.php:254`. Replace:

```php
          <h4 class="font-size-9 text-uppercase font-weight-bold mb-2 mb-s-0"><?php echo $meta['_cmb_standfirst'][0]; ?></h4>
```

with:

```php
          <?php
          // Label override (bonus, credits) wins; else the episode number.
          $episode_label  = get_post_meta( get_the_ID(), '_nm_episode_label', true );
          $episode_number = (int) get_post_meta( get_the_ID(), '_nm_episode', true );

          if ( '' === $episode_label && $episode_number > 0 ) {
            $episode_label = 'Episode ' . $episode_number;
          }

          if ( '' !== $episode_label ) {
            ?>
          <h4 class="font-size-9 text-uppercase font-weight-bold mb-2 mb-s-0" data-testid="episode-label"><?php echo esc_html( $episode_label ); ?></h4>
            <?php
          }
          ?>
```

`category-foreign-agent.php:183`. Same replacement. The `<h4>` classes are identical (`mb-2 mb-s-0`).

`category-death-in-westminster.php:267`. Same logic, keeping its own classes:

```php
          <h4 class="font-size-9 text-uppercase font-weight-bold mb-2 mb-s-1" data-testid="episode-label"><?php echo esc_html( $episode_label ); ?></h4>
```

- [ ] **Step 4: Lint**

Run: `php -l category-committed.php && php -l category-foreign-agent.php && php -l category-death-in-westminster.php`
Expected: no syntax errors.

- [ ] **Step 5: Verify locally (Review Focus 3)**

Task 4's backfill must be applied to the local DB first, or set the meta by hand on two or three local posts. Load each archive: labels match the table in Step 1. Clear the episode and label on one post: its `<h4>` is gone, and the title still renders with no layout gap beyond the missing line.

- [ ] **Step 6: Commit**

```bash
git add category-committed.php category-foreign-agent.php category-death-in-westminster.php tests/e2e/capsule-podcast-archives.spec.js
git commit -m "feat: capsule podcast archives label episodes from meta"
```

The test passes in CI only once staging has the backfill (Task 4 Step 5).

---

### Task 4: One-off backfill script

**Files:**
- Create: `scripts/one-off/2026-09-season-episode-backfill.php`

**Interfaces:**
- Consumes: meta keys from Task 1.
- Produces: `wp eval-file scripts/one-off/2026-09-season-episode-backfill.php [apply]`. Dry run by default.

- [ ] **Step 1: Write the script**

```php
<?php
/**
 * One-off: backfill season/episode meta on existing episodic posts.
 *
 * Capsule podcasts (Committed, Foreign Agent, Death in Westminster) put their
 * episode label in the standfirst. "Episode N" becomes _nm_episode = N; any
 * other standfirst ("Bonus 1", "Credits") becomes _nm_episode_label. Every
 * post gets _nm_season = 1. Standfirsts are NOT modified — editorial rewrites
 * them after deploy (docs/post-deploy-checklist.md).
 *
 * Do Your Own Research season 1: episode numbers follow publish order.
 *
 * Usage (dry run, prints the plan):  wp eval-file scripts/one-off/2026-09-season-episode-backfill.php
 * Apply:                            wp eval-file scripts/one-off/2026-09-season-episode-backfill.php apply
 *
 * Safe to re-run: it overwrites the same keys with the same values.
 */

$nm_apply = isset( $args[0] ) && 'apply' === $args[0];

$nm_capsule_slugs = array( 'committed', 'foreign-agent', 'death-in-westminster' );
$nm_dyor_slug     = 'do-your-own-research';

$nm_get_posts_in_date_order = function ( $slug ) {
  return get_posts( array(
    'category_name'  => $slug,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
  ) );
};

$nm_write = function ( $post_id, $season, $episode, $label ) use ( $nm_apply ) {
  WP_CLI::log( sprintf( '  #%d  season=%d  episode=%s  label=%s', $post_id, $season, $episode ? $episode : '-', '' !== $label ? $label : '-' ) );

  if ( ! $nm_apply ) {
    return;
  }

  update_post_meta( $post_id, '_nm_season', (string) $season );

  if ( $episode ) {
    update_post_meta( $post_id, '_nm_episode', (string) $episode );
    delete_post_meta( $post_id, '_nm_episode_label' );
  } else {
    delete_post_meta( $post_id, '_nm_episode' );
    update_post_meta( $post_id, '_nm_episode_label', $label );
  }
};

foreach ( $nm_capsule_slugs as $slug ) {
  WP_CLI::log( $slug );

  foreach ( $nm_get_posts_in_date_order( $slug ) as $post ) {
    $standfirst = trim( wp_strip_all_tags( (string) get_post_meta( $post->ID, '_cmb_standfirst', true ) ) );

    if ( preg_match( '/^episode\s+(\d+)$/i', $standfirst, $matches ) ) {
      $nm_write( $post->ID, 1, (int) $matches[1], '' );
    } elseif ( '' !== $standfirst ) {
      $nm_write( $post->ID, 1, 0, $standfirst );
    } else {
      WP_CLI::warning( sprintf( '#%d %s has no standfirst — skipped, set by hand', $post->ID, $post->post_name ) );
    }
  }
}

WP_CLI::log( $nm_dyor_slug . ' (season 1, publish order — check for non-episode posts)' );

foreach ( $nm_get_posts_in_date_order( $nm_dyor_slug ) as $index => $post ) {
  $nm_write( $post->ID, 1, $index + 1, '' );
  WP_CLI::log( '      ' . $post->post_title );
}

WP_CLI::success( $nm_apply ? 'Applied.' : 'Dry run only. Re-run with "apply" to write.' );
```

- [ ] **Step 2: Lint**

Run: `php -l scripts/one-off/2026-09-season-episode-backfill.php`
Expected: no syntax errors.

- [ ] **Step 3: Dry run on staging and review (Review Focus 5)**

Run via SSH on Kinsta staging, from the theme directory: `wp eval-file scripts/one-off/2026-09-season-episode-backfill.php`

Check the output against Task 3 Step 1's label table. For DYOR, confirm every listed post is a season 1 episode. Any trailer, clip or season 2 post must be handled first: set its meta by hand and exclude it by editing the script's DYOR loop to skip its ID. Paste the dry-run output into the PR description.

- [ ] **Step 4: Commit**

```bash
git add scripts/one-off/2026-09-season-episode-backfill.php
git commit -m "chore: one-off season/episode backfill script"
```

- [ ] **Step 5: Apply on staging before the PR's CI run (Review Focus 4)**

The user approves this step. It writes to the staging DB.
Run: `wp eval-file scripts/one-off/2026-09-season-episode-backfill.php apply`
Then run `npx playwright test tests/e2e/capsule-podcast-archives.spec.js` against staging. Expected: PASS.

---

### Task 5: One serial-podcast slug list

**Files:**
- Modify: `lib/functions-custom.php:101-132` (`nm_serial_podcast_redirect`)
- Modify: `lib/functions-hooks.php:148-168` (`podcast_series_pre_get_posts`)

**Interfaces:**
- Produces: `nm_get_serial_podcast_slugs(): string[]`

- [ ] **Step 1: Add the function**

In `lib/functions-custom.php`, directly above `nm_serial_podcast_redirect`:

```php
/**
 * Category slugs of serial (capsule) podcasts. Their archives list every
 * episode oldest first, and their single posts redirect to the archive
 * anchor. Order stays by date: bonus posts have no episode number.
 *
 * @return string[]
 */
function nm_get_serial_podcast_slugs() {
  return array( 'foreign-agent', 'committed', 'death-in-westminster' );
}
```

- [ ] **Step 2: Use it in both places**

In `nm_serial_podcast_redirect`, replace:

```php
  // Slugs of serial podcasts you want this redirect behavior for:
  $serial_slugs = array( 'foreign-agent', 'committed', 'death-in-westminster' );
```

with:

```php
  $serial_slugs = nm_get_serial_podcast_slugs();
```

In `podcast_series_pre_get_posts`, replace:

```php
  $serial_categories = array( 'foreign-agent', 'committed' ); // Add more slugs as needed
```

with:

```php
  $serial_categories = nm_get_serial_podcast_slugs();
```

This adds `death-in-westminster` to the all-posts, oldest-first query. Before committing, load `/category/death-in-westminster/` on the local site and confirm it still lists Episode 1 → 6 in that order. If it flips, the posts' dates are set in reverse on purpose: stop and ask the user.

- [ ] **Step 3: Lint and verify**

Run: `php -l lib/functions-custom.php && php -l lib/functions-hooks.php`, then `npx playwright test tests/e2e/capsule-podcast-archives.spec.js tests/e2e/vanity-urls.spec.js` locally or against staging.
Expected: PASS. Death in Westminster's label order is unchanged.

- [ ] **Step 4: Commit**

```bash
git add lib/functions-custom.php lib/functions-hooks.php
git commit -m "refactor: single serial podcast slug list; Death in Westminster lists all episodes"
```

---

### Task 6: Docs

**Files:**
- Modify: `docs/post-deploy-checklist.md` (`## Unreleased` section)
- Modify: `CHANGELOG.md` (`## [Unreleased]`)
- Modify: `docs/superpowers/specs/2026-09-29-season-episode-meta-design.md` (Status line)

- [ ] **Step 1: Checklist steps**

Append to `## Unreleased` in `docs/post-deploy-checklist.md`, numbered after the existing items:

````markdown
### N. Backfill season/episode meta — BEFORE deploying
Plain post meta, so it can run before the new templates ship; they then find it on first render.

```bash
wp eval-file scripts/one-off/2026-09-season-episode-backfill.php        # dry run, check output
wp eval-file scripts/one-off/2026-09-season-episode-backfill.php apply
```

Verify after deploy: `/category/committed/`, `/category/foreign-agent/` and `/category/death-in-westminster/` show the same episode labels as before.

### N+1. Rewrite capsule podcast standfirsts
The standfirst on each Committed, Foreign Agent and Death in Westminster post still reads "Episode 1", "Bonus 1" etc. Archives no longer use it. Replace each with a real standfirst (editorial), since it shows on single posts, in search results and in related posts.
````

- [ ] **Step 2: Changelog**

Under `## [Unreleased]`, add under `### Added` (create the heading if absent):

```markdown
- Season and episode numbers can be set on any post; capsule podcast archives now take their episode labels from them
```

- [ ] **Step 3: Spec status**

In the spec, change `**Status:** Agreed design, pre-implementation` to `**Status:** Implemented (this branch)`.

- [ ] **Step 4: Commit**

```bash
git add docs/post-deploy-checklist.md CHANGELOG.md docs/superpowers/specs/2026-09-29-season-episode-meta-design.md
git commit -m "docs: season/episode backfill checklist steps and changelog"
```
