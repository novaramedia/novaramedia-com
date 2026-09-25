# Inline SVG

Logos, wordmarks and decorative vectors are **inlined into the markup**, not referenced with
`<img src>`. Use the existing helper; do not write another one.

```php
<?php echo nm_get_file( '/dist/img/products/the-cortado/the-cortado-wordmark.svg' ); ?>
```

`nm_get_file()` lives in `lib/functions-utility.php`. It takes a path relative to the theme
root, reads the file with `file_get_contents()`, and falls back to a cURL fetch if that
function is disabled. It returns the file's contents as a string — for an SVG that is the
`<svg>` element itself, which lands in the page as real DOM.

## Why

- **The fill becomes CSS-controllable.** An `<img>` is opaque to the page's styles; an inline
  `<svg>` is not. This is the main reason.
- **One fewer network request**, and the markup gzips well alongside the rest of the HTML.
- It lets the same asset serve more than one brand colour.

The trade-off: an inlined SVG cannot be cached separately from the page, and it is repeated
in every response that renders it. That is the right trade for a small wordmark or logo; it
is the wrong one for a large illustration, which should stay an `<img>`.

## Make the fill controllable

Set the path fill to `currentColor` in the **source** file, then set `color` on an ancestor:

```svg
<path fill="currentColor" d="…" />
```

```css
.front-page-cortado__wordmark {
  color: var(--color-ochre);
}

.category-archive__the-cortado__wordmark {
  color: var(--color-gray-base);
}
```

Both of those render the same file at different colours. Without `currentColor` the fill is
baked and inlining buys nothing but the saved request.

## Sizing

An inlined `<svg>` has no intrinsic box the way an `<img>` does, so give it one. Which
dimension you drive depends on what the mark is doing.

**Beside type — drive the height.** A wordmark sitting next to body copy should key off the
same scale as the type around it, so set a rem height and let the `viewBox` supply the width:

```css
.newsletter-signup-cortado__wordmark svg {
  display: block;
  height: 1.75rem;
  width: auto;
  max-width: 100%; /* never let a wide mark overflow a narrow column */
}
```

This is the preferred form. A rem height is a meaningful number — it can be read against the
type scale — whereas a width is arbitrary and has to be re-guessed whenever the column
changes. Always pair it with `max-width: 100%`: a wide mark at a fixed height will otherwise
overflow a narrow column.

**As a display element — drive the width.** Where the mark *is* the layout, spanning its
column like a heading, width is the point and a rem height would cap it:

```css
.category-archive__the-cortado__wordmark svg {
  display: block;
  width: 100%;
  height: auto;
}
```

Either way the `viewBox` preserves the aspect ratio, so the other dimension is `auto`. Keep
`viewBox` in the source — svgo retains it, but a hand-edited file can lose it.

## Accessibility

An `<img>` has `alt`; an inlined `<svg>` does not. When the graphic carries meaning — a
wordmark acting as a heading, for instance — put a `<title>` in the source file and point at
it:

```svg
<svg role="img" viewBox="0 0 1383.98 165.538">
  <title>The Cortado</title>
```

A `<title>` as the first child of `<svg>` is itself the accessible name, so an `<h1>`
wrapping the SVG still has one. Purely decorative vectors should instead be hidden with
`aria-hidden="true"`.

**Do not give the title an `id` and point at it with `aria-labelledby`.** That pairing is a
legacy technique for assistive tech that did not support `<title>`, and it breaks the moment
a mark is inlined more than once on a page: the ids collide, which is invalid HTML, and
`aria-labelledby` resolves to the first match, so every later instance is named after the
first one's element. The Cortado wordmark is inlined from three call sites and the signup
block can be inserted repeatedly in a single post, so this was reachable. A bare `<title>`
has no id to collide.

Note that svgo strips `role="img"` during the build while keeping `<title>`. The accessible
name survives; the role does not.

**When the SVG is the only content of a link, button or heading, name the container.** Put
`aria-label` on the wrapping element rather than relying on the `<svg>` title propagating
outward:

```php
<a href="<?php echo esc_url( $category_link ); ?>" class="front-page-cortado__wordmark" aria-label="The Cortado">
  <?php echo nm_get_file( '/dist/img/products/the-cortado/the-cortado-wordmark.svg' ); ?>
</a>
```

Name-from-content does reach the `<title>` in current browsers, so this is hardening rather
than a fix for a broken state — but the container's name is then explicit and does not depend
on how a given AT treats an `<svg>` that has lost its `role`. It also states the name at the
call site, where the person reading the template can see it.

The alternative — keeping `role="img"` by overriding svgo's `removeUnknownsAndDefaults`
(`keepRoleAttr`) in `webpack.config.js` — was considered and rejected: the build config
requires team approval, and the gain is robustness only.

## Preparing a Figma export

Figma's SVG export carries attributes that fight CSS sizing. Strip them from the file placed
in `src/`:

- `preserveAspectRatio="none"` — breaks scaling
- inline `style="display: block;"` — overrides the stylesheet
- fixed `width` / `height` — prevents fluid sizing (keep `viewBox`)
- generated layer ids such as `id="Union"`

Then swap the exported hex fill for `currentColor`.

## Where files live

`src/img/products/<brand>/` for brand assets, `src/img/specials/` for one-offs. The build
copies them to `dist/img/…` and runs svgo. `nm_get_file()` is always pointed at the **dist**
path, never `src`.

## Existing usage

- `partials/front-page/show-blocks/audio-acfm.php` and `audio.php` — product logos
- `partials/specials/banners/focus-breaking-britain.php`, `survey-link.php` — decorative vectors
- `category-the-cortado.php` and `partials/front-page/show-blocks/the-cortado.php` — the
  Cortado wordmark, one file in two treatments: cream on the ochre category hero, ochre on
  the white front-page box
