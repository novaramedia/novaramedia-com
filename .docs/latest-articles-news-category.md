# Latest Articles: News Category Support

## Overview

The front page "latest articles" section displays a list of posts with up to 3 thumbnail images. A smart image slot algorithm shifts images away from news posts (which typically lack strong imagery) onto non-news posts (features, opinion, analysis).

## Architecture

### Single unified partial

Previously three separate partials (`--default`, `--thumb-small`, `--thumb-large`), now merged into a single `latest-article.php` controlled by args:

```php
$args = [
  'post_id'           => int,
  'has_bottom_border' => bool,
  'show_image'        => 'small' | 'large' | false,
];
```

The partial handles:
- **Tag + time-since** — always shown, full-width row via `layout-split-level`
- **Title** — always shown
- **Image** — small square thumb (side-by-side with title), large 16:9 thumb (stacked below title), or none
- **Byline** — skipped for news posts, `render_bylines()` for articles, `render_standfirst()` for others

### Small image layout

Tag and time-since sit at full width above the text+image grid:

```
[tag ·················· time-since]   ← full width
[title                    ][image]   ← grid: 16col + 8col
[byline                   ]
```

### Time-since

The `js-time-since` span uses `data-timestamp` and is hydrated client-side by `Utilities.js:displayTimeSince()` via Luxon's `toRelative()`. No server-side rendering needed.

## Image Slot Algorithm

Located in `latest-articles.php`. Assigns up to 3 image slots across the post list.

### Normal path (3+ non-news posts available)

1. Collect indices of all non-news posts in display order
2. First 3 non-news indices get image slots
3. Sizes: small — **LARGE** — small (middle slot gets hero image)

### Fallback path (fewer than 3 non-news posts)

1. Start with all non-news post indices
2. Fill remaining slots from fallback positions `[1, 3, 6]` (2nd, 4th, 7th post)
3. **Gap enforcement**: fallback candidates must be at least 2 positions from any existing image slot (minimum gap of 1 post between images)
4. **Size restriction**: news posts only ever get `small`. The first non-news post (in display order) gets `large`, regardless of its array position
5. If gap enforcement eliminates candidates, the total may be fewer than 3 images

### Examples with 8 posts

| Scenario | Non-news at | Image slots | Sizes |
|---|---|---|---|
| All non-news | 0,1,2,3,4,5,6,7 | 0,1,2 | small, **large**, small |
| News at 0,1 | 2,3,4,5,6,7 | 2,3,4 | small, **large**, small |
| News at 0,1,2,3 | 4,5,6,7 | 4,5,6 | small, **large**, small |
| Only 2 non-news (at 0,4) | 0,4 | 0,4,6 | **large** (non-news), small, small |
| Only 1 non-news (at 2) | 2 | 2,6 | **large** (non-news), small — pos 1,3 rejected (too close to 2) |
| All news | none | 1,3,6 | small, small, small |

### Key behaviours

- Non-news posts are always preferred for image slots
- In the fallback path, news posts never receive the large image
- The first non-news post in display order always gets `large` in the fallback path, even if it's the only image
- Gap enforcement can reduce the total below 3 — this is acceptable as it avoids visually cluttered adjacent images
