# PR #422 Review Follow-ups (v4.4.0)

Post-release improvements from Copilot review. None are release-blockers.

---

## Security: Output escaping on archive templates

**Files:** `category-downstream.php:51`, `category-acfm.php:63,71`

`$podcast_url` and `$podcast_copy` from term meta are echoed raw into `href` and link text. Should use `esc_url()` and `esc_html()`.

```php
// Before
<a href="<?php echo $podcast_url; ?>"><?php echo $podcast_copy; ?></a>

// After
<a href="<?php echo esc_url( $podcast_url ); ?>"><?php echo esc_html( $podcast_copy ); ?></a>
```

Both files have desktop + mobile versions of the same link (4 instances total).

---

## Security: Validate newsletter block ID

**File:** `src/blocks/newsletter-signup/render.php:27`

`$newsletter_id` from block attributes should be sanitised with `absint()` before use in `get_post_meta()` / `get_the_title()`.

```php
$newsletter_id = absint( $newsletter['id'] );
if ( ! $newsletter_id ) {
  return;
}
```

---

## Architecture: Newsletter block stores entire post object

**File:** `src/blocks/newsletter-signup/edit.js:54`

The block saves the full REST API post object into `attributes.newsletter`, which gets serialised as JSON in `post_content`. Should store only `newsletterId` (integer) and fetch/resolve server-side in `render.php`.

---

## Bug: parse_blocks null blockName

**File:** `partials/singles/single-post-articles.php:86`

`parse_blocks()` can return "freeform" blocks where `blockName` is `null`. Accessing `$block['blockName']` without null-coalescing can cause PHP notices.

```php
// Fix with null coalescing
$block_name = $block['blockName'] ?? null;
```

---

## Bug: Caption class injection logic

**File:** `lib/functions-filters.php:242`

The second condition (`wp-caption-text`) won't run if the first replacement already added `font-size-8` anywhere in the block content. Should make replacements independent per caption class using regex that checks each class attribute individually.

---

## Code quality: parseInt radix + NaN guard

**File:** `src/blocks/newsletter-signup/edit.js:51`

`parseInt(event.target.value)` should specify radix 10, and empty value produces `NaN` which stores `undefined` in attributes. Add explicit handling for empty selection.

---

## Code quality: in_array strict comparison

**File:** `lib/functions-custom.php:253`

The fix for duplicate featured posts switched from strict to loose `in_array`. Safer to normalise both arrays to integers and keep strict comparison.

```php
$featured_ids = array_map( 'intval', $featured_ids );
// then use in_array( $post_id, $featured_ids, true )
```
