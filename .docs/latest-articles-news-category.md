# Latest Articles: News Category Support

## Current State

The front page "latest articles" section uses 3 partials for different layouts:

| Partial | Used at index | Layout |
|---------|--------------|--------|
| `latest-article--default.php` | 0, 2, 4, 5, 7 | Title + byline, no image |
| `latest-article--thumb-small.php` | 1, 6 | Title + byline + small square thumb (side-by-side) |
| `latest-article--thumb-large.php` | 3 | Title + byline + large 16:9 thumb (stacked) |

All three partials share ~90% identical code (validation, meta fetching, UI tag, byline/standfirst logic). The `js-time-since` element exists in all three but is currently **commented out**.

Image assignment is **hard-coded by index** (positions 1, 3, 6 get images).

## Changes Needed

### 1. Hide byline for 'news' category posts

In the byline section of each partial, skip `render_bylines()` when the post has the `news` category. Currently:

```php
if ( $is_article ) {
  render_bylines( $article_post_id );
} else {
  render_standfirst( $article_post_id );
}
```

Needs to become:

```php
if ( $is_news ) {
  // no byline for news posts
} elseif ( $is_article ) {
  render_bylines( $article_post_id );
} else {
  render_standfirst( $article_post_id );
}
```

Detection: `has_category( 'news', $article_post_id )`.

### 2. Restore time-since display

Uncomment the `js-time-since` span in all three partials. The JS (`Utilities.js:displayTimeSince()`) already handles `.js-time-since` elements with `data-timestamp` — it calls Luxon's `toRelative()`. No JS changes needed.

```php
<div class="layout-split-level font-size-8 font-weight-bold mb-1">
  <?php render_post_ui_tags( $article_post_id ); ?>
  <a href="<?php echo get_permalink( $article_post_id ); ?>" class="ui-hover">
    <span class="js-time-since" data-timestamp="<?php echo $timestamp; ?>"></span>
  </a>
</div>
```

### 3. Smart image assignment (prefer non-news posts)

Currently images are at fixed indices (1, 3, 6). Instead:

- In `latest-articles.php`, before the loop, sort/partition the post IDs so that non-news posts land on the image slots (1, 3, 6) when possible
- OR: pass a `show_image` flag to each partial based on whether the post should show an image, decided by a simple algorithm:
  1. Assign image slots to the first 3 non-news posts encountered (1 large + 2 small)
  2. If fewer than 3 non-news posts exist, fill remaining image slots with news posts
  3. Always end up with exactly 2 small + 1 large image

**Approach: flag-based** (simpler, no reordering). Pre-scan the posts in `latest-articles.php`, decide which 3 get images, then pass `'show_image' => 'small'|'large'|false` to the single unified partial.

### 4. Refactor: single partial

All three partials are nearly identical. Merge into one `latest-article.php` that accepts:

```php
$args = [
  'post_id'           => int,
  'has_bottom_border' => bool,
  'show_image'        => 'small' | 'large' | false,
];
```

The partial handles:
- UI tag + time-since (always)
- Title (always)
- Image: small thumb / large thumb / none (based on `show_image`)
- Byline: skip for news, render_bylines for articles, render_standfirst for others

Delete the three old partials after.

## Implementation Order

1. **Add `is_news` helper** or use `has_category('news', $post_id)` inline
2. **Create unified `latest-article.php`** partial with all three layouts in one file, controlled by `show_image` arg
3. **Update `latest-articles.php`** loop: pre-scan posts for news category, assign image slots preferring non-news, call single partial with appropriate args
4. **Delete old partials** (`--default`, `--thumb-small`, `--thumb-large`)
5. **Test** on DevKinsta — verify 2 small + 1 large image always renders, news posts have no byline, time-since displays

---

Notes from session:

Algorithm (lines 17-58):

  1. Pre-scans $latest_articles_posts_ids to find which indices are non-news posts (line 22-26)
  2. If 3+ non-news posts exist: first 3 non-news indices get images (line 28-29)
  3. If fewer than 3 non-news: uses all non-news posts, fills remaining slots from default positions [1, 3, 6] — which will be news posts (lines 31-42)
  4. Sorts the chosen indices so they're in display order (line 45)
  5. Assigns types: 1st = small, 2nd = large, 3rd = small (lines 49-52)

  Examples with 8 posts:
  ┌──────────────────────────┬─────────────────┬─────────────┬────────────────────────────────────────┐
  │         Scenario         │   Non-news at   │ Image slots │                 Result                 │
  ├──────────────────────────┼─────────────────┼─────────────┼────────────────────────────────────────┤
  │ All non-news             │ 0,1,2,3,4,5,6,7 │ 0,1,2       │ First 3 posts get images               │
  ├──────────────────────────┼─────────────────┼─────────────┼────────────────────────────────────────┤
  │ News at 0,1              │ 2,3,4,5,6,7     │ 2,3,4       │ First 3 non-news get images            │
  ├──────────────────────────┼─────────────────┼─────────────┼────────────────────────────────────────┤
  │ News at 0,1,2,3          │ 4,5,6,7         │ 4,5,6       │ First 3 non-news get images            │
  ├──────────────────────────┼─────────────────┼─────────────┼────────────────────────────────────────┤
  │ Only 2 non-news (at 0,4) │ 0,4             │ 0,1,4       │ Both non-news + fallback at position 1 │
  ├──────────────────────────┼─────────────────┼─────────────┼────────────────────────────────────────┤
  │ All news                 │ none            │ 1,3,6       │ Falls back to original positions       │
  └──────────────────────────┴─────────────────┴─────────────┴────────────────────────────────────────┘
  The render loop (lines 76-89) just looks up each index in the $image_map — clean and simple.
