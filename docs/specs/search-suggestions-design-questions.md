# Search suggestions: open design questions

**For:** the design pass on search suggestions (PR #632).
**Status:** open. Nothing here is decided.

## What this is

When someone searches the site, the results page now shows **Suggested**
cards above the normal results. They point to shows (category archives),
newsletters and fixed parts of the site (shop, events, jobs, newsletters,
support). Before this, searching "The Cortado" returned articles that
mention it, never the show itself.

The cards work, but they are a prototype and not ready to show readers. The
questions below are for design. Each one says what the page does today.

**To see it:** search staging, https://stg-novaramediacom-staging.kinsta.cloud/,
for a show name such as "the cortado", "dyor", "downstream" or "novara live".
Staging only shows this work while the branch is deployed there, so check
with Patrick first.

The full technical spec is in
[2026-10-01-search-suggestions-design.md](2026-10-01-search-suggestions-design.md);
designers don't need it.

## Questions

### 1. What a suggestion card looks like when there is no logo

Today the card shows, in order of preference: the product logo on a grey
tile, then the category's share image (or a newsletter's featured image),
then a red tile with the name in large white type.

Most categories have neither a logo nor a share image, so most cards are the
red tile. The red tile also repeats the name that sits under the card.

- Logo-led cards are the agreed direction. Which categories should get a
  logo, and what should the rest look like?
- Categories that are not products (News, Opinion, Analysis, Report) will
  never have a logo. Do they need a different treatment, or should they not
  be suggested at all?
- Is a photo (the share image) ever right here, or should cards always be
  logo or tile so they never read as a post?

### 2. Logo tile colour

The logo sits on a flat mid-grey box. The grey is a placeholder.

- One fixed colour for every product, or a colour per product?
- If per product, where does the colour come from (picked per category in
  the admin, or taken from existing brand colours)?

### 3. The corner tag

Post cards carry category tags in the image corner, so suggestion cards do
too. Today:

- A category shows its top-level category: "Video", "Audio" or "Articles".
  Do Your Own Research reads "Video".
- A newsletter shows "Newsletter".
- The shop, events, jobs, newsletters and support links show "Section".

"Show" and "product" are deliberately not used: the site never says "show",
and "product" is an internal word. Is "Section" the right word for the fixed
links, and is the top-level category the most useful tag for a show?

### 4. Shows with the same name in two places

Five names exist twice, once under each of two top-level categories:

| Name | Under | Under |
|---|---|---|
| Analysis | Articles | Video |
| Downstream | Video | Audio |
| Interview | Articles | Video |
| Report | Articles | Video |
| The Bastani Factor | Video | Audio |

Searching one of these shows two cards with the same title, told apart only
by the corner tag. Some of the second copies hold very few posts (the audio
Downstream and the video Analysis have one each). Options: keep both and make
the difference clearer, show only the larger one, or tidy the categories
themselves (an editorial decision, not a design one).

### 5. How many cards, and how much they push results down

Up to six suggestions show above the results, in the same grid as post cards:
three across on desktop, one per row on mobile. On a phone, six suggestions
push the first actual result several screens down.

Short or common queries fill all six slots with loose matches. "the" suggests
every show whose name contains "the". Partial words also match, so a reader
typing "nov" sees Novara Live.

- Is six right, or fewer, or fewer on mobile?
- Should suggestions be smaller than post cards (a compact row or list)
  rather than full cards, so they read as shortcuts rather than content?

### 6. Card text

Under the image: the name, then the category description or newsletter
excerpt, cut to 24 words. 35 of the 50 categories in use have no
description, so most cards are image and name only. Is that fine, or should
something else fill the space (post count, latest episode date)?

### 7. The "Suggested" heading and the search box

The results page now opens with a search box pre-filled with the query, then
a "Suggested" heading over the cards, then a "Results" heading over the
posts. Both headings use the existing small bold heading style, and the
search box reuses the header search styling. Do these need their own design?
