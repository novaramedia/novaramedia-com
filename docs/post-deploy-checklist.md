# Post-Deploy Checklist

Manual steps to run **after deploying a release**. These are things the code
can't do on its own: rewrite-rule flushes, one-time admin saves (seed-then-
deprecate migrations), and verification that the live caching stack behaves as
intended. WordPress or the Kinsta/Cloudflare infrastructure needs a human action
for each.

## How to use

- After deploying `vX.Y.Z`, work through that version's section top-to-bottom.
- Add a new section each release. Keep finished sections for history — they
  document what was required and how to verify it again if something regresses.
- Anything in the CHANGELOG worded "after deploy…" / "Migration:" / "Requires a
  … flush" should have a matching step here.

---

## v4.7.0

### 1. Front Page Layout — seed the order (one-time)
**Admin > Front Page > Layout → Save once** (no changes needed).
Why: the Layout editor supersedes the legacy banner selects, which now only seed
the default order. Until a layout is saved, the front page falls back to the
historic order. Seed-then-deprecate migration — see
`docs/plans/front-page-layout-editor.md`.

### 2. Flush permalinks — drop the `/job/` archive route
**Admin > Settings > Permalinks → Save** (no changes needed).
Why: the `job` post type's `has_archive` was flipped to `false` (the public jobs
listing is the `/about/jobs` Page). Rewrite rules are cached in the DB, so the
phantom `/job/` archive route isn't removed until rules are flushed.

### 3. Verify job-page cache busting at the edge
The `Cache-Control` / `Expires` headers set by `nm_job_cache_headers()`
(`lib/functions-hooks.php`) make the jobs listing and single job pages revalidate
at deadline midnight. **But those origin headers can be stripped or overridden
by the caching stack in front of WP** — Kinsta's server-level full-page cache
and/or Cloudflare. Confirm what the **edge** actually returns:

```sh
# Jobs listing — run twice; second request shows HIT + Age if the edge caches HTML
curl -sSI https://novaramedia.com/about/jobs/ \
  | grep -iE 'cache-control|^expires|^age|cf-cache-status|x-kinsta-cache|x-cache'

# A single open job (grab a live /job/<slug>/ URL from the listing first)
curl -sSI https://novaramedia.com/job/<slug>/ \
  | grep -iE 'cache-control|^expires|^age|cf-cache-status|x-kinsta-cache|x-cache'
```

Interpret:

- **PASS** — origin header survived: `Cache-Control` contains `s-maxage=<N>`;
  `Expires` is the GMT timestamp of the relevant midnight (tonight for the
  listing, day-after-deadline for an open job); on a HIT, `Age` stays **below**
  `s-maxage`.
- **OVERRIDDEN** — the local rule is being clobbered: header missing/rewritten
  (e.g. a fixed long `max-age`), or `x-kinsta-cache: HIT` / `cf-cache-status: HIT`
  with an `Age` far larger than our `s-maxage`. → Fix at the layer that wins:
  - **Kinsta:** MyKinsta > Caching — confirm full-page cache honours origin
    `Cache-Control`, or add a path rule shortening TTL for `/about/jobs/` and
    `/job/*`.
  - **Cloudflare:** if HTML is cached (a "Cache Everything" rule), add a Cache
    Rule for those paths set to respect origin TTL.
- **NOT CACHED** — `cf-cache-status: DYNAMIC` and `x-kinsta-cache: BYPASS/MISS`
  every time: pages are served fresh from origin, no staleness to bust; the fix
  is belt-and-braces. Fine.

---

## vNext (unreleased) — embed consent gate (PR #523)

### 1. Verify block editor embed previews are NOT consent-gated
**Admin > edit any post with an embed block (or add a SoundCloud/Twitter URL embed block to a draft).**
The editor preview should show the normal embed, not the "content is blocked" placeholder.
Why: the gate relies on an `is_admin()` guard in `nm_embed_oembed_html()`; editor
previews also arrive via the REST oEmbed proxy, which wasn't exercisable in local
smoke tests (2026-07-08 — DevKinsta container couldn't fetch YouTube/Twitter oEmbed).
A gated preview means the guard isn't covering the editor's fetch path — front-end
is unaffected, but editors would see placeholders while writing.

### 2. Front-end consent smoke (2 min, incognito)
Open an audio post in a private window: placeholder shown, no soundcloud.com
requests in the Network tab → accept → player loads. Reload: player immediate.
Why: consent checking is client-side only (Kinsta page cache doesn't vary on the
consent cookie) — a stale cached page from before the deploy can serve embeds
without gate markup until the cache is purged.

---

## Template for future releases

```
## vX.Y.Z

### 1. <step> — <one-line why>
**Where to do it.** Why it's needed / what breaks without it.
```
