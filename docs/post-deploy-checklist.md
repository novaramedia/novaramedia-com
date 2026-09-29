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

## Unreleased

### 1. Set the If I Speak hero blurb
**Admin > Posts > Categories > If I Speak → Formatted description.** The redesigned hero
shows this field if set, otherwise the core Description. Launch copy from the design:

```html
A podcast from Moya Lothian-McLean and Ash Sarkar where the personal meets the political. Every week they tackle social dilemmas, cultural phenomena and the frustrations of modern life.
```

Leave the core Description as it is — it feeds Open Graph and meta descriptions.

### 2. Backfill season/episode meta — BEFORE deploying
Plain post meta, so it can run before the new templates ship; they then find it on first render. Staging needs this and step 3 too, before the PR's Playwright run can pass (`capsule-podcast-archives.spec.js`); on staging run step 3 straight after this one.

```bash
wp eval-file scripts/one-off/2026-09-season-episode-backfill.php        # dry run, check output
wp eval-file scripts/one-off/2026-09-season-episode-backfill.php apply
```

Before `apply`, read the dry run's Do Your Own Research list: every title must be a season 1 episode. Put the ID of anything else (trailer, clip, season 2) in `$nm_dyor_skip_ids` at the top of the script, re-run the dry run, and set that post's Season / Episode box by hand in wp-admin. Posts that already have a season are skipped, so re-running is safe.

Verify after deploy: `/category/committed/`, `/category/foreign-agent/` and `/category/death-in-westminster/` show the same episode labels as before.

### 3. Re-date Death in Westminster — straight after deploying
Its dates were set in reverse so the old newest-first archive showed Episode 1 on top. The archive now lists oldest first, so until this runs it shows Episode 6 → 1. Needs step 2 done first.

```bash
wp eval-file scripts/one-off/2026-09-season-episode-backfill.php redate          # dry run, check episode → date mapping
wp eval-file scripts/one-off/2026-09-season-episode-backfill.php redate apply
```

Verify: `/category/death-in-westminster/` lists Episode 1 → 6, and a single episode URL still 301s to its archive anchor.

### 4. Rewrite capsule podcast standfirsts
The standfirst on each Committed, Foreign Agent and Death in Westminster post still reads "Episode 1", "Bonus 1" etc. Archives no longer use it. Replace each with a real standfirst (editorial), since it shows on single posts, in search results and in related posts.

### 5. Save the Do Your Own Research seasons
**Admin > Products > Do Your Own Research.** The page shows nothing saved yet; the archive meanwhile builds season 1 from the old category map fields. Add a season 1 entry (number 1, title "Season 1", the file key and default node from the category's Do Your Own Research box) and Save. Then add season 2 with its FigJam file key and Save again.

Verify: `/dyor/` shows a Season 2 block above Season 1 once season 2 episodes have their Season / Episode box set, and each map loads on click. `/dyor/page/2/` 301s to `/dyor/`.

---

## v4.9.0

### 1. Create The Cortado category — before flushing permalinks
**Admin > Posts > Categories → add "The Cortado", slug `the-cortado`.**

**Open question for editorial — confirm the parent before creating it.** Does The
Cortado sit *inside* Opinion, or *beside* it as its own child of Articles? The design
work assumed a direct child of Articles (`/category/articles/the-cortado/`); the local
database currently nests it under Opinion (`/category/articles/opinion/the-cortado/`).
The digital team's recommendation is the sibling option: nested, Cortado posts would dominate
Opinion, and their UI tag reads "Opinion" (the tag shows the post's first
sub-category) rather than "The Cortado".
One knock-on worth knowing before choosing: `nm_is_article()` treats the `articles` term or
a *direct* child of it as an article, so a post filed only under a grandchild category would
be classified as a non-article and render its short description rather than its standfirst.
Cortado posts currently carry `articles` directly as well, so they are unaffected either way.

Otherwise this is an editorial taxonomy decision, not a technical one — the code derives the
URL from the term, so either works: the newsletter redirect resolves through
`get_term_link()`, the template hierarchy keys on the slug, and the vanity slug is
unaffected. But the two produce different canonical URLs, so pick one before launch
rather than moving the term afterwards and stranding links.

Why this step comes first: the `/the-cortado/` vanity slug is registered by
`handle_internal_rewrites()` (`lib/functions-rewrites.php`), which calls
`get_category_by_slug()` and silently skips the rule when the term is missing.
The newsletter→category 301 also bails without it, leaving
`/newsletters/the-cortado/` reachable as before. Do this step first or the flush
below registers nothing.

### 2. Flush permalinks — register the `/the-cortado/` route
**Admin > Settings > Permalinks → Save** (no changes needed). Why: rewrite
rules are cached in the DB; the new `^the-cortado/?$` rule doesn't match until
they're rebuilt.

This flush also registers the paginated vanity rules (`^<path>/page/([0-9]{1,})/?$`)
for **every** branded path, not just Cortado — see #607. Until it runs,
`/downstream/page/2/` and its equivalents keep 404ing as they do today.

### 3. Fill the newsletter record and category copy

**Admin > Newsletters > The Cortado.** Three fields drive the Cortado signup
surfaces (archive signup, front page block, inline signup block):

| Field | Consequence if empty |
| --- | --- |
| Mailchimp Newsletter name (`_nm_mailchimp_key`) | No signup form renders anywhere |
| Banner text (`_nm_banner_text`) | Surfaces render with no copy under the wordmark |
| Signup button label (`_nm_banner_button_label`) | Button reads "Sign up" instead of "Get The Cortado" |

Banner text accepts `<strong>` and `<em>`. The launch copy is:

```html
Get your shot of political analysis from <strong>Ash Sarkar</strong> and <strong>Steven Methven</strong>, every Monday and Friday morning.
```

That is the short copy, used by the inline signup block. The archive signup and the
front page block use the longer copy from the category instead:

**Admin > Posts > Categories > The Cortado → Formatted description**
(`_nm_category_formatted_description`). Empty falls back to the banner text above. Keep
the core Description field plain text — it feeds Open Graph and meta descriptions. Launch copy:

```html
Your shot of political analysis from <strong>Ash Sarkar</strong> and <strong>Steven Methven.</strong> Brewed on Monday and Friday mornings.
```

On all three Cortado surfaces the bold spans render in the sans face against serif
body copy — the treatment is scoped per surface in CSS.

### 4. Verify the three Cortado URLs at the edge

```sh
# Vanity slug — expect 200, served in place, no redirect
curl -sS -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramedia.com/the-cortado/

# Newsletter permalink — expect 301 to the category archive
curl -sS -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramedia.com/newsletters/the-cortado/

# Vanity pagination on an existing brand — expect 200 in place, not a 404 or a redirect
curl -sS -o /dev/null -w "%{http_code} %{redirect_url}\n" https://novaramedia.com/downstream/page/2/

# Canonical archive — expect 200. Use the URL the term actually resolves to, which
# depends on the parent chosen in step 1: /category/articles/the-cortado/ if it sits
# directly under Articles, /category/articles/opinion/the-cortado/ if nested in Opinion.
# The redirect above targets whichever it is, so read the destination it reports.
curl -sS -o /dev/null -w "%{http_code} %{redirect_url}\n" "<canonical URL from step 1>"
```

If the vanity slug 404s, step 1 or 2 was skipped. If the newsletter permalink
still returns 200, the category is missing (step 1). Kinsta full-page cache may
serve a stale 404 for `/the-cortado/` briefly after the flush — purge if so.

---

### 5. Re-pick any newsletter banner set to Gray Base

**Admin > Newsletters → each newsletter → Banner background color.** Gray Base is now the
page colour, so it has been removed from the dropdown and a banner still saved with it
renders as an invisible box on the grey page. Newsletters saved with it show no option
selected; choose White, Gray Mid or a brand colour and Update. Locally this was The Pick.

---

## v4.8.1

### 1. Verify the Novara Live standfirst exemption
**Edit a Novara Live post in the classic editor → Update with an empty
Standfirst.** Why: this release exempts posts in the `novara-live` category
(and its descendants) from the required-standfirst rule. The post should save
without the validation alert; a post in any other category should still block.
Tell editorial they no longer need to copy the Short description into the
Standfirst on Novara Live posts.

---

## v4.8.0

### 1. Notify editorial before deploy — old posts may block on Update
**Editorial heads-up before/at deploy.** Why: publish-type submits (including
Update on already-published posts) now block if Standfirst or Short
description is empty, and on video/audio posts if YouTube ID / Soundcloud URL
is empty. Old posts missing those meta values will need them filled in before
the next Update. Recommend running a count of affected published posts
beforehand so editorial knows the scale of the backlog.

### 2. Confirm the Do Your Own Research category has posts
**Admin > Posts > Categories.** Why: the block self-hides rather than erroring.
`partials/front-page/show-blocks/dyor.php` returns early if the
`do-your-own-research` category term is missing or has no posts, so an empty
category looks identical to the block never having shipped. The block renders a
featured post plus up to four recents, so ≥2 posts gives the intended layout.

### 3. Front Page Layout — add the DYOR block
**Admin > Front Page > Layout → add "Show block: Do Your Own Research" → Save.**
Why: this release makes the block *selectable* on production; it does not place
it. Nothing changes on the front page until it is added to a saved layout. If
the v4.7.0 seed-save (below) was never done, this save covers it.

### 4. Optional — set the Figma file key for the "Explore the Map" CTA
**Admin > Posts > Categories > Do Your Own Research → "Figma file key".**
Why: the CTA linking to the category map is gated on the
`_nm_dyor_figma_file_key` term meta (`dyor.php:47`). Without it the block
renders correctly, just without the button. "Figma default node ID" on the same
screen controls the map's default zoom.

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

## Template for future releases

```
## vX.Y.Z

### 1. <step> — <one-line why>
**Where to do it.** Why it's needed / what breaks without it.
```
