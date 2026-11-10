# Front Page Block Joins Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Every front-page section declares whether it is *contained* (boxed) or *open*, and the gap between any two neighbouring sections is rendered from that pair — tight between two boxes, wider between a box and an open section, and a grey rule between two open sections — instead of each section carrying its own top and bottom margins.

**Architecture:** The block registry in `lib/theme-options/options-front-page.php` gains a `frame` attribute per block. A new `lib/front-page-sequence.php` holds the pure join logic (`frame` pair → join kind → utility classes) and a small `NM_Front_Page_Sequence` class that `front-page.php` drives: it buffers each section, skips any that render nothing, and emits the right join before each rendered section. All section partials lose their outer vertical margins; the Downstream block's hardcoded `<hr>` goes. Spacing is expressed entirely with nm-stylus-library utility classes, so there is no Stylus change and no `dist/` commit.

**Tech Stack:** PHP 8 (theme already uses `str_starts_with`), WordPress `get_template_part` + output buffering, nm-stylus-library spacing utilities (`pt-4` = 1rem, `pt-5` = 2rem, `pt-6` = 3rem) and its `hr` rule, Cypress (`cypress/e2e/homepage.cy.js`).

**Spec:** the *Design* section of this document. There is no separate spec file.

## Global Constraints

- Production-quality code only; WordPress coding standards per `phpcs.xml` (two-space indent in templates, `esc_*` on output).
- No changes to the build system. No `dist/` commit — this plan introduces no Stylus/JS change. If tuning later needs a responsive value no utility provides, that is a separate, flagged decision.
- Use nm-stylus-library utilities for spacing; no custom CSS that duplicates them (Copilot review rule).
- Feature branch off `development`, one PR, phases as commits on the same branch. PR title `Feature: Front page section joins`.
- `CHANGELOG.md` entry under `[Unreleased]`, terse (feature + one user-facing fact).
- Do **not** touch `partials/front-page/show-blocks/the-cortado.php`, `src/styl/layouts/front-page-product-blocks.styl`, `category-the-cortado.php`, `docs/architecture/inline-svg.md`, `dist/main.css` or the Cortado plan docs until `feature/cortado-revisions` has merged — that branch changes them (see *Coordination*).

---

## Design

### Problem

Every front-page section currently sets its own outer margins, and they disagree: product boxes use `mt-4 mb-4`, ACFM uses `mt-4 mb-3`, Highlight and Downstream use `mt-5 mb-5`, Above the Fold uses `mt-2 mb-6 mb-s-5`, the Mega Block uses `mt-6 mb-5`, full-bleed banners use internal `pt-6 pb-6`, and Downstream hardcodes an `<hr>` at its foot regardless of what follows it. Because the Layout editor lets editors reorder sections freely, no partial can know what it sits next to, so the seams between sections are inconsistent and the Downstream rule appears even when a boxed section follows it.

### Concepts

**Frame.** Each section is one of:

| Frame | Meaning | Visual cue |
|---|---|---|
| `contained` | The section has a visible edge of its own | coloured rounded box (`background-* ui-rounded-box`), or a full-bleed coloured band |
| `open` | Content sits directly on the page background | Above the Fold, Highlight, Downstream, Mega Block |

Full-bleed banners count as `contained` because they have a hard visible edge; the join only needs to know whether a visible edge exists. If a design later wants bands to touch their neighbours edge-to-edge, add a third frame then — not now.

**Join.** The gap between two consecutive *rendered* sections, chosen from the two frames:

| Previous | Next | Join kind | Rendered |
|---|---|---|---|
| contained | contained | `tight` | `<div class="pt-4">` — 1rem, matches the gutter |
| contained | open (either order) | `loose` | `<div class="pt-5">` — 2rem |
| open | open | `divided` | `<div class="container pt-5 pb-5"><hr></div>` — 2rem, 1px `--color-gray-light` rule at container width, 2rem |

The join element uses **padding**, never margin, so it cannot collapse with anything. The `hr` needs no styles: nm-stylus-library already renders `hr` as a 1px `--color-gray-light` line with zero margin (`modules/ui.styl`).

### Rules

1. **Sections carry no outer vertical margin.** The root element of every section partial has no `mt-*`/`mb-*` (or responsive variants). The one exception is the permanent tail: the Mega Block keeps `mb-5` as the page's lead-out to the footer. Lead-in needs nothing — the products bar / submenu already end with `mb-4`.
2. **The sequence owns the gaps.** Only `NM_Front_Page_Sequence` emits joins; partials never emit dividers or inter-section spacing.
3. **Empty output means skipped.** A section that renders nothing (highlight disabled, DYOR with no posts, a newsletter without a Mailchimp key, a banner whose term is missing…) produces no join either. This is why each section is output-buffered. Corollary: **every partial must return before emitting any markup when it has nothing to show** — an empty `<section>` shell counts as output and would earn a join.
4. **Sections end flush with their last content.** A trailing `mb-*` on a last-row item inside a section adds to the section's height (`.grid-row` is `display: flex`, so item margins do not collapse away) and would widen that seam. Row gaps go *above* items (`mt-*`), or use stack-only variants (`mb-s-4`, `mb-l-4`) where the gap is only needed when columns stack.
5. **Bookends are in the sequence.** Above the Fold (`open`) is the first item and the Mega Block (`open`) the last; the editable layout sits between them. The submenu and products bar are navigation chrome with their own border-bottom and stay outside the sequence.
6. **One place to tune.** All three values live in `nm_get_front_page_join_classes()`. Change a class string there and every seam on the page follows.

### Frame assignment

| Slug | Frame | Why |
|---|---|---|
| `highlight-block` | open | layout-grid on white |
| `novara-live` | contained | `background-black` box |
| `dyor`, `dyor-alt` | contained | `background-cover-image` / white boxes |
| `audio` | contained | two coloured boxes side by side |
| `audio-acfm` | contained | `background-light-blue` box |
| `downstream` | open | thumbnails on white |
| `the-cortado` | contained | `background-ochre` box |
| `banner-support-section` | contained | `render_support_form()` draws `background-red ui-rounded-box` |
| `banner-support-video` | open | embed on white |
| `banner-podcast-death-in-westminster`, `banner-podcast-committed`, `banner-survey-link` | contained | rounded coloured box inside `container` |
| `banner-podcast-if-i-speak`, `banner-focus-pro-rev-soccer`, `banner-podcast-foreign-agent`, `banner-focus-doing-it-right-sex-on-the-left`, `banner-focus-breaking-britain`, `banner-focus-disability-its-political`, `banner-podcast-planet-b` | contained | full-bleed coloured band |
| `newsletter-signup-<id>` (dynamic) | contained | the front-page render path forces the signup's box on, white included (Task 2) |
| Above the Fold, Mega Block (bookends) | open | hardcoded in `front-page.php` |

### Markup produced

```html
<div class="front-page-section" data-testid="front-page-section" data-frame="open">
  <section class="front-page__above-the-fold container container--padded" data-testid="post-list">…</section>
</div>
<div class="front-page-join front-page-join--loose pt-5" data-testid="front-page-join" data-join="loose"></div>
<div class="front-page-section" data-testid="front-page-section" data-frame="contained">
  <section class="container">…dyor…</section>
</div>
<div class="front-page-join front-page-join--tight pt-4" data-testid="front-page-join" data-join="tight"></div>
<div class="front-page-section" data-testid="front-page-section" data-frame="contained">
  <section class="front-page-novara-live container">…</section>
</div>
<div class="front-page-join front-page-join--divided container pt-5 pb-5" data-testid="front-page-join" data-join="divided"><hr></div>
```

The `front-page-section` wrapper exists so tests can read `data-frame` on each neighbour and check the join between them; it has no styles.

### Decisions taken (and the alternatives)

- **PHP computes joins, not CSS sibling selectors.** `.a + .b::before` could draw the seams, but it cannot know a section rendered nothing, and it would hide layout logic in a stylesheet. The buffered PHP sequence is explicit and testable.
- **Utility classes, not a `front-page-joins.styl`.** The three joins are expressible with `pt-4`, `pt-5`, `container pt-5 pb-5` and the library `hr`. No Stylus means no `dist/` commit and no merge conflict with the in-flight Cortado branch. Responsive variants (`pt-s-3` etc.) are also utilities if tuning wants them.
- **A small class for sequence state** rather than a static variable in a function. `lib/theme-options/theme-options.php` already has `IGV_Admin`, so a class in `lib/` has precedent.
- **Newsletter signups are always contained on the front page.** `partials/email-signup.php` skips its box when the banner background is white; the front-page render path (`nm_render_newsletter_signup()`) passes an opt-in `force-box` arg so the front page always gets the box, white included. The partial's default is untouched, so the seven other templates that call it render exactly as before. The site is moving to an off-white page background (`--color-gray-base`, as on the DYOR archive), against which a white box still reads as a box, so a dynamic frame would only have encoded a distinction that is about to disappear.
- **`email-signup` keeps `mt-4 mb-4` by default.** Seven other templates call it; only the front-page path passes `container-classes => ''` (and `force-box => true`, see the bullet above).
- **Same values at every breakpoint to start.** Tuning on staging decides whether small screens want `pt-s-*` variants.

### Out of scope

- Per-row frame overrides in the Layout editor. The frame is a property of the block, not the placement.
- Category archive templates (`category-downstream.php` has its own `<hr>`; untouched).
- Splitting `lib/theme-options/options-front-page.php` (38K, admin + render mixed). The new render code goes in its own file instead of growing it further.
- The site-wide off-white page background (`--color-gray-base`, already used by `src/styl/pages/dyor-archive.styl`). Direction of travel, deliberately not in this PR: it would clash with sections not yet adapted.

---

## Coordination with `feature/cortado-revisions`

That branch (local commit `6ba51626`, not yet in a PR when this plan was written) changes `category-the-cortado.php`, `dist/main.css`, `docs/architecture/inline-svg.md`, `docs/plans/cortado-category-archive*.md`, `partials/front-page/show-blocks/the-cortado.php` and `src/styl/layouts/front-page-product-blocks.styl`.

- Tasks 1–5 and 7–8 touch none of those files. Branch `feature/front-page-joins` off `development` whenever convenient.
- Task 6 (Cortado block margins) waits until `feature/cortado-revisions` has merged; rebase onto `development` first. **Do not merge this PR before Task 6 is done** — until then the Cortado block keeps `mt-4 mb-4` and shows a double gap.
- `CHANGELOG.md` will conflict trivially under `[Unreleased]`; resolve by keeping both entries.

---

## File Structure

| File | Responsibility | Change |
|---|---|---|
| `lib/theme-options/options-front-page.php` | block registry (single source of truth for slug → type, label, partial) | add `frame` to every entry and to the static banners; docblocks |
| `lib/front-page-sequence.php` **(new)** | frame lookup, join kind, join classes, join renderer, `NM_Front_Page_Sequence` | create |
| `functions.php` | loads `lib/*` | one `get_template_part( 'lib/front-page-sequence' )` line |
| `lib/renderers.php` | newsletter signup render path (front page only) | pass `container-classes => ''` and `force-box => true` |
| `partials/email-signup.php` | shared signup partial (8 callers) | honour two new opt-in args, `container-classes` (default `mt-4 mb-4`) and `force-box` (default off); default output unchanged |
| `front-page.php` | page composition | drive `NM_Front_Page_Sequence` for bookends + layout |
| `partials/front-page/above-the-fold.php`, `mega-block.php`, `highlight-block.php`, `show-blocks/*.php`, `partials/specials/banners/support-video.php`, `partials/front-page/above-the-fold/featured-posts-block.php`, `latest-article.php` | section partials | strip outer margins; early-return before markup; fix trailing margins; remove Downstream `<hr>` |
| `cypress/e2e/homepage.cy.js` | homepage smoke tests | add join-structure assertions |
| `docs/architecture/front-page-joins.md` **(new)**, `docs/architecture/boxed-sections.md`, `CHANGELOG.md` | docs | describe the system; fix the now-wrong "section owns mt-4 mb-4" rule |

Line numbers below are as of `development` at `751574c5`; re-check with `grep -n` before editing.

---

### Task 1: Declare a frame on every registry block

**Files:**
- Modify: `lib/theme-options/options-front-page.php:99-121` (static banners), `:150-224` (registry + docblock)

**Interfaces:**
- Produces: every element of `nm_get_front_page_block_registry()` and `nm_get_front_page_static_banners()` has `'frame' => 'contained'|'open'`. Task 3 reads `$registry[ $slug ]['frame']`.

- [ ] **Step 1: Add `frame` to each static banner**

Replace the array body of `nm_get_front_page_static_banners()` with:

```php
  return array(
    'banner-support-section'        => array( 'label' => 'Support section', 'frame' => 'contained', 'partial' => 'partials/support-section' ),
    'banner-support-video'          => array( 'label' => 'Support Video', 'frame' => 'open', 'partial' => 'partials/specials/banners/support-video' ),
    'banner-podcast-death-in-westminster' => array( 'label' => 'Podcast: Death in Westminster', 'frame' => 'contained', 'partial' => 'partials/specials/banners/podcast-death-in-westminster' ),
    'banner-podcast-committed'      => array( 'label' => 'Podcast: Committed', 'frame' => 'contained', 'partial' => 'partials/specials/banners/podcast-committed' ),
    'banner-podcast-if-i-speak'     => array( 'label' => 'Podcast: If I Speak', 'frame' => 'contained', 'partial' => 'partials/specials/banners/podcast-if-i-speak' ),
    'banner-focus-pro-rev-soccer'   => array( 'label' => 'Focus: Pro Rev Soccer', 'frame' => 'contained', 'partial' => 'partials/specials/banners/focus-pro-rev-soccer' ),
    'banner-podcast-foreign-agent'  => array( 'label' => 'Podcast: Foreign Agent', 'frame' => 'contained', 'partial' => 'partials/specials/banners/podcast-foreign-agent' ),
    'banner-focus-doing-it-right-sex-on-the-left' => array( 'label' => 'Focus: Doing It Right: Sex On The Left', 'frame' => 'contained', 'partial' => 'partials/specials/banners/focus-doing-it-right-sex-on-the-left' ),
    'banner-focus-breaking-britain' => array( 'label' => 'Focus: Breaking Britain', 'frame' => 'contained', 'partial' => 'partials/specials/banners/focus-breaking-britain' ),
    'banner-focus-disability-its-political' => array( 'label' => 'Focus: Disability: It’s Political', 'frame' => 'contained', 'partial' => 'partials/specials/banners/focus-disability-its-political' ),
    'banner-podcast-planet-b'       => array( 'label' => 'Podcast: Planet B', 'frame' => 'contained', 'partial' => 'partials/specials/banners/podcast-planet-b' ),
    'banner-survey-link'            => array( 'label' => 'Audience Survey 2026', 'frame' => 'contained', 'partial' => 'partials/specials/banners/survey-link' ),
  );
```

Update its docblock `@return` to `array<string, array{label:string, frame:string, partial:string}>` and add one line: `frame is 'contained' (visible edge: box or full-bleed band) or 'open' (content on the page background); see lib/front-page-sequence.php.`

- [ ] **Step 2: Add `frame` to each product block and copy it for banners**

In `nm_get_front_page_block_registry()` replace the `$blocks = array( … )` literal with:

```php
  $blocks = array(
    'highlight-block' => array( 'type' => 'product', 'frame' => 'open',      'label' => 'Highlight section (configured on its own subpage)', 'partial' => 'partials/front-page/highlight-block' ),
    'novara-live'     => array( 'type' => 'product', 'frame' => 'contained', 'label' => 'Product: Novara Live', 'partial' => 'partials/front-page/show-blocks/novara-live' ),
    'dyor'            => array( 'type' => 'product', 'frame' => 'contained', 'label' => 'Product: Do Your Own Research', 'partial' => 'partials/front-page/show-blocks/dyor' ),
    'dyor-alt'        => array( 'type' => 'product', 'frame' => 'contained', 'label' => 'Product: Do Your Own Research (ALT — design comparison)', 'partial' => 'partials/front-page/show-blocks/dyor-alt' ),
    'audio'           => array( 'type' => 'product', 'frame' => 'contained', 'label' => 'Product: Audio (Novara FM + ACFM)', 'partial' => 'partials/front-page/show-blocks/audio' ),
    'audio-acfm'      => array( 'type' => 'product', 'frame' => 'contained', 'label' => 'Product: ACFM (standalone)', 'partial' => 'partials/front-page/show-blocks/audio-acfm' ),
    'downstream'      => array( 'type' => 'product', 'frame' => 'open',      'label' => 'Product: Downstream', 'partial' => 'partials/front-page/show-blocks/downstream' ),
    'the-cortado'     => array( 'type' => 'product', 'frame' => 'contained', 'label' => 'Product: The Cortado', 'partial' => 'partials/front-page/show-blocks/the-cortado' ),
  );

  foreach ( nm_get_front_page_static_banners() as $slug => $banner ) {
    $blocks[ $slug ] = array(
      'type'    => 'banner',
      'frame'   => $banner['frame'],
      'label'   => 'Banner: ' . $banner['label'],
      'partial' => $banner['partial'],
    );
  }
```

- [ ] **Step 3: Update the registry docblock**

Change `Each entry is [ type, label, partial ]:` to `Each entry is [ type, frame, label, partial ]:` and add a bullet after the `type 'banner'` one:

```
 *   - frame 'contained' | 'open' — whether the section has a visible edge of its
 *     own (coloured box or full-bleed band) or sits open on the page background.
 *     Read by the front-page sequence to choose the join between neighbours
 *     (see lib/front-page-sequence.php). Required on every entry.
```

Change the `@return` to `array<string, array{type:string, frame:string, label:string, partial:string}>`.

- [ ] **Step 4: Lint and confirm nothing rendered changed**

Run: `php -l lib/theme-options/options-front-page.php`
Expected: `No syntax errors detected`

Load the local front page and the admin *Front Page → Layout* screen; both are unchanged (the new key is not read yet).

- [ ] **Step 5: Commit**

```bash
git add lib/theme-options/options-front-page.php
git commit -m "feat: declare a frame (contained/open) on every front-page block"
```

---

### Task 2: The front page forces the newsletter signup's box and suppresses its outer margins

**Files:**
- Modify: `lib/renderers.php:565-584` (`nm_render_newsletter_signup`)
- Modify: `partials/email-signup.php:56-80`, `:105-112`

**Interfaces:**
- Produces: `partials/email-signup` honours two new optional args, following its existing hyphenated arg convention (`background-color`, `hide-image`, …):
  - `'container-classes'` (string, default `'mt-4 mb-4'`) — classes on the root `div.email-signup`.
  - `'force-box'` (bool, default `false`) — draw the `background-{colour} font-color-{colour} ui-rounded-box ui-backgrounded-box-padding` box even when the background is white.
- With neither arg passed the partial's output is byte-identical to today. Only `nm_render_newsletter_signup()` (front page) passes them. Task 3 relies on every front-page newsletter signup being `contained`.

- [ ] **Step 1: Pass the two args from the front-page path**

In `nm_render_newsletter_signup()` (`lib/renderers.php`) change the render line to:

```php
    // Front page only. The sequence owns inter-section spacing, so the signup's
    // outer margins are suppressed; and every front-page section must have a
    // frame, so the box is forced on even for a white background (the block
    // registry treats newsletter signups as contained). Other templates call
    // the partial without these args and are unaffected.
    get_template_part(
      'partials/email-signup',
      null,
      array(
        'newsletter_post_id' => $newsletter->ID,
        'container-classes'  => '',
        'force-box'          => true,
      )
    );
```

- [ ] **Step 2: Read the args in `partials/email-signup.php`**

Directly below the `hide-image` block (before the closing `?>` at line 69) add, matching the surrounding override pattern:

```php
// Outer vertical spacing. Every caller keeps the historic mt-4 mb-4 unless it
// passes its own (the front-page sequence passes '' because it renders the gap
// between sections itself).
$container_classes = 'mt-4 mb-4';

if ( isset( $args['container-classes'] ) && is_string( $args['container-classes'] ) ) {
  $container_classes = $args['container-classes'];
}

// The box is skipped for a white background unless the caller forces it on
// (the front page does, so every signup there is a contained section).
$show_box = $background_color !== 'white';

if ( ! empty( $args['force-box'] ) ) {
  $show_box = true;
}
```

- [ ] **Step 3: Use them in the markup**

Line 70, before / after:

```php
<div class="email-signup mt-4 mb-4">
```
```php
<div class="<?php echo esc_attr( trim( 'email-signup ' . $container_classes ) ); ?>">
```

Line 74, the opening condition, before / after:

```php
      if ( $background_color !== 'white' ) { // if the background color is not white, wrap in a box
```
```php
      if ( $show_box ) { // wrap in a box: non-white background, or forced by the caller
```

Line 107, the closing condition, before / after:

```php
        if ( $background_color !== 'white' ) { // close the box divs if we opened them
```
```php
        if ( $show_box ) { // close the box divs if we opened them
```

Nothing else in the partial changes; `render_mailchimp_signup_form()` still receives `$background_color`.

- [ ] **Step 4: Verify the seven other callers are byte-identical**

Run: `php -l lib/renderers.php && php -l partials/email-signup.php`
Expected: `No syntax errors detected` twice.

Load `/newsletters/` and one of `/the-cortado/`, `/downstream/`, `/acfm/` locally and diff the signup markup against `development` (DevTools → copy outerHTML, or `curl -s <url> | grep -A40 'email-signup'`). Expected: identical, including `class="email-signup mt-4 mb-4"` and no box for a white background. On the local front page, put a white-background newsletter in the Layout and confirm it renders inside the box with no outer margins.

- [ ] **Step 5: Commit**

```bash
git add lib/renderers.php partials/email-signup.php
git commit -m "feat: front page forces the newsletter signup box and owns its spacing"
```

---

### Task 3: The sequence renderer

**Files:**
- Create: `lib/front-page-sequence.php`
- Modify: `functions.php:153` (add a load line after `options-front-page`)

**Interfaces:**
- Consumes: `nm_get_front_page_block_registry()` entries with `frame` (Task 1); the front-page path forces the newsletter signup box (Task 2).
- Produces:
  - `nm_get_front_page_block_frame( string $slug ): 'contained'|'open'`
  - `nm_get_front_page_join_kind( string $previous_frame, string $next_frame ): 'tight'|'loose'|'divided'`
  - `nm_get_front_page_join_classes( string $kind ): string`
  - `nm_render_front_page_join( string $kind ): void`
  - `final class NM_Front_Page_Sequence { public function render( string $frame, callable $render ): void }`

- [ ] **Step 1: Create `lib/front-page-sequence.php`**

```php
<?php
/**
 * Front-page sequence: the spacing between consecutive front-page sections.
 *
 * Every section declares a frame in the block registry (or, for the two
 * hardcoded bookends, in front-page.php):
 *   - 'contained' — has a visible edge of its own: a coloured rounded box or a
 *                   full-bleed coloured band.
 *   - 'open'      — content sits directly on the page background.
 *
 * Sections carry no outer vertical margin. The gap between two neighbours is a
 * join, chosen from the pair of frames:
 *   contained + contained → 'tight'   (1rem, the gutter)
 *   contained + open      → 'loose'   (2rem)
 *   open + open           → 'divided' (2rem, 1px grey rule at container width, 2rem)
 *
 * NM_Front_Page_Sequence renders sections in order, buffers each one, skips any
 * that produce no output (so a hidden section earns no gap), and emits the join
 * before each rendered section after the first. Joins use padding, never
 * margin, so nothing collapses. Tune the values in
 * nm_get_front_page_join_classes() — it is the only place they live.
 *
 * See docs/architecture/front-page-joins.md.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Resolves a layout slug to its frame.
 *
 * Static blocks read the registry. Newsletter signups — the only dynamic
 * family — are always contained: nm_render_newsletter_signup() forces the
 * partial's box on for the front page. Unknown slugs render nothing (see
 * nm_render_front_page_block()), so their frame never reaches a join.
 *
 * @param string $slug Layout slug.
 * @return string 'contained' or 'open'.
 */
function nm_get_front_page_block_frame( $slug ) {
  if ( ! is_string( $slug ) || $slug === '' ) {
    return 'contained';
  }

  $registry = nm_get_front_page_block_registry();

  if ( isset( $registry[ $slug ]['frame'] ) ) {
    return $registry[ $slug ]['frame'];
  }

  return 'contained'; // newsletter signups and anything unknown
}

/**
 * Picks the join between two neighbouring frames.
 *
 * @param string $previous_frame Frame of the section above.
 * @param string $next_frame     Frame of the section below.
 * @return string 'tight', 'loose' or 'divided'.
 */
function nm_get_front_page_join_kind( $previous_frame, $next_frame ) {
  if ( $previous_frame === 'open' && $next_frame === 'open' ) {
    return 'divided';
  }

  if ( $previous_frame === 'contained' && $next_frame === 'contained' ) {
    return 'tight';
  }

  return 'loose';
}

/**
 * Utility classes for a join. The single place the front-page rhythm is tuned.
 * Spacing scale: 4 = 1rem, 5 = 2rem, 6 = 3rem. Responsive variants (pt-s-3 …)
 * are also utilities — add them here, not in the partials.
 *
 * @param string $kind 'tight', 'loose' or 'divided'.
 * @return string Space-separated class list.
 */
function nm_get_front_page_join_classes( $kind ) {
  $classes = array(
    'tight'   => 'pt-4',
    'loose'   => 'pt-5',
    'divided' => 'container pt-5 pb-5',
  );

  return isset( $classes[ $kind ] ) ? $classes[ $kind ] : $classes['loose'];
}

/**
 * Echoes a join. 'divided' carries the library-styled <hr> (1px, gray-light).
 *
 * @param string $kind 'tight', 'loose' or 'divided'.
 * @return void
 */
function nm_render_front_page_join( $kind ) {
  echo '<div class="front-page-join front-page-join--' . esc_attr( $kind ) . ' ' . esc_attr( nm_get_front_page_join_classes( $kind ) ) . '" data-testid="front-page-join" data-join="' . esc_attr( $kind ) . '">';

  if ( $kind === 'divided' ) {
    echo '<hr>';
  }

  echo '</div>';
}

/**
 * Renders front-page sections in order with the correct join between each
 * rendered pair. One instance per page render; front-page.php drives it.
 */
final class NM_Front_Page_Sequence {
  /**
   * Frame of the last section that produced output, or null before the first.
   *
   * @var string|null
   */
  private $previous_frame = null;

  /**
   * Renders one section, preceded by its join.
   *
   * The callback is buffered so a section that has nothing to show (and
   * returns before emitting markup) is skipped entirely — no wrapper, no join.
   *
   * @param string   $frame  'contained' or 'open'.
   * @param callable $render Echoes the section (typically a get_template_part() call).
   * @return void
   */
  public function render( $frame, callable $render ) {
    ob_start();
    $render();
    $html = ob_get_clean();

    if ( trim( $html ) === '' ) {
      return;
    }

    if ( $this->previous_frame !== null ) {
      nm_render_front_page_join( nm_get_front_page_join_kind( $this->previous_frame, $frame ) );
    }

    echo '<div class="front-page-section" data-testid="front-page-section" data-frame="' . esc_attr( $frame ) . '">';
    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- buffered template output, escaped at source.
    echo '</div>';

    $this->previous_frame = $frame;
  }
}
```

- [ ] **Step 2: Load it from `functions.php`**

After line 153 (`get_template_part( 'lib/theme-options/options-front-page' );`) add:

```php
get_template_part( 'lib/front-page-sequence' );
```

- [ ] **Step 3: Smoke-test the pure functions outside WordPress**

Run:

```bash
php -l lib/front-page-sequence.php && php -r '
define( "ABSPATH", "/" );
require "lib/front-page-sequence.php";
assert( nm_get_front_page_join_kind( "contained", "contained" ) === "tight" );
assert( nm_get_front_page_join_kind( "contained", "open" ) === "loose" );
assert( nm_get_front_page_join_kind( "open", "contained" ) === "loose" );
assert( nm_get_front_page_join_kind( "open", "open" ) === "divided" );
assert( nm_get_front_page_join_classes( "divided" ) === "container pt-5 pb-5" );
assert( nm_get_front_page_join_classes( "nonsense" ) === "pt-5" );
echo "ok\n";
'
```

Expected: `No syntax errors detected` then `ok`. (Add `-d zend.assertions=1` after `php` if assertions are compiled out locally.)

- [ ] **Step 4: Commit**

```bash
git add lib/front-page-sequence.php functions.php
git commit -m "feat: add the front-page sequence renderer and join logic"
```

---

### Task 4: Drive the front page through the sequence and normalise the bookends

**Files:**
- Modify: `front-page.php`
- Modify: `partials/front-page/above-the-fold.php:16`
- Modify: `partials/front-page/mega-block.php:1`
- Modify: `partials/front-page/above-the-fold/featured-posts-block.php:24`
- Modify: `partials/front-page/above-the-fold/latest-article.php:27`

**Interfaces:**
- Consumes: `NM_Front_Page_Sequence`, `nm_get_front_page_block_frame()` (Task 3).

> Between this task and Task 5 the layout blocks still carry their own margins, so the page shows double gaps. Both tasks land in the same PR; do not deploy in between.

- [ ] **Step 1: Rewrite the body of `front-page.php`**

Replace everything from `// **************` / `// ABOVE THE FOLD` down to and including the `get_template_part('partials/front-page/mega-block');` line with:

```php
    // **************
    // ABOVE THE FOLD
    // **************

    $featured_posts_ids = get_above_the_fold_featured_post_ids();
    $latest_news_posts_ids = get_latest_news_ids($featured_posts_ids);

    // Editable layout: banners + product blocks, ordered in Front Page > Layout.
    // Falls back to the historic order when no layout has been saved. The shared
    // context is passed to every product block; only those that need it use it
    // (e.g. the highlight section dedupes against the above-the-fold posts).
    // Normalise to arrays: get_above_the_fold_featured_post_ids() returns false
    // when empty (low-content / staging), which would make array_merge throw.
    $block_context = array(
      'excluded_posts_ids' => array_merge(
        is_array($featured_posts_ids) ? $featured_posts_ids : array(),
        is_array($latest_news_posts_ids) ? $latest_news_posts_ids : array()
      ),
    );

    // Sections carry no outer vertical margin of their own. The sequence buffers
    // each one, skips any that render nothing, and emits the join between each
    // rendered pair from the two frames (see lib/front-page-sequence.php).
    // Above the Fold and the Mega Block are the permanent open bookends.
    $sequence = new NM_Front_Page_Sequence();

    $sequence->render('open', function () use ($featured_posts_ids, $latest_news_posts_ids) {
      get_template_part('partials/front-page/above-the-fold', null, array(
        'featured_posts_ids' => $featured_posts_ids,
        'latest_news_posts_ids' => $latest_news_posts_ids,
      ));
    });

    foreach (nm_get_front_page_layout() as $block_slug) {
      $sequence->render(nm_get_front_page_block_frame($block_slug), function () use ($block_slug, $block_context) {
        nm_render_front_page_block($block_slug, $block_context);
      });
    }

    $sequence->render('open', function () {
      get_template_part('partials/front-page/mega-block');
    });
```

- [ ] **Step 2: Strip Above the Fold's outer margins**

`partials/front-page/above-the-fold.php:16`, before:

```php
<section class="front-page__above-the-fold container container--padded mt-2 mb-6 mb-s-5" data-testid="post-list">
```

after:

```php
<section class="front-page__above-the-fold container container--padded" data-testid="post-list">
```

- [ ] **Step 3: Strip the Mega Block's top margin; keep `mb-5` as the documented lead-out**

`partials/front-page/mega-block.php:1`, before:

```php
<section id="front-page__mega-block" class="container mt-6 mb-5">
```

after:

```php
<?php // The Mega Block is the permanent tail: its mb-5 is the page's lead-out to the footer. Every other section has no outer margin (see lib/front-page-sequence.php). ?>
<section id="front-page__mega-block" class="container mb-5">
```

- [ ] **Step 4: Fix Above the Fold trailing margins**

`partials/front-page/above-the-fold/featured-posts-block.php:24` — the primary column trails 1rem at xxl in the second featured block, which is the bottom of Above the Fold. Before:

```php
  <div class="featured-posts__primary grid-item is-l-24 is-xxl-16 mb-4">
```

after (gap only when the columns stack):

```php
  <div class="featured-posts__primary grid-item is-l-24 is-xxl-16 mb-l-4">
```

`partials/front-page/above-the-fold/latest-article.php:27` — every item, including the last, has `mb-4 pb-4`. Before:

```php
<div class="mb-4 pb-4 <?php if ($has_bottom_border) {echo 'ui-border-bottom';} ?>">
```

after:

```php
<div class="<?php echo $has_bottom_border ? 'mb-4 pb-4 ui-border-bottom' : ''; ?>">
```

Check `grep -rn "above-the-fold/latest-article'" partials/` first: this partial is only rendered from `latest-articles.php`, which already passes `has_bottom_border => false` for the last item.

- [ ] **Step 5: Verify**

Run: `php -l front-page.php`
Expected: `No syntax errors detected`

Load the local front page. In DevTools confirm: every section is wrapped in `div.front-page-section[data-frame]`; a `div.front-page-join` sits between each pair; no join before Above the Fold and none after the Mega Block; the join `data-join` matches the neighbours' frames. Save the Layout page with the Highlight section immediately after Above the Fold and confirm a `divided` join with an `<hr>` appears between them.

- [ ] **Step 6: Commit**

```bash
git add front-page.php partials/front-page/above-the-fold.php partials/front-page/mega-block.php partials/front-page/above-the-fold/featured-posts-block.php partials/front-page/above-the-fold/latest-article.php
git commit -m "feat: render front-page sections through the join sequence"
```

---

### Task 5: Normalise every layout block (except The Cortado)

**Files:**
- Modify: `partials/front-page/highlight-block.php:63`, `:162`, `:~188`
- Modify: `partials/front-page/show-blocks/novara-live.php:19`
- Modify: `partials/front-page/show-blocks/dyor.php:27`
- Modify: `partials/front-page/show-blocks/dyor-alt.php:43`
- Modify: `partials/front-page/show-blocks/audio.php:12`, `:28`, `:130-149`
- Modify: `partials/front-page/show-blocks/audio-acfm.php:30`
- Modify: `partials/front-page/show-blocks/downstream.php` (whole file shell)
- Modify: `partials/specials/banners/support-video.php:18`, `:20`

**Interfaces:**
- Consumes: nothing new. Each partial must satisfy Rules 1, 3 and 4 from the Design.

- [ ] **Step 1: Highlight block (open)**

Line 63, before / after:

```php
<section class="front-page-highlight-block mt-5 mb-5">
```
```php
<section class="front-page-highlight-block">
```

Line 162 (the last `featured-post-tertiary` call, `$latest_featured_posts_ids[6]`) — the right-hand column's last item trails `mb-4`. Change its arg from `'container_classes' => 'mb-4'` to `'container_classes' => 'mb-s-4'` (gap only when the columns stack). Lines 128, 139, 150 keep `'mb-4'` — they sit above another item.

Line ~188, the `latest_others` list item, before:

```php
            <div class="pb-3 mb-3 <?php echo ( $i < $latest_others_posts_to_show ) ? 'ui-border-bottom' : ''; ?>">
```

after:

```php
            <div class="<?php echo ( $i < $latest_others_posts_to_show ) ? 'pb-3 mb-3 ui-border-bottom' : ''; ?>">
```

- [ ] **Step 2: Novara Live, DYOR, DYOR ALT, ACFM (contained; box padding contains their internals)**

Root lines only:

| File:line | Before | After |
|---|---|---|
| `novara-live.php:19` | `<section class="front-page-novara-live container mt-4 mb-4">` | `<section class="front-page-novara-live container">` |
| `dyor.php:27` | `<section class="container mt-4 mb-4">` | `<section class="container">` |
| `dyor-alt.php:43` | `<section class="container mt-4 mb-4">` | `<section class="container">` |
| `audio-acfm.php:30` | `<section class="front-page__acfm container mt-4 mb-3">` | `<section class="front-page__acfm container">` |

- [ ] **Step 3: Audio (contained) — root, card trailing margin, empty shell**

Root line 130, before / after:

```php
<section class="front-page__audio-products container mt-4 mb-4">
```
```php
<section class="front-page__audio-products container">
```

The two cards are `grid-item is-s-24 is-xxl-12 mb-4` (line 28) *outside* their coloured boxes, so both trail 1rem at xxl. Give `render_show()` a sixth parameter and move the gap to a stack-only class on the first card. Signature (line 12) and docblock:

```php
 * @param string $grid_item_classes Extra classes for the card's grid-item (e.g. a stack-only gap).
 */
if ( ! function_exists( 'render_show' ) ) {
function render_show( $slug, $description, $logo_url = null, $background_color = 'black', $font_color = 'white', $grid_item_classes = '' ) {
```

Line 28, before / after:

```php
    <div class="grid-item is-s-24 is-xxl-12 mb-4 font-color-<?php echo esc_attr( $font_color ); ?> ui-rounded-box">
```
```php
    <div class="<?php echo esc_attr( trim( 'grid-item is-s-24 is-xxl-12 ' . $grid_item_classes ) ); ?> font-color-<?php echo esc_attr( $font_color ); ?> ui-rounded-box">
```

The section shell is emitted even when both shows have nothing (Rule 3). Replace lines 130–149 with:

```php
<?php
// Both cards must be known before the shell is emitted: a section with no
// cards would still earn a join from the front-page sequence.
ob_start();

render_show(
  'novarafm',
  'Novara Media\'s flagship podcast is about the ideas that shape our past, present and future. With a desire to change the world—and ourselves along the way—Novara FM interrogates the people, ideologies and movements that wield power in our lives, from politics and culture to technology and the environment.',
  '/dist/img/products/novara-fm/novarafm-wordmark.svg',
  'green',
  'black',
  'mb-s-4'
);
render_show(
  'acfm',
  'The home of the weird left. Nadia Idle, Jeremy Gilbert and Keir Milburn examine the links between left-wing politics, culture, music and experiences of collective joy.',
  '/dist/img/products/acfm/acfm-logo.svg',
  'light-blue',
  'black'
);

$cards = ob_get_clean();

if ( trim( $cards ) === '' ) {
  return;
}
?>
<section class="front-page__audio-products container">
  <div class="grid-row">
    <?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- buffered template output, escaped at source. ?>
  </div>
</section>
```

- [ ] **Step 4: Downstream (open) — root, drop the hardcoded rule, early return, trailing margin**

Rewrite the shell of `partials/front-page/show-blocks/downstream.php`. Top of file, before:

```php
<?php
$has_border_bottom = true;
$downstream_category = get_term_by( 'slug', 'downstream', 'category' );
?>
<div>
  <section class="container mt-5 mb-5">
    <?php
    if ( $downstream_category ) {
      $category_link = get_category_link( $downstream_category->term_id );
      ?>
```

after:

```php
<?php
$downstream_category = get_term_by( 'slug', 'downstream', 'category' );

if ( ! $downstream_category ) {
  return; // nothing to show: emit no markup so the front-page sequence skips this section
}

$category_link = get_category_link( $downstream_category->term_id );
?>
<section class="container">
```

Remove the `<hr>` block (the join now draws the rule when two open sections meet):

```php
          <?php if ( $has_border_bottom ) { ?>
        <div class="grid-item is-xxl-24 mt-5">
          <hr />
        </div>
      <?php } ?>
```

Remove the matching `}` / `</div>` that closed the old `if ( $downstream_category )` and the outer `<div>` at the bottom so the file ends with `</section>`. Re-indent the body by one level less (two spaces) to match.

Trailing margin in the Recent Episodes column — header line ~88 and item line ~100, before:

```php
          <div class="layout-split-level font-size-8 font-weight-bold mb-4">
…
          <div class="grid-item is-xxl-12 mb-4">
```

after (gap above each item; header loses its bottom margin so the first row keeps 1rem):

```php
          <div class="layout-split-level font-size-8 font-weight-bold">
…
          <div class="grid-item is-xxl-12 mt-4">
```

- [ ] **Step 5: Support Video banner (open)**

`partials/specials/banners/support-video.php:18`, before / after:

```php
<div class="container pt-6 pb-6 pt-s-5 pb-s-5">
```
```php
<div class="container">
```

Line 20, the video column trails 1rem at xxl beside the text column. Before / after:

```php
    <div class="grid-item is-m-24 is-xxl-12 mb-4">
```
```php
    <div class="grid-item is-m-24 is-xxl-12 mb-m-4">
```

- [ ] **Step 6: Verify each block ends flush**

Run: `for f in partials/front-page/highlight-block.php partials/front-page/show-blocks/*.php partials/specials/banners/support-video.php; do php -l "$f"; done`
Expected: `No syntax errors detected` for each.

Confirm no root element of a layout block still carries an outer margin:

```bash
grep -n '^<section class="[^"]*\bm[tb]-' partials/front-page/highlight-block.php partials/front-page/show-blocks/*.php partials/front-page/above-the-fold.php
```

Expected: only `partials/front-page/show-blocks/the-cortado.php` (handled in Task 6).

On the local front page, with a saved Layout containing every block, use DevTools to select each `div.front-page-section` and confirm its bottom edge meets its content (no whitespace inside the wrapper below the last row) at 1440px and at 375px wide. Confirm a Layout that puts Downstream directly after the Highlight section shows exactly one grey rule between them, and that a Layout with Downstream followed by DYOR shows no rule.

- [ ] **Step 7: Commit**

```bash
git add partials/front-page/highlight-block.php partials/front-page/show-blocks/novara-live.php partials/front-page/show-blocks/dyor.php partials/front-page/show-blocks/dyor-alt.php partials/front-page/show-blocks/audio.php partials/front-page/show-blocks/audio-acfm.php partials/front-page/show-blocks/downstream.php partials/specials/banners/support-video.php
git commit -m "refactor: strip outer margins from front-page sections; joins own the gaps"
```

---

### Task 6: The Cortado block (after `feature/cortado-revisions` merges)

**Files:**
- Modify: `partials/front-page/show-blocks/the-cortado.php:59` (line as of `751574c5`; re-check after rebase)

- [ ] **Step 1: Rebase**

```bash
git fetch origin && git rebase origin/development
```

Confirm `git log --oneline origin/development -5` includes the Cortado revisions merge before continuing.

- [ ] **Step 2: Strip the root margins**

Before / after:

```php
<section class="container mt-4 mb-4" data-testid="front-page-cortado">
```
```php
<section class="container" data-testid="front-page-cortado">
```

- [ ] **Step 3: Check trailing margins inside the box**

The ochre box uses `ui-backgrounded-box-padding`, so margins inside it stay inside the box and do not touch the seam. Confirm in DevTools that `div.front-page-section` around the Cortado block ends at the box's bottom edge at 1440px and 375px. If the revised block has moved anything outside the box with a trailing `mb-*`, convert it to `mt-*` on the item below or a stack-only `mb-s-*`, per Rule 4.

- [ ] **Step 4: Verify and commit**

Run: `php -l partials/front-page/show-blocks/the-cortado.php`
Expected: `No syntax errors detected`

```bash
git add partials/front-page/show-blocks/the-cortado.php
git commit -m "refactor: the Cortado block leaves its spacing to the front-page joins"
```

---

### Task 7: Cypress assertions for the join structure

**Files:**
- Modify: `cypress/e2e/homepage.cy.js` (add inside `describe('Homepage', …)`)

**Interfaces:**
- Consumes: `data-testid="front-page-section"` + `data-frame`, `data-testid="front-page-join"` + `data-join` (Task 3).

- [ ] **Step 1: Add the tests**

```js
  it('should place exactly one join between each pair of front-page sections', () => {
    const expectedJoin = (previous, next) => {
      if (previous === 'open' && next === 'open') return 'divided';
      if (previous === 'contained' && next === 'contained') return 'tight';
      return 'loose';
    };

    cy.get('[data-testid="main-content"] [data-testid="front-page-section"]').should('have.length.greaterThan', 1);

    cy.get('[data-testid="main-content"] [data-testid="front-page-join"]').each(($join) => {
      const $previous = $join.prev();
      const $next = $join.next();

      // A join never leads, trails, or touches another join.
      expect($previous.is('[data-testid="front-page-section"]'), 'join follows a section').to.be.true;
      expect($next.is('[data-testid="front-page-section"]'), 'join precedes a section').to.be.true;

      // Its kind is derived from the two neighbours' frames.
      expect($join.attr('data-join')).to.eq(expectedJoin($previous.attr('data-frame'), $next.attr('data-frame')));

      // Only a divided join draws the rule.
      expect($join.find('hr').length).to.eq($join.attr('data-join') === 'divided' ? 1 : 0);
    });
  });

  it('should render no empty front-page section wrappers', () => {
    cy.get('[data-testid="main-content"] [data-testid="front-page-section"]').each(($section) => {
      expect($section.text().trim().length + $section.find('img, svg, iframe').length, 'section has content').to.be.greaterThan(0);
    });
  });
```

- [ ] **Step 2: Run the homepage spec**

Run: `npx cypress run --spec cypress/e2e/homepage.cy.js`
Expected: all Homepage tests pass, including the two new ones. (Needs the local site up per `docs/testing/cypress.md`; the CI workflow runs it against staging on the PR.)

If PR #600 (Playwright migration) has merged into `development` by now, port the same two assertions to the Playwright homepage spec using `page.getByTestId(...)` and `locator.evaluate` for sibling checks instead; the DOM contract is identical.

- [ ] **Step 3: Commit**

```bash
git add cypress/e2e/homepage.cy.js
git commit -m "test: assert front-page joins sit between sections and match their frames"
```

---

### Task 8: Documentation and changelog

**Files:**
- Create: `docs/architecture/front-page-joins.md`
- Modify: `docs/architecture/boxed-sections.md` (pattern snippet + the "Spacing between sections" rule)
- Modify: `CHANGELOG.md` (`[Unreleased]` → `### Changed`)

- [ ] **Step 1: Write `docs/architecture/front-page-joins.md`**

````markdown
# Front-page joins

Front-page sections carry no outer vertical margin. The gap between two
neighbouring sections — the *join* — is rendered by `NM_Front_Page_Sequence`
(`lib/front-page-sequence.php`) from the two sections' *frames*.

## Frames

Declared per block in the registry (`nm_get_front_page_block_registry()`,
`lib/theme-options/options-front-page.php`) and hardcoded for the two bookends
in `front-page.php`.

| Frame | Meaning |
|---|---|
| `contained` | The section has a visible edge of its own: a coloured rounded box, or a full-bleed coloured band |
| `open` | Content sits directly on the page background |

Newsletter signups are always `contained`: `nm_render_newsletter_signup()`
(`lib/renderers.php`) passes `force-box` so the front page always gets the box,
white background included. Other templates that use the partial are unaffected.

## Joins

| Previous | Next | Kind | Classes |
|---|---|---|---|
| contained | contained | `tight` | `pt-4` |
| contained | open (either order) | `loose` | `pt-5` |
| open | open | `divided` | `container pt-5 pb-5` around an `<hr>` |

Tune the values in `nm_get_front_page_join_classes()` only. Joins use padding,
so they never collapse with anything.

## Rules for section partials

1. **No outer `mt-*`/`mb-*` on the root element.** The Mega Block's `mb-5` is
   the single exception: it is the permanent tail and that margin is the lead-out
   to the footer.
2. **Return before emitting markup when there is nothing to show.** The sequence
   buffers each section and skips empty output; an empty `<section>` shell would
   still earn a join.
3. **End flush with the last content.** `.grid-row` is a flex container, so a
   trailing `mb-*` on a last-row item adds to the section's height. Put row gaps
   above items (`mt-*`) or use stack-only variants (`mb-s-4`, `mb-l-4`).
4. **Never draw your own divider.** The `divided` join does that when two open
   sections meet.

## Adding a block

Add the registry entry with a `frame`, follow the rules above, and the sequence
does the rest. The Layout editor needs no change: the frame belongs to the block,
not the placement.

## Markup

```html
<div class="front-page-section" data-testid="front-page-section" data-frame="open">…</div>
<div class="front-page-join front-page-join--divided container pt-5 pb-5" data-testid="front-page-join" data-join="divided"><hr></div>
<div class="front-page-section" data-testid="front-page-section" data-frame="open">…</div>
```

`cypress/e2e/homepage.cy.js` asserts that every join sits between two sections
and that its kind matches their frames.
````

- [ ] **Step 2: Correct `docs/architecture/boxed-sections.md`**

In the pattern snippet change `<section class="container mt-4 mb-4">` to `<section class="container">`. Replace the rule bullet

```
- **The `<section>` is the container.** `container mt-4 mb-4` on the section itself.
```

with

```
- **The `<section>` is the container.** `container` on the section itself, with no
  outer margins: the front-page sequence renders the gap to each neighbour
  (see `front-page-joins.md`).
```

and replace

```
- **Spacing between sections** is the section's own `mt-4 mb-4`, not wrapper divs.
```

with

```
- **Spacing between sections** comes from the front-page join, never from the
  section. A boxed section's internal margins stay inside `ui-backgrounded-box-padding`,
  so they do not affect the seam.
```

- [ ] **Step 3: Changelog**

Under `## [Unreleased]` → `### Changed` add:

```
- Front page spacing between sections is now set by how each pair joins (boxed or open), with a divider only between two open sections; the rule hardcoded into the Downstream block is gone
```

- [ ] **Step 4: Commit**

```bash
git add docs/architecture/front-page-joins.md docs/architecture/boxed-sections.md CHANGELOG.md
git commit -m "docs: describe front-page joins and drop the per-section margin rule"
```

---

### Task 9: Tuning pass on staging

**Files:**
- Modify (only if a value changes): `lib/front-page-sequence.php` → `nm_get_front_page_join_classes()`

- [ ] **Step 1: Build a Layout that exercises every join**

On staging *Front Page → Layout*, save this order: Highlight section (open, after the open Above the Fold → `divided`), DYOR (`loose`), Novara Live (`tight`), Downstream (`loose`), a full-bleed banner e.g. Focus: Breaking Britain (`loose`), Audio (`tight`), a newsletter signup (`tight`), then the Mega Block follows (`loose`). Downstream directly before the Mega Block would give a second `divided`.

- [ ] **Step 2: Judge the three values at 1440px and 375px**

Candidates the design brief allows: `tight` stays `pt-4`; `loose` is `pt-5` (2rem) or `pt-6` (3rem); `divided` is `pt-5 pb-5` or `pt-6 pb-6`. Small screens may want `pt-s-4` on `loose` and `pt-s-4 pb-s-4` on `divided`. Change only the strings in `nm_get_front_page_join_classes()`.

- [ ] **Step 3: Commit if anything changed**

```bash
git add lib/front-page-sequence.php
git commit -m "style: tune front-page join spacing after staging review"
```

Update the classes table in `docs/architecture/front-page-joins.md` in the same commit if a value moved.

---

## Self-review

- **Spec coverage.** Frame attribute → Task 1. Join matrix, padding-not-margin, `hr` from the library, one tuning point → Task 3. Bookends in the sequence, lead-in/lead-out → Task 4. Empty-output rule → Task 3 (buffering) + Task 5 (Downstream, Audio early returns). Flush-ending sections → Tasks 4, 5, 6. Downstream `<hr>` removal → Task 5. Newsletter box forced from the front-page path only, other callers byte-identical → Task 2. Docs correction of `boxed-sections.md` → Task 8. Tests → Task 7. Tuning → Task 9. Cortado coordination → Task 6.
- **Names used consistently:** `frame` ∈ {`contained`, `open`}; join kind ∈ {`tight`, `loose`, `divided`}; `nm_get_front_page_block_frame`, `nm_get_front_page_join_kind`, `nm_get_front_page_join_classes`, `nm_render_front_page_join`, `NM_Front_Page_Sequence::render( $frame, callable $render )`; test ids `front-page-section`/`data-frame`, `front-page-join`/`data-join`.
- **Known judgement calls left to the implementer, each with a defined check:** which Cortado internals (if any) sit outside its box after the revisions branch (Task 6 step 3); whether tuning wants small-screen variants (Task 9).

## After shipping

- `git mv docs/plans/front-page-block-joins.md docs/plans/archive/` and add a row to `docs/plans/archive/README.md` with the release version.
- If the Playwright migration (#600) lands after this, port the two homepage assertions (Task 7) in that PR.
