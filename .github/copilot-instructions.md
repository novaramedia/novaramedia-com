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

### dist/ commits
Only commit `dist/` when source files actually changed. Run `npm run build` to verify. Do not commit dist files when only modifying documentation or config.

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

- Stylus tries to evaluate arithmetic inside `calc()`, which breaks the CSS output. Wrap the entire value (including `calc`) in a quoted string and pass it through the built-in `unquote()` function: `unquote('calc(100vh - var(--header-height))')`. Do **not** quote just the inside of calc — e.g. `calc('100vh - 2rem')` — as the quotes will leak into the compiled CSS.

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

## Testing

Run `npm run build` to verify JS/CSS changes compile without errors. See `docs/testing/` for full Cypress testing guide.

## Development Notes

### Stylus
- Wrap `calc()` values in `unquote()`: `unquote('calc(100vh - var(--header-height))')` — Stylus evaluates arithmetic inside calc otherwise. Quoting only the inside (e.g. `calc('...')`) leaks quotes into CSS output.
- Import from `nm-stylus-library` before custom styles
- Grid class order: `.grid-item`, then breakpoint sizes (xxl→s), then offsets

### JavaScript modules
- Import paths need `.js` extension: `import func from '../functions/func.js'`
- All modules need `onReady()` and `bind()` methods; import in `main.js`
- jQuery available globally as `$`/`jQuery`, but prefer ES6 imports

### WordPress integration
- PHP data is localized via `wp_localize_script` — check `functions.php` for available `WP` global properties
- Template hierarchy resolves most specific first (e.g. `single-event.php` before `single.php`)
- CMB2 meta boxes defined in `lib/meta/` — check existing patterns before creating new ones

## Development Workflow

### File Modifications

**JavaScript**: 
- New modules: Create in `src/js/modules/` and import in `main.js`
- Utility functions: Add to `src/js/functions/` as standalone exports
- Follow the module class pattern with `constructor()`, `onReady()`, `bind()` methods

**Stylus/CSS**:
- New layouts: Create in `src/styl/layouts/` and import in `site.styl`
- Library extensions: Add to `upstream-to-library.styl` for features that should be shared
- Use existing utility classes from `nm-stylus-library` when possible

**PHP**:
- Templates: Create in theme root following WordPress template hierarchy
- Functions: Add to appropriate `lib/functions-*.php` file or create new one
- Meta boxes: Add to `lib/meta/` directory
- Partials: Reusable components go in `partials/` directory

### Git Workflow

- Work on feature branches (not `master` or `development`)
- Keep commits focused and descriptive
- Don't commit during release process
- The `dist/` directory should be committed after building

## Code Review

Flag these in every PR:

- **Changelog missing** — `CHANGELOG.md` must have an entry under `[Unreleased]` (Keep a Changelog format). One entry per meaningful change, not per commit.
- **PR title format** — must be `<Type>: <Short description>`. Valid types: `Fix`, `Feature`, `Release`, `Refactor`, `Chore`, `Content`, `Docs`.
- **Debug artifacts** — no `console.log`, `var_dump`, `dd()`, or similar left in production code.
- **TODO without issue ref** — any TODO comment must reference a GitHub issue: `TODO(#123): ...`
- **TypeScript `any`** — flag implicit or unjustified `any`. A comment explaining why is acceptable.
- **New dependency** — flag any new package/import not already in the project. PR description should justify it.
- **Frontend CSS** — flag custom CSS that duplicates nm-stylus-library utility classes. Use the library.
- **Pattern deviation** — flag code that diverges from existing patterns in the same file or module without explanation.

Don't suggest architectural changes in review — raise a new issue instead.

## Generating Code from Issues

Before generating:

1. Read the full issue including all comments.
2. Check for linked issues or PRs — understand prior attempts.
3. Read the files most likely to be affected before writing anything.

When generating:

- Match existing file structure, naming conventions, and abstractions.
- Prefer extending existing patterns over introducing new ones.
- Match the project's stack — check `CLAUDE.md` before assuming TypeScript, PHP, etc.
- For any frontend work, use nm-stylus-library utility classes.
- Don't add error handling or validation beyond what the issue describes.

After generating:

- Update `CHANGELOG.md` under `[Unreleased]`.
- Ensure PR title follows format above.
