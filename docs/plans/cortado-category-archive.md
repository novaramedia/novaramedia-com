# The Cortado — category archive page

Notion task: "Website category archive page" (Digital Tasks).
Figma: <https://www.figma.com/design/RRZF4PFZPuSsgfzafuOhlq/Newsletters?node-id=5172-2472>
(the task card's journal links an older node, `4505-37`).
Branch: `feature/cortado-rebrand` — the branch for the wider Cortado rebrand, not this task alone.

## What this is

The Cortado is a Novara newsletter that does not yet have a home on the website.
This adds one: a branded category archive listing its editions, with signup, at a
canonical URL, plus the routing that points the existing newsletter record at it.

## Decisions taken

| Decision | Outcome |
|---|---|
| Canonical URL | The category archive, not the newsletter CPT single |
| Redirect scope | Cortado only, via a generic mechanism |
| Content model | Each edition is a normal post in the `the-cortado` category |
| Mechanism | Config array in `lib/functions-rewrites.php`, not post meta |
| Vanity slug | Yes — `/the-cortado/`, matching the nine existing brands |
| Thumbnail-less card | Shared partial, built here, reused by the If I Speak card |

Name is "The Cortado" throughout — the Figma wordmark confirms it.

## Routing

Three URLs, three mechanisms:

| URL | Mechanism | Result |
|---|---|---|
| `/category/articles/the-cortado/` | WP core | Canonical. Renders `category-the-cortado.php` |
| `/the-cortado/` | `add_rewrite_rule` in `handle_internal_rewrites()` | Serves the archive, URL preserved |
| `/newsletters/the-cortado/` | `wp_safe_redirect` 301 | Redirects to canonical |

The vanity slug goes in the existing `$internal_rewrites` array. The 301 needs a new
third section in `lib/functions-rewrites.php`, alongside the external-redirect and
internal-rewrite blocks:

```php
/** NEWSLETTER → CATEGORY REDIRECTS
 * -------------------------------------------------------------
 */
// Newsletter CPT permalinks that should 301 to a category archive.
// Format: 'newsletter-slug' => 'category-slug'
$nm_newsletter_category_redirects = array(
  'the-cortado' => 'the-cortado',
);
```

Hooked on `template_redirect`, guarded by `is_singular( 'newsletter' )`, resolving the
target with `get_category_by_slug()` and `get_term_link()`, bailing on `is_wp_error`.
A generic loop with one entry populated — other newsletters are unaffected until
someone adds them.

Why config rather than post meta: `functions-rewrites.php` is where this class of
mapping already lives, and both bespoke redirects in `functions-custom.php` carry a
`TODO: REMOVE THIS AND ADD TO REWRITES.PHP CONFIG` note from the team. A config array
is version-controlled and greppable; post meta is invisible DB state.

## Page structure

The oversized wordmark and inline `<style>` follow `category-if-i-speak.php`, but the hero
uses the **front-page box pattern**, not a full-bleed band — see
`docs/architecture/boxed-sections.md`. Assets live in `src/img/products/the-cortado/`.

1. **Hero** — ochre box inside the container (`ui-rounded-box`), `NEWSLETTER` eyebrow,
   oversized "THE CORTADO" wordmark inlined as SVG, presenters flush to the box's bottom
   edge via `ui-backgrounded-box-padding--flush-bottom`
2. **Signup band** — strapline left, Mailchimp form right, white background
3. **`LATEST CORTADO`** — featured post, image left, headline / byline / standfirst right
4. **`PAST ISSUES`** — three-column grid of thumbnail-less cards
5. **Footer row** — "Older" pagination left, "Discover all our newsletters" right

## Components

### Reused unchanged

- `partials/email-signup.php` with `background-color: white` and `hide-discover: true`
- `render_mailchimp_signup_form()` — first name, email and the Privacy Policy checkbox
  already match the design, including the `ui-input--border-gray` variant that applies
  on a white background
- `partials/pagination` for "Older"
- The featured-first-post-on-page-one pattern from `category-downstream.php`

The "Discover all our newsletters" link lives inside `email-signup.php`, but the design
puts it in the page footer row. Suppress it via `hide-discover` and lift the one-line
markup into the footer row.

### Changed

- `render_mailchimp_signup_form()` gains an optional `$button_label` parameter,
  defaulting to the current `Sign up`, so the button can read "Get The Cortado".
  Backwards compatible — no existing call site changes.

### New

- **`partials/post-layouts/archive-post-no-thumbnail.php`** — author avatar, headline,
  byline, date, excerpt. Built as a general component taking `grid-item-classes` and an
  optional `hide-excerpt`, matching the conventions of the sibling layouts.

  `archive-post.php` renders a thumbnail in all three of its branches and `list-post.php`
  is date plus title only, so neither can be adapted without changing existing pages.

  **This is also what the "Build a thumbnail-less post display format and apply it on
  the If I Speak archive page" card needs.** Built here because Cortado is the first
  real use, so the design pressure is concrete. That card should consume this partial
  rather than build a second one. *The Notion MCP server was unreachable when this was
  written, so the note has not been added to that card — do it when the server is back.*

- **`category-the-cortado.php`** — hero markup, inline `<style>` for the brand
  treatment, and assembly of the above.

- **Hero asset** — cut-out presenter photo. Source raster into
  `src/img/products/the-cortado/`, build generates the avif/webp variants into
  `dist/img/products/the-cortado/`.

## Prerequisites

Both block the work and neither can be done from the repo.

1. **The `the-cortado` category must exist** on local and on production. Its parent is an
   open editorial question — inside Opinion, or beside it under Articles? The two give
   different canonical URLs; see `docs/post-deploy-checklist.md` v4.9.0 step 1. `handle_internal_rewrites()` calls `get_category_by_slug()` and silently
   skips when the term is missing, so the vanity slug fails quietly rather than loudly.
2. **The Cortado newsletter CPT record must exist and carry `_nm_mailchimp_key`.**
   `email-signup.php` returns early without it, so the signup band renders nothing.

## Post-deploy

Rewrite rules need flushing after release. Add to `docs/post-deploy-checklist.md`, which
already covers this class of manual step.

## Imagery

**All treated imagery is artworked, not generated in CSS.** The halftone/duotone
treatment on the featured image, and the cut-out presenter photo in the hero, are
supplied as finished assets. Templates render what is uploaded and apply no colour
treatment of their own.

Consequence for editorial: a Cortado post's featured image has to be artworked before
upload, or the archive will not look like the design. This is a workflow dependency,
not something the template can enforce or fall back from.

## Open

- **The category's parent — editorial decision, blocks launch.** Inside Opinion, or beside
  it under Articles? Both work technically, since the URL derives from the term, but they
  produce different canonical URLs, so it must be settled before the term is created rather
  than moved afterwards and stranding links. Mirrored in Prerequisites above and in
  `docs/post-deploy-checklist.md` v4.9.0 step 1. Patrick cannot answer this; editorial must.

Settled during the work, recorded so they are not reopened:

- "PAST ISSUES" follows the site default of 18 per page rather than the Figma frame's 12 —
  Patrick's call, recorded in the implementation plan. No `pre_get_posts` hook ships.

## Out of scope

`/committed` has a rewrite and a 301 registered for the same path, and the 301 wins,
making the rewrite entry dead. Raised as issue #606 rather than fixed here — changing
live redirect behaviour for another brand does not belong in this PR.
