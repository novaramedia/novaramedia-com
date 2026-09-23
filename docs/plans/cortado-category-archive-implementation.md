# The Cortado Category Archive — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship a branded category archive for The Cortado at a canonical URL, with a vanity slug and a 301 from the newsletter CPT permalink, plus a shared thumbnail-less post layout that the If I Speak archive work will reuse.

**Architecture:** WordPress template hierarchy does the routing — `category-the-cortado.php` is picked up automatically for the `the-cortado` term. The vanity slug is an `add_rewrite_rule` entry; the newsletter 301 is a config-array-driven `template_redirect` hook. The page assembles existing partials (`email-signup`, `pagination`, the Downstream featured-post pattern) plus one new post layout partial.

**Tech Stack:** PHP 8 / WordPress, Stylus via nm-stylus-library, Webpack build, phpcs ("NM PHP Standard"). No automated tests in this plan — see Global Constraints.

**Spec:** `docs/plans/cortado-category-archive.md`

## Global Constraints

- Brand name is **The Cortado** in all copy and the wordmark. Category slug is `the-cortado`.
- **No CSS colour treatment on imagery.** Halftone/duotone and the cut-out hero photo are artworked and supplied finished. Templates render what is uploaded.
- **Do not modify the build system.** Webpack and release config need team approval (CLAUDE.md).
- `dist/` is committed only when source files actually changed — run `npm run build` to verify.
- **Write Playwright specs, not Cypress.** The Cypress suite was removed in #600; specs live in `tests/e2e/` and the guide is `docs/testing/testing.md`.
- **Do not write Playwright specs on this branch either.** The harness and helpers live on the phase branches and are not on `development` yet, so they cannot run here. A Cortado spec is a follow-up once phase-1 lands; see Follow-ups.
- There is **no PHP unit test framework** in this repo. Verification for this work is phpcs + `curl` status checks + visual comparison against Figma in DevKinsta.
- Keep `data-testid` attributes on structural elements regardless. Playwright's `testIdAttribute` defaults to `data-testid`, so they carry over unchanged.
- PR target is `development`. Never commit to `development` directly.
- Consult the `nm-design-system` skill before writing any markup — utility classes, grid, spacing and type scale come from nm-stylus-library, not from invention.
- **Coloured sections use the front-page box pattern.** `section.container.mt-4.mb-4 > div.grid-item.is-xxl-24 > div.grid-row.background-{x}.ui-rounded-box.ui-backgrounded-box-padding`. Reference `partials/front-page/show-blocks/dyor.php`; rules in `docs/architecture/boxed-sections.md`. Not full-bleed, not the `--top`/`--bottom` split.

## Execution note

The user has explicitly asked to review each markup stage before it is committed. Tasks 4–8 each end with **presenting markup for approval**, not with an unattended commit. Do not batch them.

---

### Task 0: Prerequisites gate

Nothing below works until these exist. Neither is reachable from the repo; both are done by the user in WP admin.

**Files:** none

- [ ] **Step 1: Confirm the category exists**

In WP admin → Posts → Categories, confirm a category named **The Cortado**, slug `the-cortado`, parent **Articles**. Create it if absent.

- [ ] **Step 2: Capture the real canonical URL**

Visit the category from the admin list ("View"). Record the actual URL. It may be `/category/articles/the-cortado/` or `/category/the-cortado/` depending on how the parent term resolves — the existing Novara Live archive spec visits `/category/novara-live` with no parent segment, while `redirect_committed_custom_url()` uses `category/audio/committed/` with one. **Every later task uses the URL recorded here, not an assumed one.**

- [ ] **Step 3: Confirm the newsletter record**

In WP admin → Newsletters, confirm a newsletter post with slug `the-cortado` exists and has `_nm_mailchimp_key` set. Without the key, `partials/email-signup.php` returns early and the signup band renders nothing.

- [ ] **Step 4: Record findings in the spec**

Append the confirmed canonical URL and newsletter slug to `docs/plans/cortado-category-archive.md` under Prerequisites, then commit:

```bash
git add docs/plans/cortado-category-archive.md
git commit -m "docs: record confirmed Cortado category URL and newsletter slug"
```

---

### Task 1: Routing — vanity slug and newsletter redirect

**Files:**
- Modify: `lib/functions-rewrites.php`

**Interfaces:**
- Produces: `$nm_newsletter_category_redirects` config array and `handle_newsletter_category_redirects()`. No later task consumes these.

- [ ] **Step 1: Record the baseline**

Capture current behaviour before changing anything, so the change is provable. `CANONICAL` is the URL from Task 0 Step 2.

```bash
curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramediacom.local/the-cortado
curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramediacom.local/newsletters/the-cortado
```

Expected now: the first returns `404`. The second returns `200` with no redirect — the newsletter single renders in place.

- [ ] **Step 2: Add the vanity slug**

In `lib/functions-rewrites.php`, add to the `$internal_rewrites` array inside `handle_internal_rewrites()`:

```php
    array(
      'pattern'  => '^the-cortado/?$',
      'category' => 'the-cortado',
    ),
```

- [ ] **Step 3: Add the newsletter redirect section**

Append to `lib/functions-rewrites.php`:

```php
/** NEWSLETTER → CATEGORY REDIRECTS
 * -------------------------------------------------------------
 */

// Newsletter CPT permalinks that should 301 to a category archive.
// The newsletter record stays as the source of signup metadata; the
// category archive is the canonical destination for readers.
// Format: 'newsletter-slug' => 'category-slug'
$nm_newsletter_category_redirects = array(
  'the-cortado' => 'the-cortado',
);

add_action(
  'template_redirect',
  function () use ( $nm_newsletter_category_redirects ) {
    handle_newsletter_category_redirects( $nm_newsletter_category_redirects );
  }
);

/**
 * Redirects newsletter CPT singles to their category archive.
 *
 * @param array $redirects Associative array of newsletter slug => category slug.
 * @return void Exits script execution after issuing a redirect.
 */
function handle_newsletter_category_redirects( $redirects ) {
  if ( ! is_singular( 'newsletter' ) ) {
    return;
  }

  $newsletter = get_queried_object();

  if ( ! $newsletter || empty( $newsletter->post_name ) ) {
    return;
  }

  if ( ! isset( $redirects[ $newsletter->post_name ] ) ) {
    return;
  }

  $category = get_category_by_slug( $redirects[ $newsletter->post_name ] );

  if ( ! $category ) {
    return; // Category not created yet — leave the newsletter page reachable.
  }

  $link = get_term_link( $category );

  if ( is_wp_error( $link ) ) {
    return;
  }

  wp_safe_redirect( $link, 301 );
  exit;
}
```

- [ ] **Step 4: Flush rewrite rules**

Rewrite rules are cached. In WP admin, go to Settings → Permalinks and click Save (no changes needed). The new rule will not match until this is done.

- [ ] **Step 5: Verify both URLs**

```bash
curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramediacom.local/the-cortado
curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramediacom.local/newsletters/the-cortado
```

Expected: the vanity slug returns `200` with no redirect URL — it serves the archive in place, matching how `/downstream/` and `/acfm/` behave. The newsletter permalink returns `301` with the canonical archive as `redirect_url`.

Then open `/the-cortado` in a browser and confirm it renders the archive (via the `category.php` fallback at this stage — the bespoke template arrives in Task 4), not a 404.

- [ ] **Step 6: Lint**

```bash
phpcs --standard=phpcs.xml lib/functions-rewrites.php
```

Expected: no errors. Fix any reported.

- [ ] **Step 7: Add the post-deploy step**

In `docs/post-deploy-checklist.md`, add a rewrite-flush entry for this release, matching the file's existing format. Without it the vanity slug 404s in production after deploy.

- [ ] **Step 8: Commit**

```bash
git add lib/functions-rewrites.php docs/post-deploy-checklist.md
git commit -m "feat: route The Cortado vanity slug and redirect newsletter permalink"
```

---

### Task 2: Shared thumbnail-less post layout

The "Past issues" card: author avatar, headline, byline, date, excerpt, no image. Built as a general partial because the If I Speak archive card needs the same component.

**Files:**
- Create: `partials/post-layouts/archive-post-no-thumbnail.php`

**Interfaces:**
- Produces: a partial loaded via `get_template_part( 'partials/post-layouts/archive-post-no-thumbnail', null, $args )`, taking `grid-item-classes` (string, required — returns early if absent, matching its siblings) and `hide-excerpt` (bool, optional, off by default). An earlier draft carried a `text-size` arg copied from `archive-post.php`; it had only one reachable branch and was removed during review, so do not pass it.
- Consumed by: Task 7.

- [ ] **Step 1: Read the sibling partials**

Read `partials/post-layouts/archive-post.php` and `partials/post-layouts/list-post.php` in full. The new partial must match their conventions: the early return on missing `grid-item-classes`, `post_class()` on the wrapper, `$args`-driven sizing, and the existing `render_bylines()` / `render_standfirst()` / `render_short_description()` helpers rather than raw `the_excerpt()`.

- [ ] **Step 2: Identify the avatar helper**

The card shows a round author avatar. Find how author images are rendered elsewhere:

```bash
grep -rn "get_avatar\|contributor.*thumbnail\|render_.*author" lib/renderers.php partials/ | head -20
```

Use the existing helper. Contributors are a CPT (`single-contributor.php`), so the avatar likely comes from a contributor post's thumbnail rather than `get_avatar()`. Do not invent a new avatar path.

- [ ] **Step 3: Consult the design system**

Invoke the `nm-design-system` skill. Take grid, spacing, type-scale and colour classes from it. Do not invent class names.

- [ ] **Step 4: Write the partial**

Follow `archive-post.php`'s structure, minus every thumbnail branch, plus the avatar. Include a generic `data-testid="archive-post-no-thumbnail"` hook on the article element — this is a shared partial, so no brand-specific testid here; the Cortado grid wrapper gets its own in Task 7.

Decisions taken at review (2026-09-16): date uses `NM_DATE_FORMAT_LONG`, not the `dd/mm/yyyy` in the Figma frame, which was a design error; a post with no contributor thumbnail renders text only, no placeholder ring; the whole card is one anchor.

- [ ] **Step 5: Present the markup for approval**

Show the file to the user before committing. Do not proceed to Task 3 until approved.

- [ ] **Step 6: Lint**

```bash
phpcs --standard=phpcs.xml partials/post-layouts/archive-post-no-thumbnail.php
```

- [ ] **Step 7: Commit**

```bash
git add partials/post-layouts/archive-post-no-thumbnail.php
git commit -m "feat: add thumbnail-less archive post layout partial"
```

---

### Task 3: Configurable signup button label

**Files:**
- Modify: `lib/renderers.php:37` (`render_mailchimp_signup_form`)
- Modify: `partials/email-signup.php`

**Interfaces:**
- Produces: `render_mailchimp_signup_form( $mailchimp_key, $background_color = 'black', $button_color = 'red', $button_label = 'Sign up' )`. The `email-signup.php` partial accepts a `button-label` arg and passes it through.
- Consumed by: Task 5.

- [ ] **Step 1: Add the parameter**

In `lib/renderers.php`, change the signature to add a fourth parameter defaulting to `'Sign up'`, and update the submit input to use it:

```php
  <input class="email-signup__submit ui-button ui-button--<?php echo esc_attr( $button_color ); ?> fs-6" type="submit" value="<?php echo esc_attr( $button_label ); ?>" />
```

Update the function docblock to document the new parameter.

- [ ] **Step 2: Pass it through the partial**

In `partials/email-signup.php`, read an optional `button-label` arg alongside the existing colour overrides, and pass it as the fourth argument to `render_mailchimp_signup_form()`.

- [ ] **Step 3: Verify nothing else broke**

```bash
grep -rn "render_mailchimp_signup_form" --include="*.php" .
```

Confirm every existing call site passes three or fewer arguments and therefore keeps the `Sign up` default. Load an existing newsletter page in DevKinsta and confirm the button still reads "Sign up".

- [ ] **Step 4: Lint**

```bash
phpcs --standard=phpcs.xml lib/renderers.php partials/email-signup.php
```

- [ ] **Step 5: Commit**

```bash
git add lib/renderers.php partials/email-signup.php
git commit -m "feat: allow newsletter signup button label to be overridden"
```

---

### Task 4: Template skeleton and hero

**Files:**
- Create: `category-the-cortado.php`
- Create: source raster for the hero photo under `src/img/products/the-cortado/` — the
  newer convention, alongside `acfm`, `dyor` and `novara-fm`. Not `specials/banners/`, which
  is where the older If I Speak banner lives; see the As built note below.

**Interfaces:**
- Produces: `category-the-cortado.php`, picked up automatically by the WordPress template hierarchy for the `the-cortado` term. Tasks 5–8 add sections to this same file.

- [ ] **Step 1: Read the precedent**

Read `category-if-i-speak.php` in full. It is the closest existing pattern: oversized wordmark, brand colour, inline `<style>` block scoped by a `category-archive__<slug>` class, banner asset referenced from `dist/img/specials/banners/` with `.avif`/`.webp` variants selected by a body class.

- [ ] **Step 2: Export the hero asset from Figma**

The cut-out presenter photo on the black circle. Export from node `5172-2472` at 2x. Place the source raster in `src/img/products/the-cortado/` (see the note below — this is where they ended up). The build generates the avif/webp variants into `dist/` — do not hand-place files in `dist/`.

**As built (2026-09-16):** two assets, both under `src/img/products/the-cortado/` (the newer convention — `acfm`, `dyor`, `novara-fm` live there), not `specials/banners/`. The wordmark is vector art in Figma, exported as `the-cortado-wordmark.svg` (single path, fill `#F8F5F5`, Figma export cruft stripped); the presenters are Figma's own 2× render of the composed group via `download_assets` with `defaultScale: 2` → `the-cortado-presenters.png`. `get_screenshot` only renders at 1× and is not suitable for hero rasters.

**Asset replaced 2026-09-16:** Figma bakes the containing frame's background into an export as opaque pixels, so the first render carried the hero's ochre and could not sit on the front page's light background. Patrick supplied a transparent replacement, **1195×762**, flush to its bottom edge. The `width`/`height` attributes in both templates track that, not the original export. See `docs/architecture/image-assets.md` — a source replaced under the same filename does **not** regenerate its avif/webp, so delete the variants before rebuilding.

- [ ] **Step 3: Build and confirm the variants**

```bash
npm run build
ls -la dist/img/products/the-cortado/
```

Expected: avif and webp variants present.

- [ ] **Step 4: Consult the design system**

Invoke the `nm-design-system` skill for the type scale, container and grid classes the hero needs.

- [ ] **Step 5: Write the skeleton and hero**

`get_header()`, `$category = get_category( get_query_var( 'cat' ) );`, a `<main id="main-content" class="category-archive category-archive__the-cortado" data-testid="main-content">`, the inline `<style>` block, the hero markup (eyebrow `NEWSLETTER`, "THE CORTADO" wordmark, photo), then `get_footer()`. No post loop yet.

**As built (2026-09-16):** the hero is the front-page box — `section.container.mt-4.mb-4 > .grid-item.is-xxl-24 > .grid-row.background-ochre.ui-rounded-box.ui-backgrounded-box-padding.ui-backgrounded-box-padding--flush-bottom`. Wordmark is an `<img>` of the SVG inside the `<h1>` (alt gives the accessible name); presenters via `<picture>` avif/webp/png, flush to the box's bottom edge per the design. Two scoped CSS rules only. `--color-ochre` + `.background-ochre` + the `--flush-bottom` modifier are staged in `src/styl/upstream-to-library.styl` for library promotion; `pb-0` cannot do the flush because utilities compile before the UI module. Took three passes (full-bleed → `--top`/`--bottom` split → correct) — hence `docs/architecture/boxed-sections.md`. The xxl container is 1400px, not the 1200px the design-system skill claimed.

**As built (2026-09-18):** the template's inline `<style>` is gone. Its four rules moved to `src/styl/pages/the-cortado-archive.styl`, imported from `site.styl` after the other `pages/` files, following `dyor-archive.styl` and `novara-fm-archive.styl`. Static CSS belongs in the build, not the template; the five other category templates that still inline theirs (ACFM, Committed, Death in Westminster, Foreign Agent, If I Speak) are a separate cleanup. The front-page block's ring-colour override now targets `.ui-border` rather than `.ui-circle-image`, matching the archive — `.ui-border` is the class that draws the ring.

**Revised (2026-09-22):** the `NEWSLETTER` eyebrow above the wordmark was removed on design feedback. The hero now opens with the `<h1>` wordmark.

Add `data-testid="cortado-hero"` to the hero element, and `data-testid="main-content"` to the `<main>`. Note that `category-downstream.php` and `category-if-i-speak.php` both omit the `main-content` testid while `category.php` and `category-novara-live.php` carry it — follow the ones that have it, since Task 9's spec depends on it.

- [ ] **Step 6: Present the markup for approval**

Show the file and a browser screenshot next to the Figma frame. Do not proceed until approved.

- [ ] **Step 7: Lint and commit**

```bash
phpcs --standard=phpcs.xml category-the-cortado.php
git add category-the-cortado.php src/img/products/the-cortado/ dist/
git commit -m "feat: add The Cortado category archive template and hero"
```

---

### Task 5: Signup band

**Files:**
- Modify: `category-the-cortado.php`

**Interfaces:**
- Consumes: `button-label` arg from Task 3.

- [ ] **Step 1: Fetch the newsletter record**

Use the `category-downstream.php` pattern — `get_posts()` with `post_type => 'newsletter'`, `name => 'the-cortado'`, `posts_per_page => 1` — and guard on the result being non-empty before rendering.

- [ ] **Step 2: Render the band**

```php
get_template_part(
  'partials/email-signup',
  null,
  array(
    'newsletter_post_id' => $newsletter_post_id,
    'background-color'   => 'white',
    'hide-discover'      => true,
    'button-label'       => 'Get The Cortado',
  )
);
```

- [ ] **Step 3: Verify against the design**

Patrick, 2026-09-16: the band is a **separate full-width section beneath the hero**, using the partial's white mode — not a white card inside the ochre box. Follow the Figma from here on, with flexibility.

**As built (2026-09-16), approved:** newsletter record fetched in the header block (Downstream pattern), rendered with `background-color: white`, `button-color: black`, `button-label: 'Get The Cortado'`, `hide-discover`, `hide-headline`, `hide-image`. The last two are new args on the partial, same shape as `hide-discover`, because record 51230 carries a headline, image and red button the Figma band does not have (they stay for `/newsletters/`). The partial's image-less form column was also widened from `is-l-10 is-xxl-8` to `is-l-12 is-xxl-12` so the row fills 24 and the form reaches the container edge as designed — this also closes the same 4-column gap for Downstream, Novara Live and The Pick on `/newsletters/` (reviewed, accepted). Strapline copy on the record is stale (Ash-only, weekly) — editorial fix in WP admin. Pre-existing, not fixed: `archive-newsletter.php` forces the Cortado block black but leaves the button on the alternating logic, giving a black button on black.

Load the page in DevKinsta. Confirm: white background, strapline left, form right, grey-bordered inputs, "Get The Cortado" on the button, and **no** "Discover all our newsletters" link inside the band — that moves to the footer row in Task 8.

- [ ] **Step 4: Present for approval, then lint and commit**

```bash
phpcs --standard=phpcs.xml category-the-cortado.php
git add category-the-cortado.php
git commit -m "feat: add signup band to The Cortado archive"
```

---

### Task 6: Latest Cortado featured block

**Files:**
- Modify: `category-the-cortado.php`

- [ ] **Step 1: Read the precedent**

Read the featured-post block in `category-downstream.php` (the `$is_first_page` branch). It calls `the_post()` once before the main loop, renders a large image left and title/standfirst right, then a rule.

- [ ] **Step 2: Implement**

Same shape, with the `LATEST CORTADO` eyebrow. The featured image is the post's own featured image, rendered through `render_thumbnail()` — **no CSS colour treatment**, per the Global Constraints. Add `data-testid="cortado-latest"`.

- [ ] **Step 3: Verify the pagination interaction**

Confirm the featured post is only pulled out on page 1, and that page 2 onwards does not skip or duplicate it. `category-downstream.php` handles this with `$is_first_page` and an incremented `$display_newsletter_after` — read how before implementing.

- [ ] **Step 4: Present for approval, then lint and commit**

```bash
phpcs --standard=phpcs.xml category-the-cortado.php
git add category-the-cortado.php
git commit -m "feat: add latest issue block to The Cortado archive"
```

**As built (2026-09-16), approved:** 12/12 grid, `col24-16to9` in `ui-rounded-box`, eyebrow
`font-size-8`, title `font-size-15` (NM 48 exact), byline and standfirst `font-size-10`.
Internal gaps are `mt-2` — Figma specifies 20-24px but the render was too loose at both
`mt-4` and `mt-3` (Patrick's call, two rounds).

One divider only, below the signup band, and it belongs to **this layout**, not the shared
partial (an earlier `show-border-bottom` arg on `partials/email-signup.php` was reverted —
the partial is unchanged). Critical detail, and the reason it took three attempts:

```php
<div class="grid-item is-xxl-24">
  <div class="ui-border-bottom ui-border--gray-mid"></div>
</div>
```

**The border must sit on an inner element, never on the `grid-item` itself.** Every
`.grid-item` carries `padding-left/right: calc(var(--grid-gutter)/2)` (8px), and a border on
it draws across the border box — overhanging the content columns by 8px each side. On an
inner element it spans the content box (1384px at xxl), aligning exactly with the strapline's
left edge, the form's right edge and the hero box. `ui-border--gray-mid` is `#D4D4D4`, matching
the Figma signup frame's `border-b` exactly. Figma shows no rule under the Latest block; a
second one was tried and removed.

The Figma applies the ochre to the featured image as `mix-blend-multiply`. Not reproduced —
treated imagery is artworked. Post 69500's thumbnail is now the design's own `Rectangle-910.png`.

---

### Task 7: Past issues grid

**Revised (2026-09-22):** heading copy is now `PAST CORTADOS`, and the front-page block's eyebrows are `LATEST CORTADO` / `PAST CORTADOS` (were `LATEST ISSUE` / `RECENT ISSUES`). Test ids and class names keep `past-issues`.

**Files:**
- Modify: `category-the-cortado.php`

**Interfaces:**
- Consumes: `partials/post-layouts/archive-post-no-thumbnail.php` from Task 2.

- [ ] **Step 1: Implement the loop**

`PAST ISSUES` heading, then the remaining posts in a three-column grid:

```php
get_template_part(
  'partials/post-layouts/archive-post-no-thumbnail',
  null,
  array(
    'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
  )
);
```

Confirm the grid classes against the `nm-design-system` skill — the values above mirror `category-downstream.php` and may need adjusting for this design.

Put `data-testid="cortado-past-issues"` on the grid's wrapping element, and set the ochre rule/ring colour in `src/styl/pages/the-cortado-archive.styl` by overriding `--ui-border-color` on the cards — the partial itself carries no brand colour.

- [ ] **Step 2: Settle pagination count**

The Figma frame shows 12 cards. Decide with the user whether to set `posts_per_page` for this archive or accept the site default, and record the decision in the spec's Open section.

- [ ] **Step 3: Present for approval, then lint and commit**

```bash
phpcs --standard=phpcs.xml category-the-cortado.php
git add category-the-cortado.php docs/plans/cortado-category-archive.md
git commit -m "feat: add past issues grid to The Cortado archive"
```

**As built (2026-09-16), approved:** 3-up (`is-s-24 is-l-12 is-xxl-8 mb-5`) using the shared
partial. Ochre card rules and avatar rings come from three lines of scoped CSS overriding
`--ui-border-color` — the partial supplies the rules, the consuming template picks the
colour, so If I Speak can differ. Card title dropped from `font-size-13` (Figma's NM 32) to
`font-size-11` (20px): at 3-up with real titles rather than the design's short dummy text,
32px was unreadable. Card rhythm is `mt-2`, matching the featured block. The `dd/mm/yyyy`
date in the frame is a known design error, built as specified pending design review.

`posts_per_page` stays at the site default of 18 (Patrick's call) — no `pre_get_posts` hook
in the shipped code. A temporary 2-per-page filter was used locally to exercise pagination.

**Vanity-slug pagination — #607, fixed during review.** `handle_internal_rewrites()` had
registered only `^slug/?$`, so `/the-cortado/page/2/` (and the nine existing brands) fell
through to WordPress's generic page rule, resolved to a non-existent `pagename` and 404'd.
The rewrite array now holds bare paths and derives both the page-1 and the
`^<path>/page/([0-9]{1,})/?$` rule from each entry, so every branded path is covered.
Verified locally on `/downstream/page/2/` and `/page/3/` (distinct posts, served in place)
with `/page/99/` still 404ing. The rules are cached in the DB, so the post-deploy permalink
flush is still required before they take effect.

---

### Task 8: Footer row

**Files:**
- Modify: `category-the-cortado.php`

- [ ] **Step 1: Implement**

A row with `get_template_part( 'partials/pagination' )` on the left and the "Discover all our newsletters" link on the right. Lift that link's markup from `partials/email-signup.php`:

```php
<a href="<?php echo site_url( 'newsletters/' ); ?>" class="ui-hover"><span class="ui-dot ui-dot--red"></span>Discover all our newsletters</a>
```

Check the dot colour against the design — the Figma frame may not use red on this page.

- [ ] **Step 2: Present for approval, then lint and commit**

```bash
phpcs --standard=phpcs.xml category-the-cortado.php
git add category-the-cortado.php
git commit -m "feat: add pagination and newsletters link to The Cortado archive"
```

**As built (2026-09-16), approved:** two grid items rather than `layout-split-level` — with
`space-between` and a single child the link falls left, and the pagination partial renders
nothing when there is no next or previous page. The newsletters link uses `ui-action-link`
(gradient underline, clears on hover); `lib/functions-filters.php` already applies the same
class to next/prev links, so the row is consistent without further work. The red `ui-dot`
that `email-signup.php` puts before this link was dropped — the frame shows plain text.

`partials/pagination.php` separated Newer and Older with a bare space, which reads as one
run-on word; changed to a spaced em dash in its own commit, as it affects every paginated
archive.

---

### Task 9: Changelog and PR

- [ ] **Step 1: Update the changelog**

Invoke the `changelog` skill. One line per feature, no implementation detail.

- [ ] **Step 2: Confirm the build is clean**

```bash
npm run build
git status --short
```

Only commit `dist/` if source files actually changed.

- [ ] **Step 3: Open the PR**

Target `development`. Link the Notion card, the spec, and issue #606 as related-but-out-of-scope. **Do not merge** — the user merges.

---

## Follow-ups

**Revised (2026-09-23):** the front page takes the off-white `--color-gray-base` background
(`src/styl/pages/front-page.styl`, the DYOR archive pattern) on this branch, from Pietro's
2026-09-22 list. Two reasons to do it here rather than wait for the section-joins work: it is
the site's direction of travel, and against off-white the Cortado block could sit in a white
box instead of the full ochre, which is the option under evaluation. Playwright coverage for
the archive, the newsletter 301 and the front-page block landed as `tests/e2e/the-cortado.spec.js`.

**Decided (2026-09-23):** white box. Pietro's Figma revision (Newsletters file, node
`5268:11231`) puts the front-page block in a `background-white` box with an ochre wordmark;
the Latest Cortado sits directly on the box rather than in an inner card, and the Past
Cortados are ruled rows again. The `boxed` arg on `archive-post-no-thumbnail.php`, added for
the ochre version, had no other consumer and is removed.
Latest Cortado headlines on the block and the archive scale with title length on the same
cadence as the front page's primary above-the-fold slot, via the shared
`nm_get_lead_headline_size_classes()` (lib/functions-custom.php). Stopgap until the design
has a mobile pass — the Figma is desktop-only.
The date is dropped from `archive-post-no-thumbnail.php` cards (block and archive) — not
shown on small post cards elsewhere on the site.


- Add a note to the If I Speak thumbnail-less card that `partials/post-layouts/archive-post-no-thumbnail.php` already exists and should be consumed rather than rebuilt. The `notion-novara` MCP server was unreachable when this plan was written.
- **Playwright spec for the Cortado archive**, once `feature/playwright-phase-1` lands on `development`. Model it on `tests/e2e/novara-live-archive.spec.js` from that branch and use the existing helpers (`gotoFresh`, `verifyCriticalPageStructure`, `checkImages`, `testResponsive`). Cover: the canonical URL renders, `cortado-hero` visible, signup form present with the "Get The Cortado" button, `cortado-latest` present on page 1, at least one `archive-post-no-thumbnail` inside `cortado-past-issues`, the newsletters link present, and both routing behaviours from Task 1.
- **Add the Cortado ochre (`#B37400`, Figma token "Standard/Novara -3") to the shared palette** — nm-stylus-library colours plus `background-`/`font-color-` utilities — rather than leaving it scoped in the template. Check the other Cortado design views for further tints first so the whole ramp lands together. Patrick will link the views; next phase, not this archive page.
- Issue #606 — the `/committed` rewrite/redirect conflict. Out of scope here.
