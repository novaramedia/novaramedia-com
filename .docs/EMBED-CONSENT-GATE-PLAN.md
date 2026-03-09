# Cookie Consent Gating for Third-Party Embeds

## Context

Third-party embeds (SoundCloud, Twitter/X, Vimeo, etc.) currently load without checking cookie consent, exposing users to tracking cookies before they've agreed. YouTube is already handled with `youtube-nocookie.com` and is exempt. The site has an existing cookie consent system (`cookie-approval` cookie + `#obligation-bar`) but it doesn't gate embed loading.

Because Kinsta page caching doesn't vary by the `cookie-approval` cookie, **all consent checking must happen client-side in JS**. PHP always outputs consent gate wrappers; JS handles hydration.

## Approach

PHP wraps all non-YouTube embeds in a `.embed-consent-gate` container, with the real embed HTML base64-encoded in a `data-embed-html` attribute. A new `EmbedConsent` JS module checks the cookie on page load and either immediately hydrates all gates (returning user) or shows placeholder UI with accept buttons (new user). Custom events coordinate between the embed gates, the existing cookie bar, and the SoundCloud lazy loader.

### Three embed pathways covered

1. **Classic editor embeds** - URLs auto-embedded via WordPress oEmbed, intercepted by `embed_oembed_html` filter at display time
2. **Block editor embeds** - `core/embed` Gutenberg blocks with oEmbed HTML baked at save time, intercepted by `render_block` filter at render time
3. **Hardcoded template embeds** - SoundCloud via `render_soundcloud_embed_iframe()` in PHP templates (YouTube already exempt via nocookie)

### Platform classification

| Platform   | Status | Reason                                                  |
| ---------- | ------ | ------------------------------------------------------- |
| YouTube    | Exempt | Uses `youtube-nocookie.com`, minimal cookie exposure    |
| SoundCloud | Gated  | Sets tracking cookies via `w.soundcloud.com`            |
| Twitter/X  | Gated  | Loads `platform.twitter.com/widgets.js`, heavy tracking |
| Vimeo      | Gated  | Sets analytics cookies                                  |
| Spotify    | Gated  | Sets tracking cookies                                   |
| Instagram  | Gated  | Loads Meta tracking                                     |
| Facebook   | Gated  | Loads Meta tracking                                     |
| TikTok     | Gated  | Sets tracking cookies                                   |
| Any other  | Gated  | Whitelist approach - only YouTube exempt                |

---

## Files

### New

| File                                     | Purpose                                                    |
| ---------------------------------------- | ---------------------------------------------------------- |
| `src/js/modules/EmbedConsent.js`         | Consent gate hydration, cookie setting, event coordination |
| `src/styl/components/embed-consent.styl` | Placeholder styles                                         |

### Modified

| File                             | Change                                                                                                                                                                                                   |
| -------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `lib/functions-filters.php`      | Add `nm_consent_gate_wrap()` helper + detection helpers. Modify `nm_embed_oembed_html()` to gate non-YouTube oEmbeds. Add `nm_consent_gate_block_embeds()` `render_block` filter for block editor embeds |
| `lib/renderers.php`              | Modify `render_soundcloud_embed_iframe()` to wrap output in consent gate                                                                                                                                 |
| `src/js/modules/Utilities.js`    | Dispatch `cookie-consent-granted` event on accept; listen for same event to hide bar                                                                                                                     |
| `src/js/modules/AudioPlayers.js` | Extract `findAndLoadPlayers()` method; listen for `embed-consent-hydrated` event to re-scan                                                                                                              |
| `src/js/main.js`                 | Import/instantiate `EmbedConsent` before `AudioPlayers` in `onReady()`                                                                                                                                   |
| `src/styl/site.styl`             | Add `@import "components/embed-consent"` under COMPONENTS                                                                                                                                                |
| `docs/OEMBED-PRIVACY.md`         | Update to reflect implemented system                                                                                                                                                                     |

---

## Implementation Steps

### Step 1: PHP helper functions

**`lib/functions-filters.php`** - Add these new functions (no existing code modified yet):

- `nm_consent_gate_wrap($html, $platform)` - Wraps embed HTML in a `.embed-consent-gate` div with the original HTML base64-encoded in `data-embed-html`. Outputs a placeholder with:
  - Platform name in the message
  - Explanatory text: "[Platform] content is blocked because you have not accepted cookies."
  - Accept button using existing `ui-button ui-button--small ui-button--white` classes
  - Privacy policy link
  - `is_feed()` guard to skip gating in RSS feeds
- `nm_is_embed_exempt($html)` - Returns true for YouTube/nocookie embeds
- `nm_detect_embed_platform($html)` - Maps domains to human-readable names (SoundCloud, Twitter/X, Vimeo, Spotify, Instagram, Facebook, TikTok, or "Third-party" fallback)
- `nm_html_has_iframe_or_script($html)` - Returns true if HTML contains `<iframe` or `<script` tags (avoids gating plain text oEmbed responses)

### Step 2: CSS

**New file `src/styl/components/embed-consent.styl`:**

- `.embed-consent-gate` - vertical margin
- `.embed-consent-gate__placeholder` - light grey background (`var(--color-gray-light)`), border, centered flexbox layout, `min-height: 100px`, border-radius
- `.embed-consent-gate__content` - max-width 400px for readability
- `.embed-consent-gate__message` - `font-size-9` class applied in PHP, bottom margin
- `.embed-consent-gate__accept` - button styling inherited from `ui-button` classes in PHP
- `.embed-consent-gate__privacy-link` - subtle underlined link, `font-size-8`

**`src/styl/site.styl`** - Add import under COMPONENTS section (after `quote`):

```stylus
@import "components/embed-consent"
```

### Step 3: New EmbedConsent JS module

**New file `src/js/modules/EmbedConsent.js`:**

Key methods:

- `onReady()` - Checks `cookie-approval` cookie via js-cookie. If consented: calls `hydrateAllGates()` immediately. If not: binds click handlers on `.embed-consent-gate__accept` buttons and listens for `cookie-consent-granted` event
- `hydrateAllGates()` - Finds all `.embed-consent-gate` elements, decodes base64 `data-embed-html`, replaces gate with real HTML. After all gates hydrated, dispatches `embed-consent-hydrated` event (for AudioPlayers). **Script re-execution**: scripts injected via `innerHTML` don't auto-execute, so any `<script>` elements are recreated as new DOM nodes to trigger browser execution (critical for Twitter/X `widgets.js`)
- `hydrateGate(gate)` - Single gate hydration with base64 decode, DOM replacement, and script re-creation
- `handleAccept()` - Sets `cookie-approval` cookie (365 days), dispatches `cookie-consent-granted`, calls `hydrateAllGates()`
- `bindGateButtons()` - Attaches click handlers to all accept buttons

### Step 4: Modify existing JS modules

**`src/js/modules/Utilities.js`** - In `checkGDPRApproval()`:

- After setting cookie on `#obligation-accept` click, add: `document.dispatchEvent(new CustomEvent('cookie-consent-granted'))`
- Add listener for `cookie-consent-granted` to hide `#obligation-bar` (handles consent from embed gate buttons)

**`src/js/modules/AudioPlayers.js`**:

- Extract `findPlayers()` + `loadPlayersWithThrottling()` into a new `findAndLoadPlayers()` method
- Call `findAndLoadPlayers()` from `onReady()` (preserves current behaviour)
- Add `document.addEventListener('embed-consent-hydrated', () => this.findAndLoadPlayers())` to re-scan for newly-revealed SoundCloud placeholders after consent gate hydration

**`src/js/main.js`**:

- Import `EmbedConsent` from `./modules/EmbedConsent.js`
- Instantiate `this.embedConsent = new EmbedConsent()` in `Site` constructor
- Call `this.embedConsent.onReady()` **before** `this.audioPlayers.onReady()` in `onReady()` (ensures consent gates are hydrated before AudioPlayers scans for SoundCloud placeholders)

### Step 5: Activate consent gating - oEmbed filter (classic editor)

**`lib/functions-filters.php`** - Modify existing `nm_embed_oembed_html()`:

- YouTube branch: **unchanged** (exempt - nocookie switch + responsive wrapper)
- Vimeo branch: wrap in responsive container first, then wrap in `nm_consent_gate_wrap($wrapped, 'Vimeo')`
- New catch-all: detect platform via `nm_detect_embed_platform()`, gate if `nm_html_has_iframe_or_script()` returns true

This catches classic editor embeds processed at display time via `the_content` filter.

### Step 6: Activate consent gating - block editor filter

**`lib/functions-filters.php`** - New function `nm_consent_gate_block_embeds($block_content, $block)`:

- Hooked to `render_block` filter at priority 5 (before existing `nm_add_caption_class` at priority 10)
- Only processes blocks where `$block['blockName'] === 'core/embed'`
- Guards: `is_admin()` returns early (no gating in block editor preview), empty content returns early
- YouTube check: if `nm_is_embed_exempt()`, applies nocookie switch as safety net for old posts but no consent gate
- Other providers: reads `$block['attrs']['providerNameSlug']` for platform identification (WordPress stores this in block attributes, e.g. `"twitter"`, `"vimeo"`, `"soundcloud"`), falls back to `nm_detect_embed_platform()`, wraps in consent gate

### Step 7: Activate consent gating - hardcoded SoundCloud

**`lib/renderers.php`** - Modify `render_soundcloud_embed_iframe()`:

- Use `ob_start()` before existing output and `ob_get_clean()` after to capture the embed HTML (both lazy and non-lazy paths)
- Wrap captured HTML: `echo nm_consent_gate_wrap($embed_html, 'SoundCloud')`
- Preserves existing calling convention (function echoes output, callers don't expect a return value)

### Step 8: Build, test, documentation

- Run `npm run build` to compile JS/CSS
- Test all scenarios (see Verification section)
- Update `docs/OEMBED-PRIVACY.md` with implemented system details

---

## Event Flow

```
User clicks embed "Accept"     User clicks cookie bar "Accept"
        |                                |
        v                                v
 EmbedConsent.handleAccept()    Utilities (sets cookie)
 - Sets cookie                          |
 - Dispatches cookie-consent-granted <---+
 - Calls hydrateAllGates()              |
        |                               v
        |                      EmbedConsent listens
        |                      -> hydrateAllGates()
        v                              |
 All gates replaced with               v
 real embed HTML              Utilities listens
        |                     -> hides #obligation-bar
        v
 Dispatches embed-consent-hydrated
        |
        v
 AudioPlayers listens
 -> findAndLoadPlayers()
 -> SoundCloud iframes load
```

---

## Edge Cases Handled

| Case                                         | Solution                                                                                                                                             |
| -------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- |
| **RSS feeds**                                | `nm_consent_gate_wrap()` returns raw HTML when `is_feed()` is true                                                                                   |
| **Admin/editor preview**                     | `nm_consent_gate_block_embeds()` returns raw HTML when `is_admin()` is true                                                                          |
| **Script execution**                         | Twitter/X embeds include `<script>` tags that won't execute via `innerHTML` - `hydrateGate()` recreates script elements to trigger browser execution |
| **Flash of placeholder for consented users** | Minimal - `EmbedConsent.onReady()` hydrates synchronously at DOM ready before meaningful paint. Acceptable trade-off for cache compatibility         |
| **Multiple embeds on one page**              | Clicking accept on any one gate loads ALL embeds on the page                                                                                         |
| **Old YouTube block embeds**                 | `nm_consent_gate_block_embeds()` applies nocookie switch as safety net for posts saved before the oEmbed filter existed                              |
| **Page caching**                             | HTML is always the same (consent gate wrapper). JS handles dynamic behaviour client-side. No cache invalidation needed                               |
| **SoundCloud two-step loading**              | Consent gate hydration reveals `.soundcloud-lazy` placeholder, then AudioPlayers hydrates to iframe. Both happen near-instantly for consented users  |

---

## Verification Checklist

- [ ] **New visitor** - Visit an audio post (SoundCloud) and a post with block editor embeds (Twitter/Vimeo). Verify consent gate placeholders appear. Check Network tab: no requests to soundcloud.com, twitter.com, vimeo.com before accepting
- [ ] **Accept via cookie bar** - Click Accept on `#obligation-bar`. All embeds should load. SoundCloud plays
- [ ] **Accept via embed button** - Fresh session, click "Accept cookies & load" on any embed gate. All gates hydrate. Cookie bar hides
- [ ] **Returning visitor** - With cookie set, load pages with embeds. Embeds appear immediately, no visible placeholder flash
- [ ] **YouTube exempt** - YouTube embeds load without any consent gate on all pages
- [ ] **RSS** - Check feed output contains raw embeds, no consent gate HTML
- [ ] **Block editor** - Edit a post with embeds, verify normal preview in editor
- [ ] **Build** - `npm run build` succeeds without errors
- [ ] **Mobile** - Consent gate placeholders are readable and buttons tappable on mobile viewports

---

## Dependencies

- Existing `js-cookie` library (v3.0.1) - already in use for `cookie-approval` cookie
- Existing `ui-button` CSS classes - used for accept buttons
- No new npm packages required
