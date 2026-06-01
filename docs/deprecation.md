# Deprecation Policy

## Rule

**Remove after 4 minor releases, or the next major version, whichever is later.**

| Deprecated in | Earliest removal (later of 4 minors / next major) |
|---------------|-----------------|
| 4.3.x | 5.0.0 (later of 4.7.0 and 5.0.0) |
| 4.6.x | 5.0.0 (later of 4.10.0 and 5.0.0) |
| 5.0.x | 6.0.0 (later of 5.4.0 and 6.0.0) |

The 4-minor / next-major rule provides:
- Enough deploy cycles for content migrations in the DB to have run
- At least one planned release after the migration lands before the deprecated code is removed
- A predictable removal window contributors can rely on

## Annotation format

Every deprecated PHP function or class must have a `@deprecated` docblock tag:

```php
/**
 * @deprecated 4.3.0 Use render_share_link() instead.
 */
function render_tweet_link( $url, $title = null, $link_text = 'Tweet', $hashtag = null ) {
  render_share_link( 'twitter', $url, compact( 'title', 'link_text', 'hashtag' ) );
}
```

Required elements:
- `@deprecated X.Y.Z` — version when deprecated (patch = 0 if whole minor)
- Short replacement note — what to use instead, or `No replacement.` if dead code

No `@deprecated-tier` tag needed — the single rule covers all cases.

## Exceptions

**Remove immediately** (no grace period):
- Time-gated code whose window has expired (verify with `date`)
- Code proven unreachable by static analysis (grep confirms zero callers + no dynamic dispatch)
- Security vulnerability with no safe migration path

**Extend grace period** (document in the deprecation notice):
- Third-party plugin compatibility that Novara does not control — extend to next major only
- This should be rare; all theme function callers are internal

## Audit

If you use Claude Code, the `deprecation-audit` skill produces a triage table: every `@deprecated` symbol, its removal version threshold, caller count, and verdict. Without it, grep the codebase directly — e.g. `grep -rn "@deprecated" --include="*.php"` — and check each symbol's version against the removal rule above.

Remove in a dedicated `Refactor:` PR — never bundle with feature work.
