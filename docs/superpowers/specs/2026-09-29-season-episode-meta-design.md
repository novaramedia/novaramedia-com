# Season / episode post meta

**Date:** 2026-09-29
**Status:** Agreed design, pre-implementation
**Branch:** `feature/season-episode-meta` (one PR against `development`)

## Problem

Some content is episodic: the capsule podcasts (Committed, Foreign Agent, Death in Westminster) and Do Your Own Research, which is now seasonal. The site has no way to record "episode 7 of season 2". The capsule archives fake it by typing "Episode 1" into the standfirst field and rendering the standfirst as an episode label, so those posts have no real standfirst and the numbering can't be queried.

The next DYOR page needs season 2 in its own block above season 1's. That needs data a template can group by.

## Decisions (agreed 2026-09-29)

1. **Two integer meta fields, no taxonomy.** A season is a number. Named or themed seasons are possible later but not built now; this is one product of a few, not a core site feature.
2. **Available on every post.** Not only AV: a scope-limited articles series can use it too. Blank fields change nothing anywhere.
3. **The show is the category.** "Season 2 of what" comes from the post's show category, as today. No field for it.
4. **An episode always has a season in the data.** Committed is season 1, episodes 1–4. Whether a template prints "Season 1" is a display choice. The same holds for a label override: a bonus is still in a season. A season with neither episode nor label is allowed.
5. **No shared label helper.** Each show's template formats the numbers its own way (`S01E14`, "Episode one", capitalised or not). This feature provides data only.
6. **Non-numbered posts get a label override.** Trailers, bonuses and extras ("Bonus 1", "The producers", "Credits") have no episode number but still need a label. A separate optional text field holds it; when set, templates show it instead of the number.
7. **Order by date, never by episode number.** Bonuses sit between numbered episodes, so publish date is the only order that holds for every post. Episode numbers are for display and grouping only.

## Components

### 1. Meta fields

New file `lib/meta/meta-boxes-post-episode.php`, loaded alongside the other `lib/meta/` boxes in `functions.php`.

- CMB2 box on `post`, `context 'side'`, title "Season / Episode", no `show_on_cb`.
- Fields `_nm_season` and `_nm_episode`: `text` type with `type="number"`, `min="1"`, `step="1"` attributes.
- `sanitization_cb` stores a positive integer as a string, or deletes the value when blank or invalid. Non-numeric input is never stored.
- Field `_nm_episode_label`: plain `text`, sanitised with `sanitize_text_field`, blank deletes. For posts that are not a numbered episode ("Bonus 1", "Trailer", "Credits"). If both number and label are set, templates show the label.
- Field descriptions tell editors: leave all blank unless this post is part of an episodic series; a season is required whenever an episode number or label is set.

### 2. Validation

Extend the NM fork `lib/meta/cmb2-validation.php` with one new rule:

- `data-validation-required-with="<field id>[,<field id>…]"`: this field is required when any of the named fields has a value.

`_nm_season` carries `data-validation-required-with="_nm_episode,_nm_episode_label"`. Same publish-only behaviour as the existing rules (Save Draft and Preview save freely). The rule is generic, not season-specific. Bump the fork's version and document the rule in its header.

Classic editor only, like every existing rule. See the block-editor gap already recorded in that file's header.

### 3. Reading and querying

Templates read values directly with `get_post_meta( $id, '_nm_season', true )` and cast to `int` (label read as a string). No accessor function unless the capsule template work shows the same few lines repeated enough to warrant one.

Query patterns (documented in the spec, not wrapped):

- **Group a show by season:** one query for all posts in the category (`posts_per_page => -1`, date order), group in PHP by `_nm_season`. Show post counts are small. Posts with no season go in an unnumbered group; each template decides whether to render it.
- **One season:** `meta_query` on `_nm_season` = n with `type => NUMERIC`, still ordered by date.

Never `orderby => meta_value_num` on `_nm_episode`: bonuses and trailers have no number and would be dropped from the results, and would sort wrongly if they weren't.

### 4. Capsule podcast retrofit (same PR)

Committed, Foreign Agent and Death in Westminster:

1. **Sample first.** Pull the current standfirst values for every post in the three categories from production or staging and record them in the PR. This sizes the job and preserves the old strings.
2. **Data fix by hand**, not code (no parsing of standfirsts in templates). Set `_nm_season = 1` on every post, `_nm_episode = n` on numbered episodes, and `_nm_episode_label` on the rest (live today: Foreign Agent's "Bonus 1", "Bonus 2", "The producers", "Credits"). The season/episode/label meta can be written with WP-CLI **before** the deploy (it is plain post meta, no code needed to store it), so the new templates find it on first render. Replace each standfirst with a real one, or leave it for editorial if no text exists. Script it with WP-CLI if the count warrants it. Either way it is a one-time step recorded in `docs/post-deploy-checklist.md`.
3. **Template markup.** Replace the `_cmb_standfirst` `<h4>` in `category-committed.php:254`, `category-foreign-agent.php:183` and `category-death-in-westminster.php:267` with the label override if set, else a label built from the episode number, in each show's own wording. The labels must render identically to today's once the data fix lands. Decide in the PR whether the real standfirst now appears on the archive at all.
4. **Deploy ordering.** Meta first (pre-deploy), then deploy, then standfirst rewrites. Templates guard anyway: a post with neither episode nor label prints no `<h4>`.

DYOR season 1: set `_nm_season = 1` and `_nm_episode = n` on existing DYOR posts as part of the same data step. The DYOR page redesign is **out of scope** (Notion card "Webpage DYOR series 2 — season split"). This feature unblocks it.

### 5. Serial podcast config (consider in the same PR)

Two hardcoded slug lists exist and have drifted:

- `lib/functions-hooks.php:157` `podcast_series_pre_get_posts`: `foreign-agent`, `committed`. Makes the archive show every post, oldest first.
- `lib/functions-custom.php:113` `nm_serial_podcast_redirect`: the same two plus `death-in-westminster`. 301s single posts to the archive anchor.

So Death in Westminster's archive runs the default query (newest first, paged) while its single posts redirect to the archive. At minimum:

- Put both lists behind one source of truth, e.g. `nm_get_serial_podcast_slugs()`, and include `death-in-westminster` in the query hook if its archive is meant to list every episode oldest first. Confirm the intended order against the live page before changing it.
- Keep the hook's date ordering (decision 7).

## Out of scope

- Label formatting helpers.
- Season names, descriptions, artwork, or a season taxonomy.
- The DYOR archive redesign.
- Block editor validation (separate follow-up, `feature/block-editor-meta-validation`).

## Testing

- `php -l` on touched PHP.
- Manual wp-admin:
  - Episode without season blocks Publish and allows Save Draft.
  - Season alone publishes.
  - Blank both publishes.
  - Label without season blocks Publish.
  - `0`, `-1` and `abc` never store in the number fields.
- Playwright:
  - Capsule archives render one label per post, matching today's strings (Committed: Episode 1–4; Death in Westminster: Episode 1–6; Foreign Agent: Episode 1, 2, 3, Bonus 1, Episode 4, 5, Bonus 2, Episode 6, The producers, Credits).
  - Death in Westminster lists all episodes if the hook change lands.
  - Existing smoke tests stay green.
- Post-deploy checklist step verified on production after the data fix.

## Changelog

Terse entry under `[Unreleased]`: season and episode numbers can be set on any post, and capsule podcast archives now use them.
