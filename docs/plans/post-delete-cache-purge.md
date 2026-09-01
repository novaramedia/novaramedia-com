# Post-Delete Cache Purge Investigation

## Problem

Deleting a post on prod didn't bust cache. Anyone with a cached copy (or served
via Cloudflare edge) kept seeing the "deleted" page as live. Confirmed sequence
with the user:

1. Trashed the post — page still live.
2. Permanently deleted it — page still live.
3. Manual "Purge Individual File" in the Cloudflare WP plugin — **this fixed it**.
4. Ran a full "purge everything" afterwards as a belt-and-braces measure.

## Environment note — read this before acting on anything below

Only the **production Kinsta install** matters here. Staging is not fronted by
the same caching stack, so it cannot reproduce or verify this bug. Everything
below marked "confirmed by code" is true in any environment (same plugin/theme
code everywhere); everything marked "needs verification" must be checked/tested
against **prod only**.

## Code findings (confirmed by reading source, any environment)

### 1. Kinsta mu-plugin — no purge hook on permanent delete

`wp-content/mu-plugins/kinsta-mu-plugins/cache/class-cache-purge.php:106-109`
hooks only:

- `transition_post_status` → publish
- `wp_insert_post` → update while published
- `wp_trash_post` → trash

No hook on `delete_post` / `before_delete_post`. Permanent delete never purges
Kinsta's own page cache. Confirmed gap, independent of Cloudflare.

### 2. Cloudflare plugin — `deleted_post` hook fires too late to work

`wp-content/plugins/cloudflare/cloudflare.loader.php:96-105` hooks
`deleted_post` → `purgeCacheByRelevantURLs()`. `deleted_post` fires **after**
WP core removes the row. Inside that method, `Hooks.php:166-169` calls
`get_post($postId)`, which returns `null` post-deletion, hits `continue`, and
the purge is silently skipped. Confirmed dead code path for permanent deletes.

The trash path is unaffected by this specific bug — `transition_post_status`
fires while the post row still exists.

### 3. Hypothesis A (APO/plugin-specific-cache gate closed) — ruled out

`Hooks.php:140-152`: `purgeCacheByRelevantURLs()`'s entire body is gated behind
`isPluginSpecificCacheEnabled() || isAutomaticPlatformOptimizationEnabled()`.
Suspected this gate was closed, silently no-opping the automatic purge even on
trash (which otherwise fires at the correct time).

**Ruled out 2026-07-01** — user confirmed Automatic Platform Optimization (APO)
has been enabled on prod for a long time. Gate is open. Not the cause.

## Open question — real cause still unconfirmed

With APO confirmed on, the trash-time hook should have purged successfully
(correct timing, gate open) — but it didn't. Only a manual per-URL purge
worked. Remaining candidates, none yet checked against prod:

- Purge request sent to Cloudflare's API but failed silently (auth/zone/
  network). Plugin has debug logging (`Hooks.php` `$this->logger->debug(...)`)
  — check logs from the incident window if still retained.
- URL mismatch — `getPostRelatedLinks()` may compute a URL variant (protocol,
  trailing slash, AMP, query args) that doesn't match the actual cached edge
  key, so the API call "succeeds" but purges the wrong thing.
- APO may have separate purge semantics from a standard cache purge call —
  worth confirming the plugin calls the correct endpoint for APO specifically.
- Request flow is visitor → Cloudflare edge → Kinsta origin cache → WP. Kinsta
  purges correctly on trash (see finding 1, for trash specifically — its gap is
  only on *permanent* delete). A stale Cloudflare edge entry alone fully
  explains the symptom regardless of Kinsta's correctness — plausible only the
  edge layer is at fault here.

## Next steps (prod-only — staging cannot reproduce this)

1. Check the Cloudflare plugin's zone/domain config and any dashboard-level
   Cache/Page Rules on prod — confirm the plugin targets the right zone and
   that dashboard rules aren't caching more aggressively than the plugin
   assumes.
2. Pull plugin debug logs from the original incident window, if retained —
   confirm whether the trash-time purge call fired, what URL it targeted, and
   what Cloudflare's API returned.
3. Controlled repro on prod, off-peak: create a disposable draft, publish
   briefly, trash it, `curl -sSI` the URL immediately for `cf-cache-status` /
   `age` / `x-kinsta-cache`, then compare against using the manual "Purge
   Individual File" button on a second test post. Isolates whether the
   automatic trash-time purge is firing at all vs. firing but ineffective.
4. ~~Fix the two confirmed-independent bugs regardless of the outcome above~~
   **DONE 2026-09-01** (branch `fix/post-delete-cache-purge`), implemented in
   `lib/functions-hooks.php`:
   - `before_delete_post` → `Cache_Purge::initiate_purge()` via the
     `$kinsta_muplugin` global closes the Kinsta permanent-delete gap; the
     `_cmb_contributors` purge pattern is reused via the existing
     `KinstaCache/purgeImmediate` filter so contributor pages purge on delete.
   - `cloudflare_purge_url_actions` filter adds `before_delete_post` to the
     Cloudflare plugin's own purge actions, so its handler runs while the post
     row still exists (fixes the `deleted_post`-fires-too-late bug); the
     existing `cloudflare_purge_by_url` contributor filter applies unchanged.
   - Known limitation, acceptable: a post deleted *from trash* purges its
     `__trashed`-suffixed permalink — its live URL was already purged at trash
     time. The fix fully covers direct force-deletes.
   - Runtime verification still prod-only (see step 3); local verification was
     `php -l` plus static reading of both plugins' purge paths.
5. Leave `wp_trash_post` alone for now — code inspection shows it fires
   correctly at the right time with the gate open. The mystery is why its
   effect isn't reaching the edge, not a missing or misfiring hook. Fix that
   once step 3 identifies the actual failure point — don't guess at a fix
   before then.

## Environments

- **Local DevKinsta / staging**: fine for reading code, confirming hook
  wiring, and checking new PHP doesn't fatal. Cannot validate actual
  cache-busting behavior — no equivalent edge cache layer in front.
- **Production Kinsta**: the only environment where this bug is real and the
  only one any fix can be verified against.
