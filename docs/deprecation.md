# Deprecation Policy

## Rule

**Remove deprecated code as soon as it is verified unused. If that hasn't happened, removal is due after 4 minor releases, or at the next major if that ships first.**

Everything that can call into this theme is in one of two places: this repo, or the production database (page templates, page slugs, post meta, options). There are no external consumers to give notice to, so once both places are verified clean there is nothing to wait for. Deprecated code left in place gets flagged during unrelated work, misleads readers, and rots untested.

The 4-minor window is a deadline, not a grace period. Past it, the audit reports the symbol as overdue and someone has to run the verification below.

| Deprecated in | Overdue at |
|---------------|-----------------|
| 4.3.x | 4.7.0 (4 minors), or 5.0.0 if it ships first |
| 4.6.x | 4.10.0 (4 minors), or 5.0.0 if it ships first |
| 4.7.x | 4.11.0 (4 minors), or 5.0.0 if it ships first |
| 5.0.x | 5.4.0 (4 minors), or 6.0.0 if it ships first |

## Verified unused

Both checks must pass before removal.

**1. No callers in the repo.** Grep for the symbol name, and for it as a quoted string in case it is registered as a hook callback. Callbacks dispatched dynamically (`call_user_func`, string interpolation) can't be grepped, so check those by reading the code.

**2. No use in the production database.** Use read-only WP-CLI over SSH on Kinsta live (connection details: MyKinsta → Sites → Info → SFTP/SSH). Run `wp option get siteurl` first to confirm you are on live. Typical checks:

```sh
# Page template file, e.g. page__example.php
wp post list --post_type=page --post_status=any \
  --meta_key=_wp_page_template --meta_value=page__example.php

# Slug-matched template, e.g. page-example.php
wp post list --post_type=page --post_status=any --name=example

# Meta key or option read only by the deprecated code
wp db query "SELECT post_id FROM $(wp db prefix)postmeta WHERE meta_key = '_nm_example' LIMIT 5"
wp option get nm_example
```

`--post_status=any` covers drafts and private posts. Trashed posts are excluded, so add `--post_status=trash` if those matter. If anything turns up, migrate or delete that data by hand before removing the code. Never remap old values in templates.

## Annotation format

Every deprecated PHP function, class, or file must have a `@deprecated` tag:

```php
/**
 * @deprecated 4.3.0 Use render_share_link() instead.
 */
function render_tweet_link( $url, $title = null, $link_text = 'Tweet', $hashtag = null ) {
  render_share_link( 'twitter', $url, compact( 'title', 'link_text', 'hashtag' ) );
}
```

Required elements:
- `@deprecated X.Y.Z`: the version when it was deprecated (patch = 0 if it covers a whole minor)
- A short replacement note: what to use instead, or `No replacement.` if it is dead code

For whole files (templates, meta-box files), put the tag in the file's header docblock. Don't write free-text markers such as `DEPRECATED @ VERSION 4.2.0`. The audit doesn't find them, which is how three newsletter templates stayed in place 8 minors past deprecation.

## Exceptions

**Remove immediately**, migrating callers in the same PR:
- Time-gated code whose window has expired (verify with `date`)
- Security vulnerability with no safe migration path

**Extend past the deadline** (document why in the deprecation notice):
- Third-party plugin compatibility that Novara does not control. Extend to the next major only.
- This should be rare, because all theme function callers are internal.

## Audit

If you use Claude Code, the `deprecation-audit` skill produces a triage table: every `@deprecated` symbol, its deadline, caller count, and verdict. Without it, grep the codebase directly, e.g. `grep -rn "@deprecated" --include="*.php"`, and run the verification above for each symbol.

Remove in a dedicated `Refactor:` PR. Never bundle a removal with feature work.
