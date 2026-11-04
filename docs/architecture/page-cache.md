# Page cache purging

Logged-out readers never reach PHP on a cache hit. Three layers sit in front of
WordPress:

| Layer | Header to check | Cleared by |
|---|---|---|
| Kinsta full-page cache | `x-kinsta-cache` | Kinsta mu-plugin |
| Kinsta edge | `ki-cf-cache-status` | Kinsta mu-plugin (full purges) |
| Cloudflare APO | `cf-apo-via`, `cf-cache-status` | Cloudflare plugin |

`wp_cache_flush()` clears the object cache (Redis) only. It does **not** clear
any page cache, so it never fixes stale pages for readers.

## What purges what

| Change | Kinsta | Cloudflare APO | Where |
|---|---|---|---|
| Post publish, update, trash | Post, home, its archives | Post and related URLs | Plugins |
| Post permanent delete | Same | Same | `lib/functions-hooks.php` |
| Contributor pages of a changed post | Added to the purge | Added to the purge | `lib/functions-hooks.php` |
| Menu edit, term edit, term delete | Full purge | Full purge | Kinsta built-in, Cloudflare in `lib/functions-cache-purge.php` |
| Any CMB2 options page save | Full purge | Full purge | `lib/functions-cache-purge.php` |
| Published contributor's name or slug changes (bylines site-wide) | Full purge | Full purge | `lib/functions-cache-purge.php` |
| `_nm_mailchimp_key` added, changed or removed on any post (signup forms site-wide) | Full purge | Full purge | `lib/functions-cache-purge.php` |

Options pages get a full purge on every save, changed or not, so saving a page
doubles as a manual refresh. Neither plugin has a public API for purging an
arbitrary URL, so a homepage-only purge would mean calling plugin internals.

Jobs pages are not purged; they send revalidate headers instead
(`nm_job_cache_headers()`).

## Known gaps

- Editing a contributor's bio or image does not purge the article pages that
  show it at the bottom. Only a name or slug change does (see above).
- Changing a newsletter record's banner copy, image or colours does not purge
  the pages that embed it. Only a Mailchimp key change does.
- Content that depends on the time of rendering — live schedule "on now", the
  fundraiser end time — stays as rendered until the cached copy expires.

## Adding something new

If a new value renders on pages other than the post it is saved on, it needs a
purge. A new CMB2 options page is covered automatically. Post or term meta that
shows on unrelated pages is not: add its URLs through the `KinstaCache/purgeImmediate`
and `cloudflare_purge_by_url` filters, as the contributor purge does.

## Measuring the cost of full purges

Each full purge is logged (time, layer, trigger, user ID) in the
`nm_cache_purge_log` option, capped at the newest 200 entries:

```bash
wp option get nm_cache_purge_log --format=json
```

Compare the purge times with hit ratio and origin load:

- MyKinsta → Analytics → Cache (hit / miss ratio)
- MyKinsta → Analytics → Performance (PHP response time, throughput)
- Cloudflare dashboard → Caching → APO / cache analytics (origin requests)

If purges are frequent and each one shows as a clear dip in hit ratio or a rise
in PHP response time, a selective purge is worth building. If not, the full
purge stays.
