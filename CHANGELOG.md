# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
