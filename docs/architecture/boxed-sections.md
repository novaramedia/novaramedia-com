# Boxed sections

The coloured "box" that front-page product blocks and brand heros sit in has one
canonical structure. Copy it exactly; do not reinvent it per template. The reference
implementation is `partials/front-page/show-blocks/dyor.php`.

## The pattern

```php
<?php // ── Section: <name> ── ?>
<section class="container mt-4 mb-4">
  <div class="grid-item is-xxl-24">
    <div class="grid-row background-{colour} ui-rounded-box ui-backgrounded-box-padding">

      <div class="grid-item is-xxl-24">
        <?php // Box content. Nested layouts use grid-row grid-row--nested. ?>
      </div>

    </div>
  </div>
</section>
```

Inner white cards inside the box:

```php
<div class="background-white ui-rounded-box pt-3 pb-3 pl-4 pr-4">
  …
</div>
```

## Rules

- **The `<section>` is the container.** `container mt-4 mb-4` on the section itself.
  There is no `grid-row` between the section and the first `grid-item is-xxl-24`.
- **Colour, radius and padding all sit on the one `grid-row` box:**
  `background-{colour} ui-rounded-box ui-backgrounded-box-padding`.
  `ui-backgrounded-box-padding` is `1.5rem 1rem` and is what gives the content its inset.
- **One box.** Not a `ui-rounded-box--top` half plus a `--bottom` half. That split
  exists in `category-do-your-own-research.php` and `page-support.php` and is not the
  front-page pattern.
- **Never full-bleed.** The box lives inside the container; the page background shows
  around it. At `xxl` the container is 1400px.
- **Nested layouts** inside the box are `grid-row grid-row--nested` (both classes),
  containing `grid-item is-*` children.
- **Spacing between sections** is the section's own `mt-4 mb-4`, not wrapper divs.
- **Media that must meet a box edge** (a hero graphic sitting on the bottom of the box)
  keeps `ui-backgrounded-box-padding` and adds `ui-backgrounded-box-padding--flush-bottom`.
  Do not use `pb-0` — spacing utilities compile before the UI module, so the component's
  `padding` shorthand overrides them. The modifier is staged in
  `src/styl/upstream-to-library.styl` pending promotion to nm-stylus-library.
- **Indentation:** two spaces; a blank line after the opening box `div` and before its
  close; `<?php // ── Section: … ── ?>` markers between sections.

## Why this is written down

The pattern has been rebuilt wrongly more than once — as a full-bleed band, and as the
`--top`/`--bottom` split — because the Figma frames do not make the boxing explicit and
the older category templates are not consistent with the front page. When a design
shows a coloured section, it is this box unless the design owner says otherwise.

## Where it is used

- `partials/front-page/show-blocks/dyor.php` — reference
- `partials/front-page/show-blocks/*.php` — product blocks
- `category-the-cortado.php` — brand hero
