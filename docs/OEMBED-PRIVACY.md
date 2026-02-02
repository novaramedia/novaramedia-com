# oEmbed Privacy & GDPR Compliance

## Current State

### What Works

- **YouTube embeds** - oEmbed via Gutenberg embed block
- **Twitter/X embeds** - oEmbed via Gutenberg embed block
- **Vimeo embeds** - oEmbed via Gutenberg embed block
- Block editor rendering - Each block wrapped in `.text-copy`, applies `the_content` filter

### Manual Embeds (Non-oEmbed)

Site already uses **youtube-nocookie.com** for manually generated YouTube embeds (via custom functions/shortcodes).

### Cookie Consent System

**Location:**

- JavaScript: `src/js/modules/Utilities.js` → `checkGDPRApproval()` method
- HTML: `partials/bottom-bar/cookie-notice.php` → `#obligation-bar` element

**How it works:**

1. On page load, `checkGDPRApproval()` checks for `cookie-approval` cookie
2. If cookie not present, shows `#obligation-bar` with privacy notice
3. User clicks `#obligation-accept` button
4. Cookie `cookie-approval=true` is set (via js-cookie library)
5. Bar is hidden

**Cookie name:** `cookie-approval`
**Value when approved:** `"true"`

**Checking consent in JS:**

```javascript
import Cookies from 'js-cookie';
const hasConsent = Cookies.get('cookie-approval') === 'true';
```

**Checking consent in PHP:**

```php
$has_consent = isset($_COOKIE['cookie-approval']) && $_COOKIE['cookie-approval'] === 'true';
```

---

## Privacy Issues with Current oEmbed Implementation

### ❌ GDPR Violations

All oEmbed embeds currently load **without checking user consent** and set tracking cookies:

| Platform             | Privacy Issues                                                                                                   | Data Sent To  |
| -------------------- | ---------------------------------------------------------------------------------------------------------------- | ------------- |
| **YouTube** (oEmbed) | • Uses `youtube.com` (not `youtube-nocookie.com`)<br>• Sets tracking cookies<br>• Sends IP, user agent, referrer | Google        |
| **Twitter/X**        | • Loads `platform.twitter.com/widgets.js`<br>• Sets tracking cookies<br>• Tracks engagement                      | Twitter/X     |
| **Instagram**        | • Not currently supported (WP removed in 5.5)<br>• Would load Meta tracking if implemented                       | Meta/Facebook |
| **Vimeo**            | • Loads Vimeo player<br>• May set analytics cookies                                                              | Vimeo         |

### Technical Details

**Where embeds are rendered:**

- `partials/singles/single-post-articles.php:77-88`
- Uses `render_block()` then `apply_filters('the_content', ...)`

**Where embed HTML is modified:**

- `lib/functions-filters.php:107-114` - `embed_oembed_html` filter
- Wraps YouTube/Vimeo in responsive container
- Does NOT modify embed source URLs

---

## ✅ Implemented: YouTube No-Cookie Embeds

### Overview

YouTube oEmbed URLs are now switched from `youtube.com/embed` to `youtube-nocookie.com/embed` to reduce tracking.

**Note:** youtube-nocookie.com still loads from Google servers but sets fewer cookies. For full GDPR compliance, would still need consent gate (future work).

### Implementation Details

**File:** `lib/functions-filters.php:106-131`

**Function:** `nm_embed_oembed_html()`

**How it works:**

1. Filter hooks into `embed_oembed_html` at priority 99
2. Checks if original URL contains `youtube.com/` or `youtu.be/`
3. Replaces `youtube.com/embed` with `youtube-nocookie.com/embed` in the iframe HTML
4. Wraps in responsive container divs

**Why youtu.be links work:**

- When WordPress processes a `youtu.be` short URL, it fetches oEmbed data from YouTube's API
- YouTube's oEmbed endpoint always returns iframes with `youtube.com/embed/VIDEO_ID` as the src
- The `str_replace` operates on the returned iframe HTML, not the original URL
- So both `youtube.com/watch?v=XXX` and `youtu.be/XXX` URLs result in the same iframe HTML being modified

**Current code:**

```php
/**
 * Add wrapper classes to oEmbed elements and use privacy-enhanced YouTube embeds.
 *
 * YouTube oEmbed returns iframes with youtube.com/embed URLs regardless of whether
 * the original URL was youtube.com or youtu.be. The str_replace works for both
 * because it operates on the returned iframe HTML, not the original URL.
 */
function nm_embed_oembed_html( $html, $url, $attr, $post_id ) {
  if ( str_contains( $url, 'youtube.com/' ) || str_contains( $url, 'youtu.be/' ) ) {
    // Replace youtube.com with youtube-nocookie.com in iframe src for reduced tracking
    $html = str_replace( 'youtube.com/embed', 'youtube-nocookie.com/embed', $html );
    return '<div class="oembed-element"><div class="u-video-embed-container">' . $html . '</div></div>';
  }

  if ( str_contains( $url, 'vimeo.com/' ) ) {
    return '<div class="oembed-element"><div class="u-video-embed-container">' . $html . '</div></div>';
  }

  return $html;
}
add_filter( 'embed_oembed_html', 'nm_embed_oembed_html', 99, 4 );
```

### Testing

1. Create a test post with YouTube embed block using `youtube.com` URL
2. Create another embed using `youtu.be` short URL
3. Publish and view on frontend
4. Inspect both iframe srcs - should be `youtube-nocookie.com/embed/VIDEO_ID`
5. Verify videos play correctly
6. Check existing posts with YouTube embeds still work

### Acceptance Criteria

- [x] YouTube oEmbed iframes use `youtube-nocookie.com` instead of `youtube.com`
- [ ] Videos play correctly (requires testing)
- [x] Responsive container wrapper still applied
- [ ] No regression on existing YouTube embeds (requires testing)
- [x] Vimeo embeds unaffected
- [x] Works with both youtube.com and youtu.be URLs

---

## Future Work: Consent Gate for Embeds

### Concept

Before loading any third-party embed (YouTube, Twitter, Vimeo), check cookie consent. If not approved, show placeholder with consent prompt.

### Architecture

**PHP (Server-side):**

1. Filter `render_block` for `core/embed` blocks
2. If platform is YouTube/Twitter/Vimeo, wrap in consent-gated container
3. Store original embed HTML in data attribute
4. Display placeholder with "Load [Platform] content" button

**JavaScript (Client-side):**

1. On placeholder click, check `Cookies.get('cookie-approval')`
2. If approved: inject original embed HTML
3. If not approved: trigger consent prompt (similar to existing `checkGDPRApproval()`)
4. After consent granted: inject embed

### Integration with Existing Cookie System

**Files to modify:**

- `lib/functions-filters.php` - Add `render_block` filter for embeds
- `src/js/modules/Utilities.js` - Add embed consent handler, reuse existing cookie logic
- New partial for embed placeholder HTML

**Reusable from existing system:**

- Cookie name: `cookie-approval`
- Cookie check: `Cookies.get('cookie-approval') === 'true'`
- Consent UI pattern: `#obligation-bar` style

**New JS module needed:**

```javascript
// Proposed: src/js/modules/EmbedConsent.js
export class EmbedConsent {
  constructor() {
    this.setupPlaceholders();
  }

  setupPlaceholders() {
    document
      .querySelectorAll('.embed-consent-placeholder')
      .forEach((placeholder) => {
        placeholder
          .querySelector('.embed-load-btn')
          .addEventListener('click', (e) => {
            this.handleEmbedClick(
              e.target.closest('.embed-consent-placeholder')
            );
          });
      });
  }

  handleEmbedClick(placeholder) {
    const hasConsent = Cookies.get('cookie-approval') === 'true';

    if (hasConsent) {
      this.loadEmbed(placeholder);
    } else {
      // Trigger consent flow, then load embed
      this.showConsentPrompt(() => this.loadEmbed(placeholder));
    }
  }

  loadEmbed(placeholder) {
    const embedHtml = placeholder.dataset.embedHtml;
    placeholder.outerHTML = embedHtml;
  }
}
```

### Priority

| Task                    | Priority | Effort             |
| ----------------------- | -------- | ------------------ |
| YouTube nocookie switch | High     | Low (30 min)       |
| Twitter consent gate    | Medium   | Medium             |
| YouTube consent gate    | Medium   | Medium             |
| Vimeo consent gate      | Low      | Medium             |
| Instagram support       | Low      | Skip unless needed |

---

## Code Locations Reference

**Embed rendering:**

- `partials/singles/single-post-articles.php:77-88` - Block rendering loop
- `lib/functions-filters.php:106-131` - `nm_embed_oembed_html()` filter (YouTube nocookie + responsive wrapper)

**Cookie consent system:**

- `src/js/modules/Utilities.js:64-82` - `checkGDPRApproval()` method
- `partials/bottom-bar/cookie-notice.php` - Consent banner HTML
- Cookie name: `cookie-approval`

**Manual embed functions:**

- Search codebase for `youtube-nocookie` to find existing patterns

---

## Testing Checklist

### YouTube No-Cookie (Immediate)

- [ ] YouTube embeds use youtube-nocookie.com
- [ ] Videos play correctly
- [ ] Responsive container still works
- [ ] Works in block editor preview
- [ ] Works on published posts

### Consent Gate (Future)

- [ ] Placeholders show for embeds before consent
- [ ] Clicking placeholder checks consent status
- [ ] Consent prompt appears if not yet approved
- [ ] Embeds load correctly after consent
- [ ] No third-party requests before user interaction
- [ ] Mobile-responsive placeholders
- [ ] Accessible (keyboard navigation, screen readers)

---

## Resources

- [GDPR and oEmbeds](https://wordpress.org/support/article/embeds/#gdpr-and-privacy)
- [YouTube iframe parameters](https://developers.google.com/youtube/player_parameters)
- [YouTube Privacy Enhanced Mode](https://support.google.com/youtube/answer/171780)
- [Embed Privacy plugin](https://wordpress.org/plugins/embed-privacy/)
- [WordPress oEmbed API](https://developer.wordpress.org/reference/functions/wp_oembed_get/)
