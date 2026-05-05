# Tech Debt & Deferred Fixes

Non-blocking improvements, deferred bug fixes, and known issues that aren't release-blockers. Sourced primarily from Copilot/human code reviews where the finding was valid but out of scope for the PR it was raised on.

**How to use:**
- Before starting a release cycle or a refactor sprint, scan this file and pull in what's relevant
- When an item is fixed, delete its section (git history preserves the record)
- New entries: include **Files** (with line refs if useful), **Why deferred**, and a **Fix approach**

---

## Security

### ssh-keyscan trust-on-first-use in deploy workflows

**Files:** `.github/workflows/deploy-production.yml:35`, `.github/workflows/deploy-staging.yml:47`, `.github/workflows/cypress.yml:68`

All three workflows populate `~/.ssh/known_hosts` by running `ssh-keyscan` at runtime. This is trust-on-first-use — the runner will accept *whatever* host key is presented the first time, which is vulnerable to MITM. Particularly concerning for the production deploy path.

**Why deferred:** Project-wide pattern across 3 workflows, not specific to any single PR. Requires adding a new secret (`KINSTA_PROD_KNOWN_HOSTS` or similar) containing the pinned host key entry, which needs coordination outside the code change.

**Fix approach:**
1. On a trusted machine/connection, capture the Kinsta host key: `ssh-keyscan -p <PORT> <HOST>`
2. Verify the fingerprint against Kinsta's records
3. Store the full known_hosts line as a GitHub Actions secret
4. Replace the `ssh-keyscan` line in each workflow with `echo "$KNOWN_HOSTS_ENTRY" >> ~/.ssh/known_hosts`

Source: Copilot review on PR #461.

---

### Output escaping on archive templates

**Files:** `category-downstream.php:51`, `category-acfm.php:63,71`

`$podcast_url` and `$podcast_copy` from term meta are echoed raw into `href` and link text. Should use `esc_url()` and `esc_html()`.

```php
// Before
<a href="<?php echo $podcast_url; ?>"><?php echo $podcast_copy; ?></a>

// After
<a href="<?php echo esc_url( $podcast_url ); ?>"><?php echo esc_html( $podcast_copy ); ?></a>
```

Both files have desktop + mobile versions of the same link (4 instances total).

Source: Copilot review on PR #422.

---

### Validate newsletter block ID

**File:** `src/blocks/newsletter-signup/render.php:27`

`$newsletter_id` from block attributes should be sanitised with `absint()` before use in `get_post_meta()` / `get_the_title()`.

```php
$newsletter_id = absint( $newsletter['id'] );
if ( ! $newsletter_id ) {
  return;
}
```

Source: Copilot review on PR #422.

---

## Bugs

### parse_blocks null blockName

**File:** `partials/singles/single-post-articles.php:86`

`parse_blocks()` can return "freeform" blocks where `blockName` is `null`. Accessing `$block['blockName']` without null-coalescing can cause PHP notices.

```php
// Fix with null coalescing
$block_name = $block['blockName'] ?? null;
```

Source: Copilot review on PR #422.

---

### Caption class injection logic

**File:** `lib/functions-filters.php:242`

The second condition (`wp-caption-text`) won't run if the first replacement already added `font-size-8` anywhere in the block content. Should make replacements independent per caption class using regex that checks each class attribute individually.

Source: Copilot review on PR #422.

---

## Architecture

### Newsletter block stores entire post object

**File:** `src/blocks/newsletter-signup/edit.js:54`

The block saves the full REST API post object into `attributes.newsletter`, which gets serialised as JSON in `post_content`. Should store only `newsletterId` (integer) and fetch/resolve server-side in `render.php`.

Source: Copilot review on PR #422.

---

## Code Quality

### parseInt radix + NaN guard

**File:** `src/blocks/newsletter-signup/edit.js:51`

`parseInt(event.target.value)` should specify radix 10, and empty value produces `NaN` which stores `undefined` in attributes. Add explicit handling for empty selection.

Source: Copilot review on PR #422.

---

### in_array strict comparison

**File:** `lib/functions-custom.php:253`

The fix for duplicate featured posts switched from strict to loose `in_array`. Safer to normalise both arrays to integers and keep strict comparison.

```php
$featured_ids = array_map( 'intval', $featured_ids );
// then use in_array( $post_id, $featured_ids, true )
```

Source: Copilot review on PR #422.
