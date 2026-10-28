# Strip Empty Paragraphs on Save — Design Spec

**Date:** 2026-10-28
**Status:** Draft, awaiting review
**Scope:** Theme only. Save-time normalisation of post content and CMB2 text fields, plus a one-off backfill of existing data.

## 1. Problem

Editors press Enter at the end of (and between) paragraphs in TinyMCE fields to add space. The editor stores these as `&nbsp;` lines or empty `<p>` elements, which `wpautop` renders as visible blank paragraphs — on the front page above the fold when it happens in a short description, and often on Downstream posts.

The QA bot (`nm-qa-marita-bot` 0.6.0, `short_description_trailing_blank_lines` / `body_trailing_blank_lines`) now flags trailing blank lines after publish. That catches the problem but leaves an editor to fix every case by hand, and only covers two fields.

## 2. Decisions

1. **Normalise at save time, in one place.** Values are cleaned as they are written, so every consumer (templates, REST API, feeds, the QA bot) reads clean data.
2. **No output-side trimming.** Templates do not `trim()` or regex stored values to hide this. Template-level fixes are not the right layer for data problems and drift as new echo sites are added.
3. **No blocking editor rule.** The fix is mechanical, so the machine does it. `lib/meta/cmb2-validation.php` keeps its existing job (required / word-length rules) and is not changed.
4. **Strip empty paragraphs anywhere, not just at the end.** Spacing between content is a layout concern and belongs in CSS. There is no supported case for editor-inserted blank lines. If stripping exposes a layout that relied on them, the layout is fixed.
5. **One-off backfill** for existing data, following the `scripts/one-off/` + `wp eval-file` precedent, with a dry run.
6. **The QA bot check stays.** After this ships it should go quiet; a new hit means a write path this spec does not cover.

## 3. What gets stripped

A shared, pure helper `nm_strip_empty_paragraphs( string $value ): string` in `lib/functions-utility.php`. "Blank" below means only whitespace, `&nbsp;`, U+00A0 and `<br>` / `<br />`.

### HTML / classic content (value contains no `<!-- wp:` block markers)

- `<p …>` elements whose content is blank — removed anywhere.
- Lines whose content is blank (the stored form of an empty TinyMCE paragraph — classic content is saved without `<p>` tags) — removed anywhere.
- `<br>` at the end of a paragraph (before `</p>`, a blank line, or the end of the value) — removed.
- Runs of 3+ newlines collapsed to 2 (one paragraph break) after the above.
- Leading and trailing blank content trimmed.

### Kept

- `<br>` inside a paragraph that has text — a soft line break (poems, addresses, lyrics).
- `&nbsp;` inside a line of text (e.g. `10&nbsp;km`).
- Anything that is not a blank paragraph or blank line: embeds, shortcodes, images, oEmbed URLs on their own line.

### Block content (value contains `<!-- wp:` markers)

- Only whole empty paragraph blocks (`<!-- wp:paragraph … -->` wrapping a blank `<p>`) are removed, anywhere.
- Nothing inside any other block is touched. Editing markup inside a block risks the editor's "unexpected or invalid content" error.
- Spacer blocks are left alone — they are the sanctioned way to add space if one is ever needed.

The site runs the Classic Editor plugin, so block content is the minority case; it is handled conservatively rather than ignored because the block editor is reachable (see `cmb2-validation.php` docblock).

## 4. Where it runs

### Post content and excerpt — `wp_insert_post_data`

Hooked in `lib/functions-filters.php`. Runs on every insert/update path (classic editor, block editor via REST, quick edit, imports), applied to `post_content` and `post_excerpt`.

- Post types: every type that supports `editor` (`post_type_supports( $type, 'editor' )`), so new CPTs are covered automatically. This skips core's internal storage types — `oembed_cache` (provider embed HTML), `customize_changeset` and `wp_global_styles` (JSON) — which hold no editor-written content.
- `$data` is slashed at this point. The patterns match no quotes or backslashes, so working on the slashed string is safe; tests cover a slashed attribute (`<p class=\"x\">`).

### CMB2 fields — `cmb2_sanitize_{$type}`

One filter per field type, covering post meta, term meta and the CMB2 options pages (Links Bar, Fundraising, etc.) and group sub-fields, without touching any field definition.

- Types: `text`, `text_small`, `textarea`, `textarea_small`, `wysiwyg`.
- Excluded: `textarea_code` (embed codes), URL/email/number/date types.
- CMB2 has no on/off setting for this; the per-type filter is its extension point. Registration is a loop of `add_filter( "cmb2_sanitize_{$type}", … )` over the types above with one shared callback.
- These filters short-circuit CMB2's own sanitiser when they return non-null. The callback therefore runs the default first — `$sanitizer->{$type}()` — and strips its result. If another filter already returned a non-null override, that value is passed through unchanged.

### Out of scope

- Meta written outside CMB2 (direct `update_post_meta` from other plugins). The backfill can be re-run if this turns out to matter.
- Term descriptions, user bios, widget text.
- Existing plain-text `trim()` calls at `lib/functions-seo.php` and `lib/functions-custom.php` — different concern (whitespace in plain-text fields), left as is.

## 5. Backfill

`scripts/one-off/2026-10-strip-empty-paragraphs.php`, run with `wp eval-file`. Dry run by default; `apply` writes.

- Posts: `post_content` and `post_excerpt` for every post type that supports `editor`, all statuses except `auto-draft` and `inherit` (revisions stay as history).
- Meta: every CMB2 field of the types above, found from the registered boxes (`CMB2_Boxes::get_all()`), including group fields (helper applied to each sub-field) and options-page values.
- Writes go through `$wpdb->update` for post fields so `post_modified` is not bumped (otherwise thousands of posts would show as "updated" in feeds and sitemaps), followed by `clean_post_cache()`. Meta and options use `update_post_meta` / `update_term_meta` / `update_option`, which do not touch modified dates.
- Dry run prints a count per field and the IDs + titles of changed posts, so a sample can be checked on staging before `apply`.
- Idempotent: the helper is stable on its own output, so re-runs change nothing.

## 6. Testing

- **Unit (`tests/unit/lib/StripEmptyParagraphsTest.php`, hooks in `FunctionsFiltersTest.php`)** — the helper is pure, so no WordPress stubs needed beyond what the file already uses. Cases: trailing/leading/mid-content `&nbsp;` lines and empty `<p>`; `<p>` with attributes, slashed attributes; trailing `<br>`; soft `<br>` inside text kept; inline `&nbsp;` kept; oEmbed URL lines kept; empty paragraph blocks removed and other blocks byte-identical; Spacer block kept; idempotence (`f(f(x)) === f(x)`); empty string.
- **Manual on staging** — save a post in the classic editor with blank lines between and after paragraphs, and in the short description; confirm the saved value and the rendered page. Same in the block editor. Re-save a Links Bar options page.
- **Backfill** — dry run on staging after a fresh DB pull, spot-check ~10 changed posts on the front end before `apply`.
- No Playwright spec: the behaviour is save-side and the e2e suite runs read-only against staging.

## 7. Docs and conventions

- `.github/copilot-instructions.md`: under Meta, add the rule "content and CMB2 text fields are normalised on save by `nm_strip_empty_paragraphs()`; never add template-level trims for blank lines, and never rely on editor-inserted blank lines for spacing — use CSS", with an anti-example.
- `docs/post-deploy-checklist.md`: backfill step (dry run, spot-check, apply) and a note to watch the QA bot channel for a week.
- `CHANGELOG.md`: one Unreleased line.

## 8. Risks

- **Posts that relied on blank lines for spacing will tighten up.** Accepted by design (decision 4). Fix the layout in CSS if any are reported.
- **Editor state after save (block editor).** The classic editor reloads after saving and shows the cleaned value. The block editor may keep showing its local copy, trailing empty block included, until the page is reloaded. Harmless; to be confirmed during build.
- **`<pre>` content** with blank lines made of `&nbsp;` would lose them. No current usage of `<pre>` in post content is expected; the backfill dry run will show it if there is any.
