# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
