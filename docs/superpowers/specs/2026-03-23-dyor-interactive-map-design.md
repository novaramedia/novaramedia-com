# DYOR Interactive Map — Design Spec

> **Status: Partially implemented (descoped)**
>
> This spec was the original design for an interactive map using Figma Embed Kit 2.0 with postMessage-driven navigation. During implementation, the Embed Kit 2.0 approach was descoped because `NAVIGATE_TO_FRAME_AND_CLOSE_OVERLAYS` could not be verified against FigJam board embeds, and the OAuth client-id requirement added unnecessary complexity for the initial release.
>
> **What was actually implemented:** A simple Figma iframe embed with `node-id` targeting. Each post stores a FigJam node ID in post meta, and the archive page constructs the iframe URL with the latest episode's node ID to set the initial viewport. There is no JS module, no postMessage communication, and no hover-to-navigate interaction. The category meta stores only the file key and a default node ID (not an embed URL or client ID).
>
> The interactive features described below remain a potential future enhancement if Embed Kit 2.0 support for FigJam boards is confirmed.

## Overview

Replace the static Figma iframe embed on the Do Your Own Research category archive with an interactive map using Figma Embed Kit 2.0. Episodes link to map nodes, and the map navigates to relevant positions via the postMessage API.

## Goals

- Map auto-pans to the latest episode's node when scrolled into viewport
- Hovering a post card in the archive grid navigates the map to that episode's node
- Episodes are linked to map nodes via a post meta field (Figma node ID)
- No server-side Figma API calls required

## Architecture

### Figma Embed Kit 2.0

The current simple embed iframe is upgraded to use Embed Kit 2.0:
- Requires a Figma OAuth app `client-id` (public, safe to expose — only enables postMessage communication)
- `embed-host` identifies the site
- `scaling=min-zoom` to avoid over-zooming when navigating to nodes
- `viewport-controls=true` for manual pan/zoom
- Initial `node-id` set to the latest episode's Figma node

**Graceful degradation:** If no client ID is configured, the map renders as a simple embed (current behaviour) without interactive features. The JS module checks for the client ID data attribute before initialising.

**Note:** `NAVIGATE_TO_FRAME_AND_CLOSE_OVERLAYS` is documented for prototype embeds. It needs verification against FigJam board embeds at implementation time. If it doesn't work for boards, we fall back to dynamically updating the iframe `src` with a new `node-id` param (iframe reload approach — heavier but guaranteed to work).

### Data Model

**Post meta field:** `_nm_dyor_figma_node_id` (text, CMB2)
- Added to posts in the "do-your-own-research" category only (via `show_on_cb`)
- Contains the FigJam node ID (e.g. `125-70`)
- Optional — posts without a node ID don't participate in map interactions

**Category meta fields (on the DYOR category term):**
- `_nm_dyor_map_embed_url` — existing field, kept as the simple embed fallback URL
- `_nm_dyor_figma_file_key` — the Figma file key (extracted from the board URL)
- `_nm_dyor_figma_client_id` — the OAuth app client ID

PHP constructs the Embed Kit 2.0 URL from the file key and client ID. If either is missing, falls back to `_nm_dyor_map_embed_url`.

### PHP Changes

**`lib/meta/meta-boxes-category-dyor.php`**
- Add fields: Figma file key, Figma client ID (alongside existing embed URL field)

**New file: `lib/meta/meta-boxes-post-dyor.php`**
- CMB2 meta box with `show_on_cb` that checks if the post belongs to the DYOR category
- Single text field: `_nm_dyor_figma_node_id`

**`category-do-your-own-research.php`**
- If file key + client ID are set: render Embed Kit 2.0 iframe with constructed URL
- Otherwise: render simple embed from `_nm_dyor_map_embed_url` (current behaviour)
- Map container gets `data-latest-node-id` attribute (from latest episode's meta, omitted if none set)
- Map container gets `data-figma-client-id` attribute (signals JS to initialise interactive features)
- Each post card in the archive grid gets `data-figma-node-id` attribute (only if meta is set)

### JS Module: DYORMap

New module following existing theme pattern (`src/js/modules/DYORMap.js`):

```
class DYORMap {
  constructor()   — finds map iframe and post cards, bails if no client-id data attr
  onReady()       — binds events
  bind()          — IntersectionObserver on map section, mouseenter listeners on post cards
  onIframeReady() — called when INITIAL_LOAD received, enables navigation
  onMapVisible()  — sends navigate message to latest episode node (once)
  onPostHover(e)  — sends navigate message to hovered post's node ID
  navigateToNode(nodeId) — postMessage to iframe (no-op if iframe not ready)
}
```

**postMessage format:**
```js
iframe.contentWindow.postMessage({
  type: 'NAVIGATE_TO_FRAME_AND_CLOSE_OVERLAYS',
  nodeId: '125-70'
}, 'https://www.figma.com');
```

**Events listened for:**
- `INITIAL_LOAD` from iframe — gate for when navigation messages can be sent
- IntersectionObserver on `.dyor-archive__map-embed` — triggers initial pan to latest episode

**Timeout:** If `INITIAL_LOAD` is not received within 10 seconds, the module disables interactive features silently. The embed still works as a regular iframe — users just lose hover-to-navigate.

### Embed URL Format

```
https://embed.figma.com/board/{fileKey}/{fileName}
  ?client-id={oauthClientId}
  &embed-host=novaramedia
  &node-id={latestEpisodeNodeId}
  &scaling=min-zoom
  &viewport-controls=true
  &footer=false
  &page-selector=false
```

### Mobile Behaviour

On mobile, the map renders with `viewport-controls=true` for native Figma pan/zoom. Post card hover interactions are unavailable. The initial auto-pan to the latest episode still fires via IntersectionObserver. No mobile-specific interactions are added for now.

## Fallback Plan

If `scaling=min-zoom` zooms too tightly on individual nodes:
- Create invisible bounding rectangles in FigJam around each episode's neighbourhood
- Store the rectangle's node ID in the post meta instead of the artwork's node ID
- No code changes needed — just different node IDs in the meta fields

## Out of Scope

- Clickable hotspots on the map linking back to posts (add links in FigJam directly)
- Episode sidebar navigation
- Search-the-map functionality
- Server-side Figma REST API integration / caching
