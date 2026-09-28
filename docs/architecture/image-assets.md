# Image assets

Raster brand assets live in `src/img/products/<brand>/`, one-offs in `src/img/specials/`.
The Webpack build copies them to `dist/img/…` and generates `.avif` and `.webp` alongside
any `.jpg` or `.png` via sharp. Templates reference the **dist** path and serve the variants
with `<picture>`:

```php
<picture>
  <source srcset="<?php echo esc_url( $base . 'name.avif' ); ?>" type="image/avif">
  <source srcset="<?php echo esc_url( $base . 'name.webp' ); ?>" type="image/webp">
  <img src="<?php echo esc_url( $base . 'name.png' ); ?>" alt="…" width="…" height="…" />
</picture>
```

Only the source raster belongs in `src/` — never hand-place a generated variant in `dist/`.
SVGs pass through svgo instead; see `inline-svg.md`.

## Replacing an asset in place

**Delete the generated variants before rebuilding.** The pipeline does not regenerate an
`.avif` or `.webp` when its source changes but the filename stays the same, so a rebuild
leaves the old variants in `dist/` and the browser keeps serving them. Nothing errors, the
build reports success, and the page silently shows the previous artwork.

```sh
rm -f dist/img/products/<brand>/<name>.avif dist/img/products/<brand>/<name>.webp
npm run build
```

Confirm afterwards that the variants actually changed — compare timestamps against the
source, and check the webp header: `VP8 ` is lossy without alpha, while `VP8X` or `VP8L`
carries an alpha channel.

```sh
stat -f "%Sm %z %N" -t "%H:%M:%S" src/img/products/<brand>/<name>.png dist/img/products/<brand>/<name>.*
head -c 16 dist/img/products/<brand>/<name>.webp | xxd | head -1
```

## Transparency

Check that an asset meant to sit on more than one background is genuinely transparent, not
merely carrying an unused alpha channel. Figma bakes the containing frame's background into
an export as opaque pixels, so a PNG can report RGBA while every corner pixel is solid. Sample
the corners rather than trusting the colour type, and ask for a transparent export from the
design file if they are not clear.

## Treated imagery

Duotone, halftone and similar treatments are **artworked into the asset**, not applied in
CSS. Templates render what is uploaded and apply no colour treatment of their own.
