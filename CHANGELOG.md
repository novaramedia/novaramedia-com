# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- Only first Above The Fold audio post has a thumbnail and it's no longer cropped
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
