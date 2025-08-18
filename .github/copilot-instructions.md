# Novara Media WordPress Theme - AI Coding Guidelines

## Project Architecture

This is a **modern WordPress theme** for novaramedia.com using a **modular JavaScript architecture** with Webpack build system and Stylus CSS preprocessing and the https://github.com/novaramedia/nm-stylus-library design system.

### Core Structure
- **Entry Point**: `src/js/main.js` - imports all modules and initializes the `Site` class
- **Module Pattern**: Each feature is a class in `src/js/modules/` (Header, Support, Analytics, etc.)
- **Utilities**: Reusable functions in `src/js/functions/` (e.g., `selectText.js`, `isNonEmptyString.js`)
- **WordPress Integration**: `functions.php` loads `dist/main.js` with localized `WP` global object

### JavaScript Patterns

**Module Structure**: All modules follow this pattern:
```javascript
export class ModuleName {
  constructor() { /* setup properties */ }
  onReady() { /* DOM ready logic */ }
  bind() { /* event listeners */ }
}
```

**Utility Functions**: Create standalone functions in `src/js/functions/` and import them:
```javascript
import isNonEmptyString from '../functions/isNonEmptyString.js';
```

## Build System

- Do not attempt any changes to the build system without consulting the team.
- Do not run any release commands without prior approval.

### Commands
- `dev` - Development build with watch mode
- `build` - Production build with minification
- `release` - Automated versioning (don't commit/tag/push during process)

### Release Process
1. Pull `development` branch
2. Run `release` action
3. **Don't commit during release-it prompts**
4. After scripts complete, commit as `Build: x.x.x`
5. Create PR to `master` with changelog entries

### Webpack Configuration
- **Entry**: `src/js/main.js` (imports `src/styl/site.styl`)
- **Output**: `dist/main.js` and `dist/main.css`
- **CSS**: Stylus → PostCSS → CSS extraction
- **Modern JS**: Babel with `@babel/preset-env` and CoreJS polyfills

## CSS/Stylus Design System Architecture

The basis of the design system is a library of utility classes uses across all our projects. This is found at `node_modules/nm-stylus-library/` or [nm-stylus-library](https://github.com/novaramedia/nm-stylus-library).

### Grid System

- **24-column grid**
- **Responsive breakpoints**: xxl/xl/l/m/s with different container widths
- **Responsive offsets**: Utility classes for controlling offsets (e.g., `.offset-xxl-*`)
- **Class ordering for grid items**: First `.grid-item` then the grid size classes in descending order from xxl downwards. Any offsets should come before the grid size classes for that breakpoint.

### Class ordering

Follow a consistent order for class properties.

- Start with BEM descriptive identifier first (if needed)
- Then grid and layout
- Then spacing or display utilities
- Then typography
- Then color

### Extending the library

Sometimes the library is missing features, or sometimes designs add new features. In this circumstance we write the new styles in the `upstream-to-library.styl` file. During the release flow these styles will be moved over to the library and the dependency updated.

### Project specific styles

For features that are unique to the project and not needed in the library, we create specific styles for the layout in development in the `src/styl/layouts` directory in a new file.

### Stylus and compilation specific details

- Values for the use of the css `calc` function need to be escaped with quotes.

## WordPress Template Patterns

- **Partials system**: Reusable PHP components in `partials/` directory
- **Template hierarchy**: Custom post types (contributor, event, job, notice) with dedicated templates
- **Module imports**: Layout-specific Stylus files in `layouts/` directory
- **Custom Functions**: Organized in `lib/functions-*.php` files

## WordPress Customizations

### Custom Post Types
- **Contributors**: Author profiles with CMB2 meta boxes
- **Events**: IRL events with calendar integration  
- **Jobs**: Job postings with deadline handling
- **Notices**: Simple announcements without archives

### Meta
- **CMB2**: Meta box framework for custom fields (`lib/meta/`)
- **Theme Options**: ACF-style options in `lib/theme-options/`

### Data Flow

Access PHP data via `WP` global object.

```php
// functions.php localizes data for JavaScript
$global_javascript_variables = array(
    'supportSectionAutovalues' => nm_get_support_autovalues(),
    'liveCheckerData'          => nm_get_livechecker_data(),
);
wp_localize_script('site-js', 'WP', $global_javascript_variables);
```

## Development Patterns

### File Organization
- **Modules**: Feature-based classes in `src/js/modules/`
- **Functions**: Utility functions in `src/js/functions/`
- **Styles**: Component styles in `layouts/` imported by `site.styl`
- **Templates**: WordPress template files in theme root, partials in `partials/`

### Code Standards
- **CSS**: BEM-like naming with component prefixes (e.g., `support-form__button`)
- **PHP**: WordPress coding standards with PHPCS configuration

### Dependencies
- **Frontend**: jQuery, Lodash (partial imports), Luxon, Lazysizes, Swiper
- **Build**: Webpack 5, Babel, Stylus, PostCSS, ESLint
- **WordPress**: CMB2, Composer autoloader for vendor packages

## Key Integration Points

### Support/Donation System
- **External service**: `donate.novaramedia.com` 
- **Dynamic display values**: Configurable via `supportSectionAutovalues` and other Site Meta settings
- **Form behavior**: `Support.js` handles value selection, custom inputs, accessibility

### Accessibility Features
- **ARIA states**: `aria-checked`, `aria-pressed` for custom form controls
- **Keyboard navigation**: Arrow keys for form controls, focus management
- **Screen readers**: Proper labeling and state announcements
