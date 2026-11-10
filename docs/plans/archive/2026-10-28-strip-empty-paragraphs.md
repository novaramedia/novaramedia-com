# Strip Empty Paragraphs on Save Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove editor-inserted empty paragraphs and blank lines from post content, excerpts and CMB2 text fields when they are saved, and clean existing data once.

**Architecture:** One pure helper, `nm_strip_empty_paragraphs()`, in `lib/functions-utility.php`. Two save-time hooks in `lib/functions-filters.php` call it: `wp_insert_post_data` for post content/excerpt, and `cmb2_sanitize_{$type}` for CMB2 text field types (wrapping CMB2's default sanitiser). A one-off `wp eval-file` script applies the same helper to existing data.

**Tech Stack:** PHP (WordPress theme), CMB2 2.11, PHPUnit + Brain Monkey (`npm run test:php`), WP-CLI. No JS, no build step, no `dist/` changes.

**Spec:** `docs/specs/2026-10-28-strip-empty-paragraphs-design.md`

## Global Constraints

- Branch: `feature/strip-empty-paragraphs` (off `development`). Commit locally; ask before pushing (every push runs Playwright CI against staging).
- Production-quality code; match file style (2-space indent, `nm_` snake_case functions, docblocks like neighbouring functions).
- Blank = only whitespace, `&nbsp;`, U+00A0 and `<br>` / `<br />`.
- Kept: `<br>` inside a paragraph with text; `&nbsp;` inside a line of text; embeds, shortcodes, oEmbed URL lines.
- Block content (contains `<!-- wp:`): only whole empty `wp:paragraph` blocks removed; every other block byte-identical; Spacer blocks kept.
- Post types: any type where `post_type_supports( $type, 'editor' )`. No allowlist.
- CMB2 types: `text`, `text_small`, `textarea`, `textarea_small`, `wysiwyg`. Not `textarea_code`.
- Backfill must not bump `post_modified`.
- No output-side trims added anywhere.
- Docs updated in the same commit as the behaviour they describe.

## Review Focus

1. **Windows line endings** (`\r\n`) from pasted content — blank lines still detected; text lines untouched. Pinned in Task 1.
2. **Entity variants** — `&#160;` and `&#xA0;` count as blank like `&nbsp;`; uppercase `<P>` / `<BR/>` matched. Pinned in Task 1.
3. **Table cells** — `<td>&nbsp;</td>` is layout-bearing markup, not an empty paragraph, and must be kept. Pinned in Task 1.
4. **All-blank value** — a field containing only blank lines becomes `''`, not a stray newline. Pinned in Task 1.
5. **Repeatable / group CMB2 values arrive as arrays** — every string inside is stripped, non-strings untouched. Pinned in Task 3.

---

### Task 1: `nm_strip_empty_paragraphs()` helper

**Files:**
- Modify: `lib/functions-utility.php` (append)
- Create: `tests/unit/lib/StripEmptyParagraphsTest.php`

**Interfaces:**
- Produces: `nm_strip_empty_paragraphs( string $value ): string` — pure, no WordPress calls, idempotent.

- [ ] **Step 1: Write the failing tests** in `StripEmptyParagraphsTest` (namespace `NM\Tests\Lib`, extends `NM\Tests\TestCase`, `setUpBeforeClass` requires `lib/functions-utility.php`). One method per case, `assertSame( $expected, nm_strip_empty_paragraphs( $input ) )`:

| Test | Input | Expected |
|---|---|---|
| `test_trailing_nbsp_lines_are_removed` | `"One.\n\n&nbsp;\n\n&nbsp;"` | `"One."` |
| `test_leading_blank_lines_are_removed` | `"&nbsp;\n\nOne."` | `"One."` |
| `test_mid_content_nbsp_line_is_removed` | `"One.\n\n&nbsp;\n\nTwo."` | `"One.\n\nTwo."` |
| `test_empty_p_elements_are_removed_anywhere` | `"<p>One.</p>\n<p>&nbsp;</p>\n<p></p>\n<p>Two.</p>\n<p><br></p>"` | `"<p>One.</p>\n<p>Two.</p>"` |
| `test_empty_p_with_attributes_and_slashes_is_removed` | `"<p>One.</p><p class=\\\"x\\\">&nbsp;</p>"` | `"<p>One.</p>"` |
| `test_trailing_br_in_a_paragraph_is_removed` | `"<p>One.<br></p>\n\nTwo.<br />"` | `"<p>One.</p>\n\nTwo."` |
| `test_soft_br_inside_text_is_kept` | `"Line one<br>\nLine two"` | unchanged |
| `test_inline_nbsp_is_kept` | `"It is 10&nbsp;km away."` | unchanged |
| `test_oembed_url_line_is_kept` | `"One.\n\nhttps://www.youtube.com/watch?v=abc\n\nTwo."` | unchanged |
| `test_table_cell_nbsp_is_kept` | `"<table><tr><td>&nbsp;</td></tr></table>"` | unchanged |
| `test_nbsp_entity_variants_and_uppercase_tags` | `"One.\n\n&#160;\n\n<P>&#xA0;</P>\n\n\u{00A0}<BR/>"` | `"One."` |
| `test_crlf_line_endings` | `"One.\r\n\r\n&nbsp;\r\n\r\nTwo."` | `"One.\r\n\r\nTwo."` |
| `test_all_blank_value_becomes_empty_string` | `"&nbsp;\n\n<p></p>\n"` | `""` |
| `test_empty_string` | `""` | `""` |
| `test_empty_paragraph_blocks_are_removed` | two `wp:paragraph` blocks with text around one with `<p></p>` and one with `<p>&nbsp;</p>` | only the two text blocks, joined by `"\n\n"` |
| `test_other_blocks_are_untouched` | a `wp:group` containing `<p></p>`, a `wp:spacer` block, a `wp:html` block containing `&nbsp;` lines | unchanged |
| `test_is_idempotent` | data provider over every non-block input above | `f( f( x ) ) === f( x )` |

- [ ] **Step 2: Run to verify failure**

Run: `npm run test:php -- --filter StripEmptyParagraphsTest`
Expected: errors, "Call to undefined function nm_strip_empty_paragraphs()".

- [ ] **Step 3: Implement `nm_strip_empty_paragraphs( string $value ): string`** in `lib/functions-utility.php`, with a docblock stating what counts as blank and what is kept (Global Constraints).

Approach: build one blank-token pattern (`\s`, `&nbsp;`, `&#160;`, `&#xA0;`, U+00A0, `<br\s*/?>`, case-insensitive, `u` flag). If the value contains `<!-- wp:`, remove only `<!-- wp:paragraph[^>]*-->\s*<p[^>]*>BLANK*</p>\s*<!-- /wp:paragraph -->` and trim, then return. Otherwise, in order: remove `<p[^>]*>BLANK*</p>`; remove whole lines made only of blank tokens (multiline mode, line must contain at least one non-whitespace blank token or be empty); remove `<br>` runs that are followed by optional whitespace then `</p>`, a blank line, or end of string; collapse 3+ consecutive line breaks (`\r?\n`) to 2, keeping the value's own line-ending style; trim blank tokens from both ends. Table cells survive because the line rule only matches lines with nothing but blank tokens.

- [ ] **Step 4: Run to verify pass**

Run: `npm run test:php -- --filter StripEmptyParagraphsTest`
Expected: all pass. Then `npm run test:php` — whole suite green.

- [ ] **Step 5: Commit**

```bash
git add lib/functions-utility.php tests/unit/lib/StripEmptyParagraphsTest.php
git commit -m "feat: helper to strip empty paragraphs and blank lines"
```

---

### Task 2: Strip post content and excerpt on save

**Files:**
- Modify: `lib/functions-filters.php` (append)
- Create: `tests/unit/lib/FunctionsFiltersTest.php`

**Interfaces:**
- Consumes: `nm_strip_empty_paragraphs( string ): string` (Task 1)
- Produces: `nm_strip_empty_paragraphs_on_post_save( array $data ): array`, hooked `add_filter( 'wp_insert_post_data', 'nm_strip_empty_paragraphs_on_post_save' )`

- [ ] **Step 1: Write the failing tests** (`setUpBeforeClass` requires both `lib/functions-utility.php` and `lib/functions-filters.php`; stub `post_type_supports` with `Functions\when()`):
  - `test_post_content_and_excerpt_are_stripped_for_editor_post_types` — `post_type_supports` returns `true`; `$data = array( 'post_type' => 'notice', 'post_content' => "One.\n\n&nbsp;", 'post_excerpt' => "Ex.\n\n&nbsp;", 'post_title' => 'T' )` → content `"One."`, excerpt `"Ex."`, title unchanged.
  - `test_types_without_editor_support_are_untouched` — `post_type_supports` returns `false`; `post_type => 'oembed_cache'`, `post_content => "<p></p>"` → `$data` returned identical.
  - `test_hook_is_registered` — `assertNotFalse( has_filter( 'wp_insert_post_data', 'nm_strip_empty_paragraphs_on_post_save' ) )`. If Brain Monkey's hook registry is reset by `setUp`, assert with `Filters\expectAdded()` around a fresh `require` instead — pick whichever works and note it in the test docblock.

- [ ] **Step 2: Run to verify failure**

Run: `npm run test:php -- --filter FunctionsFiltersTest`
Expected: "Call to undefined function nm_strip_empty_paragraphs_on_post_save()".

- [ ] **Step 3: Implement `nm_strip_empty_paragraphs_on_post_save( array $data ): array`** — returns `$data` unchanged unless `post_type_supports( $data['post_type'], 'editor' )`; otherwise strips `post_content` and `post_excerpt`. Docblock notes `$data` is slashed and the patterns are safe on slashed input.

- [ ] **Step 4: Run to verify pass**

Run: `npm run test:php`
Expected: all pass.

- [ ] **Step 5: Manual check (local DevKinsta)** — classic editor: post with `Enter` presses between and after paragraphs → Update → reopen: blank lines gone; front end shows no empty `<p>`. Switch a draft to the block editor, add empty paragraph blocks mid and end → Save → reload: gone.

- [ ] **Step 6: Commit**

```bash
git add lib/functions-filters.php tests/unit/lib/FunctionsFiltersTest.php
git commit -m "feat: strip empty paragraphs from post content on save"
```

---

### Task 3: Strip CMB2 text fields on save + docs

**Files:**
- Modify: `lib/functions-filters.php` (append)
- Modify: `tests/unit/lib/FunctionsFiltersTest.php`
- Modify: `.github/copilot-instructions.md` (`### Meta` section)
- Modify: `CHANGELOG.md` (`## [Unreleased]`)

**Interfaces:**
- Consumes: `nm_strip_empty_paragraphs()` (Task 1)
- Produces: `nm_cmb2_strip_empty_paragraphs( $override_value, $value, $object_id, array $field_args, $sanitizer )` returning the stripped sanitised value; registered for each of `text`, `text_small`, `textarea`, `textarea_small`, `wysiwyg` via `add_filter( "cmb2_sanitize_{$type}", 'nm_cmb2_strip_empty_paragraphs', 10, 5 )`.

- [ ] **Step 1: Write the failing tests** — `$sanitizer` is an anonymous-class stub whose `wysiwyg()` / `text()` methods return a fixed string (stands in for CMB2's default sanitiser):
  - `test_cmb2_value_is_default_sanitised_then_stripped` — stub `wysiwyg()` returns `"Desc.\n\n&nbsp;"`; `$field_args = array( 'type' => 'wysiwyg' )`; override `null` → `"Desc."`.
  - `test_cmb2_existing_override_passes_through` — override `'kept'` → `'kept'`, stub method never called.
  - `test_cmb2_array_values_are_stripped_deeply` — stub returns `array( "A.\n\n&nbsp;", array( 'k' => "<p></p>B." ), 5 )` → `array( 'A.', array( 'k' => 'B.' ), 5 )`.

- [ ] **Step 2: Run to verify failure** — `npm run test:php -- --filter FunctionsFiltersTest`, expect undefined function.

- [ ] **Step 3: Implement** — if `$override_value` is non-null return it; else `$clean = $sanitizer->{ $field_args['type'] }()` (CMB2's default for that type), then strip every string in `$clean` recursively (own small recursion or `map_deep` with an `is_string` guard; if `map_deep`, alias it in tests). Docblock explains the short-circuit and why the default sanitiser is called first. Register the five filters in a `foreach` over the type list.

- [ ] **Step 4: Run to verify pass** — `npm run test:php`, all green.

- [ ] **Step 5: Docs**
  - `.github/copilot-instructions.md` under `### Meta`: rule — post content, excerpts and CMB2 text fields are normalised on save by `nm_strip_empty_paragraphs()`; don't add template-level trims for blank lines; don't rely on editor-inserted blank lines for spacing, use CSS. Anti-example: `echo wpautop( trim( preg_replace( '/(&nbsp;\s*)+$/', '', $short_desc ) ) );` in a template.
  - `CHANGELOG.md` Unreleased → `### Changed`: `- Empty paragraphs and blank lines are stripped from post content, excerpts and text meta fields on save`

- [ ] **Step 6: Manual check** — edit a post's short description with trailing `Enter`s, Update, reopen: gone. Same on a Links Bar options page text field and a group sub-field (e.g. About page).

- [ ] **Step 7: Commit**

```bash
git add lib/functions-filters.php tests/unit/lib/FunctionsFiltersTest.php .github/copilot-instructions.md CHANGELOG.md
git commit -m "feat: strip empty paragraphs from CMB2 text fields on save"
```

---

### Task 4: One-off backfill script + post-deploy step

**Files:**
- Create: `scripts/one-off/2026-10-strip-empty-paragraphs.php`
- Modify: `docs/post-deploy-checklist.md` (`## Unreleased`, next step number)

**Interfaces:**
- Consumes: `nm_strip_empty_paragraphs()` (Task 1); runs under `wp eval-file`, so the theme and CMB2 are loaded.

- [ ] **Step 1: Write the script**, docblock in the style of `scripts/one-off/2026-09-season-episode-backfill.php` (purpose, usage, safe to re-run). Behaviour:
  - `$nm_apply = in_array( 'apply', $args, true );` — dry run otherwise.
  - Posts: post types from `get_post_types()` filtered by `post_type_supports( $type, 'editor' )`; all statuses except `auto-draft`, `inherit`, `trash`; batch with `get_posts( fields => ids, posts_per_page => 200, paged )` / raw `$wpdb` select. For each changed `post_content` / `post_excerpt`: apply via `$wpdb->update( $wpdb->posts, … )` then `clean_post_cache( $id )` — never `wp_update_post` (would bump `post_modified`).
  - Meta: walk `CMB2_Boxes::get_all()`; for each field of the five types (and group fields' sub-fields), read and rewrite with `get_post_meta` / `update_post_meta`, `get_term_meta` / `update_term_meta`, or `cmb2_get_option` / `cmb2_update_option` by the box's `object_types` / `option_key`. Group values: strip each sub-field key of a target type.
  - Output: per-field changed counts; then `ID<TAB>post_type<TAB>title` for each changed post; `WP_CLI::success()` summary with "(dry run)" when not applying.
- [ ] **Step 2: Verify locally** — `wp eval-file scripts/one-off/2026-10-strip-empty-paragraphs.php` on the DevKinsta DB: lists changes, writes nothing (`wp post get <id> --field=post_content` unchanged). Run with `apply`, then re-run dry: zero changes (idempotence). Check one changed post's `post_modified` is unchanged.
- [ ] **Step 3: Post-deploy checklist step** — after deploy: dry run, open ~10 listed posts on the front end and check spacing, then `apply`; note "watch the QA bot channel for a week — any new trailing-blank-lines hit means a write path this doesn't cover". Same commands block format as step 2 of the existing checklist.
- [ ] **Step 4: Commit**

```bash
git add scripts/one-off/2026-10-strip-empty-paragraphs.php docs/post-deploy-checklist.md
git commit -m "chore: one-off backfill to strip existing empty paragraphs"
```

---

### Wrap-up (not a task)

- Move this plan to `docs/plans/archive/` in the PR's final commit.
- Ask before pushing. PR against `development`; staging backfill dry run + spot-check before merge.
