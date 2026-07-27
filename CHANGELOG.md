# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- Job posting structured data locations reduced to city and postcode-district level, and Leeds office moved to Mabgate Mills

## [4.8.0] - 2026-07-27

### Added

- All header and footer menus user-editable via Appearance → Menus, with fallbacks rendering the default links when no menu is assigned
- Required validation on post meta fields on publish and update — standfirst and short description on all posts, YouTube ID on video, Soundcloud URL on audio (classic editor only — block editor support to follow)
- Do Your Own Research product block available in the front-page Layout editor on production
- In-development front-page blocks flagged "[DEV ONLY]" in the Layout editor dropdown

### Fixed

- About page names render editor-entered links instead of escaping them to literal text

## [4.7.0] - 2026-06-24

### Security

- Compare parsed referer host to `$_SERVER['HTTP_HOST']` via `wp_parse_url()` for video autoplay check — prevents spoofing via host name in referer query/path (`partials/singles/single-post-video.php`)
- Replace `extract()` with explicit variable assignments in custom gallery shortcode (`lib/custom-gallery.php`)
- Output escaping hardening across article headers, audio post, event archive, quote component, and video title renderer: `esc_html`/`esc_url` for plain-text and URL fields; `esc_html` for quote copy and standfirst (both plain-text fields, no HTML expected); `wp_kses` without anchors for support box content (prevents nested `<a>`); `rel="nofollow noopener noreferrer"` on podcast subscribe, Google Maps, and Resonance FM external links

### Added

- Front page Layout editor — order banners and product blocks in one sortable admin list (Front Page > Layout); falls back to the historic order until a layout is saved
- ACFM standalone front-page full-width product block
- Related Post Gutenberg block — search for posts or events in editor; renders type-specific layout on frontend
- Short URL rewrite from `/dyor` to the Do Your Own Research show page
- Environment gating for in-development front-page blocks via new `nm_is_production()` helper — show on local/development/staging, hide on production

### Changed

- Consolidate rounded-corner CSS helpers: introduce `ui-rounded-box--nested` (4px) for nested inner colour-blocked elements; replace `ui-rounded-image` with `ui-rounded-box` throughout templates

- Update frontend dependencies: jQuery 3→4, @wordpress/scripts 27→32, Cypress 13→15, cssnano 7→8, postcss-preset-env 10→11, webpack-cli 6→7
- Share link rendering consolidated into single `render_share_link( $platform, $url, $args )` function, replacing the per-platform share helpers

### Deprecated

- Legacy front-page banner selects — superseded by the Layout editor; retained only to seed the default order. Migration: after deploy, open Front Page > Layout and Save once

### Removed

- Unused `job` post-type archive (`has_archive` now false) — the public jobs listing is the `/about/jobs` Page; the `/job/` archive was an unfiltered, stale duplicate. Requires a permalink flush after deploy (Settings > Permalinks > Save)

### Fixed

- Doubled navigation arrows in products bar carousel — Swiper v12 auto-injects SVG icons (`addIcons: true` default) alongside existing `.ui-chevron` spans; disabled via `addIcons: false`
- XSS: escape standfirst, short description, post UI tag attributes, about-page group fields, resources row, and legacy author meta output
- Share links missing `rel="noopener noreferrer"` (tabnabbing)
- Fundraising options separator field had duplicate CMB2 id, preventing support-section heading from saving
- CI deploy self-heals when the staging theme repo's `.git` is missing, re-cloning automatically instead of failing every run
- Job pages served stale open/closed states after deadlines passed; `Cache-Control` / `Expires` headers now cap the shared-cache TTL so caches revalidate at midnight on the deadline date for single job pages and nightly for the jobs archive (`lib/functions-hooks.php`)
- OG/Twitter meta tags used `value=` instead of `content=`, silently dropping Twitter cards and `fb:app_id`
- Feature-detect script had `typo="text/javascript"` attribute; browsers ignored the AVIF/WebP detection entirely
- Schema.org `validThrough` on job posts emitted `1970-01-01` when no deadline was set; field now only added when a deadline exists
- `render_show()` in audio partial declared at file scope without guard, causing fatal redeclare on second include
- Path traversal: `_cmb_article_layout` post meta now validated against known layout values before use in `get_template_part()`
- Code tidy and refactor: product-bar card spacing, Reddit/Facebook share-URL formatting, front-page banner `switch`→`if/elseif`, favicon `rel`, and Novara Live `font-size` class casing

## [4.6.1] - 2026-05-13

### Changed

- Update sticky support bar copy and layout for second round of May 2026 fundraiser campaign
- Expand sticky support bar admin options with separate heading fields for mobile open state and desktop closed state

### Fixed

- Purge contributor pages from Kinsta and Cloudflare cache on post publish (including `?is_full_archive=true` variant)
- Include contributor, event, and job post types in site search results

## [4.6.0] - 2026-05-06

### Changed

- Reorganise developer docs into `docs/` directory structure; update AI coding guidelines and trim `CLAUDE.md`

- Update support page copy and layout for May 2026 fundraiser campaign
- Replace hard-coded `'j F Y'` date literals with `NM_DATE_FORMAT_LONG` constant (#499)
- Update DIW podcast urls (except the plain RSS one)

### Fixed

- Fix schedule tab active state when pre-selected via URL parameter

## [4.5.5] - 2026-04-27

### Added

- IMPRESS logomark to footer with link to complaints page

## [4.5.4] - 2026-04-24

### Added

- `noindex, follow` on internal search result pages (meta tag + HTTP header)

### Fixed

- Header menu spacing on mobile
- Empty News category no longer breaks front page above-the-fold render
- YouTube embeds failing to load in Safari
- YouTube embed iframes missing accessible `title` attribute
- Release `after:release` hook failing on Linux CI (`sed` → `perl` for cross-platform in-place edit)

### Removed

- Remove PayPal donation link section on Support page

## [4.5.3] - 2026-04-08

### Changed

- Front page latest articles column (above the fold) now shows only News posts

### Fixed

- Survey 2026 banner link pointing to wrong domain

## [4.5.2] - 2026-03-26

### Added

- Per-post Figma node ID meta box for DYOR episodes
- Audience Survey 2026 front page banner

### Changed

- DYOR map config replaced: old embed URL field removed, now uses Figma file key + default node ID

### Fixed

- Mobile on-scroll header title truncates with ellipsis when text overflows

## [4.5.1] - 2026-03-17

### Added

- Do Your Own Research category archive template

### Changed

- Modernised 404 page with search form pre-filled from failed URL path
- Refactor redirects and rewrites into single data-driven file (`lib/functions-rewrites.php`)
- Use correct post layout (with UI tags) on Articles and other category archives and search/tag index (`category.php`, `index.php`)
- Migrate all remaining category/taxonomy archives to archive-post layout (ACFM, If I Speak, Focus, Breaking Britain)
- Consolidate video embed into archive-post via `show-video-embed` flag

### Fixed

- Archive post partial layout creep from non-block image and link elements

### Removed

- /asksophie redirect as it should be a Bitly
- Legacy post layout partials (flex-post, flex-video-embed-post, post-col6, post-col8)

## [4.5.0] - 2026-03-07

### Added

- Death in Westminster podcast support (archive, banner, rewrites)
- Deploy to Staging GitHub Actions workflow for manually deploying any branch to Kinsta staging persistently
- `paths-ignore` filter on Cypress workflow to skip test runs for non-frontend changes (docs, workflows, config files)

### Changed

- Optimise inline newsletter WP block data flow
- PHPCS config: added Claude hook for automated lint enforcement
- Non-interactive release script (`scripts/release.sh`) for automated versioning and PR creation

## [4.4.0] - 2026-02-09

### Added

- Cypress end-to-end testing with 8 test suites, CI via GitHub Actions, and `data-testid` selectors across PHP templates
- Wordpress Block for inline newsletter signup forms

### Changed

- Updated nm-stylus-library (with pure Helv Neue sans fonts)
- Update latest articles front page column to support News type posts with new image and layout logics
- Updated ACFM archive page for better newsletter signup integration and newer header style
- Updated the Downstream archive page for better newsletter signup integration and newer header style
- Refactored stylus files for max DRY

### Fixed

- Fix duplicate featured posts appearing above the fold when theme option slots are empty — `intval()` normalisation converted unset values to `0` before the fallback loop could fill them

### Removed

- Old style 12 col grid, old style margin and padding helpers, kouto swiss

### Fixed

- Fix duplicate featured posts appearing above the fold due to type mismatch (int vs string) in `in_array` comparison

## [4.3.1] - 2026-01-15

### Changed

- Improved line breaking for titles on homepage above the fold
- Removed Yarn from repository, standardized on NPM as single package manager

### Fixed

- Fixed featured post duplication by filtering non-numeric values from exclusion arrays and adding explicit `post_status` filter
- RSS feed title and post author incorrect values
- Strip tags from page title html

## [4.3.0] - 2026-01-07

### Added

- Add Github Action workflow to notify to Slack on releases
- Additional SEO title content. Downstream shows standfirst. Opinion, Features & Analysis show the author. Complete override option available via meta box.
- .avif and .webp generation via build script
- Push custom metadata (authors, standfirst, reading age) to GTM dataLayer via GTM4WP plugin integration
- Click to copy post ID column in admin views

### Changed

- Replace HTTP_HOST environment checks with wp_get_environment_type() for improved reliability
- Rebuild the support form, with new condensed width version
- Updated all dev and front end dependencies (`chalk` pinned at v4)
- Migrate the .js-fix-widows helper into a css utility class based progressive enhancement based on text-wrap
- Migrate newsletter functionality to custom post type not page templates
- Integrated nm-stylus-library 0.12.0-RC
- Events views quickly updated to design system
- Updated YouTube embed generation to support modern Safari and use helper
- Use localstorage not cookies for non-identified functional browser prefs
- Very basic jobs views design update
- Lazyload SoundCloud players
- Update design and copy on Support & How We Are Funded pages

## [4.2.10] - 2025-05-22

### Added

- Function that accepts an array to support multiple external redirects to handle simple path-based redirects.
- Redirect from https://novaramedia.com/asksophie to this google form https://docs.google.com/forms/d/e/1FAIpQLSegJV5jED2FhIUS7_rryZC6V2Y65W7W-kE3tXGMH7zr4sl_uQ/viewform

## [4.2.9] - 2025-05-21

### Added

- Redirect to red-flags category https://novaramedia.com/redflags to https://novaramedia.com/category/articles/red-flags/

## [4.2.8] - 2025-04-30

### Fixed

- Increased the margin under the committed archive post title

## [4.2.7] - 2025-04-21

### Added

- Committed (podcast series) archive page and banner

## Changed

- podcast_series_pre_get_posts() to look at series categories array
- render_ui_tag() to render the UI tags

## [4.2.6] - 2025-01-13

### Fixed

- If I Speak title onto one line and image to contain rather than cover

## [4.2.5] - 2025-01-13

### Fixed

- Added a margin to the bottom of the heading on larger screens to stop it overlapping text below

### Changed

- included NM stylus library

## [4.2.4] - 2024-12-02

### Changed

- Timestamp for job posts so they remain visible until 23:59:59 on deadline day
- Removed the lines that told applicants to email in their application

### Added

- Support video section
- Config for PHPCS that uses WP Standards with some of our own styles and a little more tolerance

### Fixed

- nm_is_articles() supports posts where Articles hasnt been selected but a child category has

## [4.2.3] - 2024-11-01

### Fixed

- Uncaught false returns with get_the_sub_category()

## [4.2.2] - 2024-10-14

### Changed

- Increase root type size to 16px
- Have only 1 heading type size for latest articles section above the fold

### Fixed

- Incorrect letter spacing values on new type tooling

## [4.2.1] - 2024-09-30

### Fixed

- Added styles for articles headings to fix missing function from type styles migration

## [4.2.0] - 2024-09-22

### Added

- Hardcoded temporary apology notice. To automatically hide after term. Can potentially be recycled in future.
- Wordpress core cache auto-flushed when front page options are updated. This should ideally improve cache busting for changing e.g. featured posts above the fold

### Changed

- Refactor typography declarations to separate sizes from weights and fonts
- Refactor stylus to use nm-stylus-library via packages

### Fixed

- Video titles with standfirsts in same line now use a renderer and don't display stray full stops

## [4.1.1] - 2024-07-01

### Added

- Ability to embed video in 1st primary featured post above the flow. For electionsesh type events

### Changed

- Refactor See Also display to renderer function
- Refactor primary featured component to show wider titles
- Newsletter page less colorful and now ordering child pages by menu_order. Also forces Cortado to have black background regardless of position

## [4.1.0] - 2024-06-24

### Added

- Highlight module that can be used to show recent content from a specific Section

### Changed

- Primary featured posts will not have huge titles if the titles are more than 14 words long
- Increase hit size on menu toggle nav elements, especially on s size
- Use UI Tag in place of h4 text on single post

### Fixed

- Hack !important fix for overbolding on mobile—will be clean with type classes update
- Primary featured component less likely to show empty space with unset related posts but display meta set to show

## [4.0.1] - 2024-06-17

### Changed

- Show post tags on single related posts block
- Allow paragraph breaks on description of latest post on NL front page block
- Update support bar markup for new grid and type

### Fixed

- archive-post.php link nesting issue
- render_short_description() not applying content filters

## [4.0.0] - 2024-06-10

### Added

- New type system
- New grid system
- New UI set
- New UX components
- Front page Audio product blocks
- Front page Novara Live block
- Front page Downstream block

### Changed

- Above the fold completely refactored
- Site options meta refactored
- Quick links bar becomes Product bar
- Header refactored with new menus
- Email signup refactored

### Updated

- Webpack deps

## [3.14.0] - 2024-02-01

### Added

- If I Speak basic archive template and front page banner
- Perf: preload support texture
- Perf: eager load featured post thumbs on front page

## [3.13.0] - 2023-09-29

### Added

- ACFM specific show page template (basic first version)

### Changed

- Use lazysizes to lazy load YouTube embeds rather than own solution
- Extend time til support bar reoopens to 21 days

## [3.12.0] - 2023-06-23

### Added

- Feature detect support for avif and webp

### Changed

- Use webp assets for support texture backgrounds
- Optimise assets for Breaking Britain & Foreign Agent
- [video-caption] shortcode with utility TinyMCE button
- Support bar copy now driven by meta set in the fundraising panel
- Style to allow the_content <figure>s to be thinner than their container and have the caption fit their width
- YouTube embeds use no-cookie domain as default

### Removed

- Hoisted support bar for fundraisers feature

## [3.11.0] - 2023-06-09

### Added

- Support bar at bottom of page. Has open and closed state that can persist via cookie if allowed. All copy hardcoded
- Utility function ot use file_get_contents when possible and fallback to old technique

### Changed

- Header loses the black marble (wow) and gets tighter
- Support page gets a fresh skin
- And the support video banner gets a fresk skin too
- Cookie approval functionality and layout gets improved and renamed
- Button style gets tweaked, some ui/ux utility classes added

## [3.10.1] - 2023-05-08

### Added

- Pages can now set custom short descriptions that display in contexts like opengraph meta or search layouts

### Fixed

- Fix critical error in seo.php when archive page has no custom description set
- Author pages (WP users not our Contributors) force redirected to home.
- oEmbeds of posts no longer display and link to the WP user who posted them

## [3.10.0] - 2023-03-27

### Added

- Support Section default and url code alternate values can now set one off as the default
- CMB2 meta field basic validation. Can validate required and max words via data attributes

### Removed

- The Pick and The Cortado single page templates

## [3.9.0] - 2023-02-03

### Added

- Newsletter page template with all meta field settings
- Auto list all newsletters as signup banner options
- URL rewrites for /tyskysour and /novara-live to Novara Live category archive

### Changed

- Newsletter page auto lists all child pages with signup forms
- TyskySour functionality cloned and renamed Novara Live (non breaking. TyskySour code to be removed after migration)

### Deprecated

- The Pick and The Cortado single page templates
- Hardcoded newsletter signup banner options for The Pick and The Cortado
- TyskySour archive template

## [3.8.1] - 2022-11-21

### Added

- Utility function to lazy load Youtube embeds
- Banner for 'Pro Revolution Soccer' Focus
- Navigation menu for section custom taxonomy archives

### Changed

- Update default podcast follow url to Podfollow
- Render 2 possible quotes on Focus taxonomy archive and improve display styling

### Fixed

- oEmbed only fixes ratio for videos from Youtube or Vimeo

## [3.8.0] - 2022-10-21

### Added

- Page template for article style content with CTA link buttons (for use for supporter comms primarily)

### Changed

- Only first Above The Fold audio post has a thumbnail and it's no longer cropped
- Support form autovalues now set via Site Options: Fundraising Options metadata
- Post Contributor selector metafield now adds not replaces existing value as to allow search for multiple contribs

### Fixed

- Fix Above The Fold article byline rendering

## [3.7.1] - 2022-07-19

### Changed

- Refactored footer layout to use more WP Nav Menus
- Refactored support copy using a site option panel to make more copy editable

## [3.7.0] - 2022-07-01

### Added

- Contributors post type and association to posts
- Auto short bio on articles with associated contributor

### Changed

- Foreign Agent archive copy changes

### Fixed

- Foreign Agent query hook targeting

## [3.6.1] - 2022-05-24

### Changed

- Minor edits to Foreign Agent archive

## [3.6.0] - 2022-05-24

### Added

- Foreign Agent: Category archive, banner & conditional routing from single.php to archive

## [3.5.0] - 2022-05-20

### Added

- Support video banner
- Support banners at top of archive page when front page banner is turned on

## [3.4.0] - 2022-05-16

### Added

- (temp) Nav menus registered and rendered for the 3 top level categories and visible on the archive pages.

### Changed

- 2022 support page

## [3.3.1] - 2022-05-11

### Fixed

- JS error in Support module

## [3.3.0] - 2022-05-02

### Added

- CHANGELOG.md!
- Support Section optional autovalues loaded via private query param codes.

### Changed

- The Cortado page template to match The Pick style.
- Webpack update and refactor: `yarn build` now explicitly needed to generate optimized image assets, min Node version is v16.

## [3.2.17] - 2022-04-19

### Changed

- Iteration of TyskySour archive page
- Improved display of no posts layout
- Improved display of captions inside the_content

### Fixed

- Thumbnails in admin list view overflowing
- Critical error on index.php!
