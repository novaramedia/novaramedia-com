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

## Testing and Verification

### Building Code
Always build the project to verify JavaScript and CSS changes:
```bash
npm run dev    # Development build (faster, with source maps)
npm run build  # Production build (minified, optimized)
```

### Linting
- **JavaScript**: ESLint is configured with `eslint:recommended` and Prettier integration
  - ESLint runs automatically during webpack builds via `eslint-webpack-plugin`
  - Configuration: `.eslintrc.json`
  - Supports ES6 modules and browser environment
- **PHP**: WordPress Coding Standards via PHPCS
  - Configuration: `phpcs.xml`
  - Excludes Yoda conditionals and doc comment requirements for small functions
  - Run manually if phpcs is available: `phpcs`

### Manual Verification
Since this is a WordPress theme without automated tests:
1. **Build the code** after making changes
2. **Check webpack output** for errors or warnings
3. **Review compiled files** in `dist/` directory
4. **Test in WordPress** if possible, or review the generated HTML/CSS/JS
5. **Verify accessibility** features if modifying interactive components

### Checking Your Changes
```bash
# See what files changed
git status

# Review your changes
git diff

# Check webpack output
npm run build
```

## Common Pitfalls and Troubleshooting

### Stylus Compilation Issues
- **calc() values**: Must be escaped with quotes: `calc("100% - 20px")`
- **Import order**: Make sure to import from `nm-stylus-library` before custom styles
- **Grid classes**: Always use correct order: `.grid-item`, then breakpoint sizes (xxl→s), then offsets

### JavaScript Module Issues
- **Import paths**: Use relative paths with `.js` extension: `import func from '../functions/func.js'`
- **Module loading**: Ensure modules are imported in `main.js` and have proper `onReady()` and `bind()` methods
- **jQuery**: Available globally as `$` and `jQuery`, but prefer ES6 imports where possible

### Build System
- **Don't modify**: Webpack configuration without team approval
- **node_modules**: Never commit this directory (already in .gitignore)
- **dist directory**: Contains built files, should be committed after build
- **Watch mode**: Use `npm run dev` for development, but note it doesn't have watch mode configured

### WordPress Integration
- **WP global object**: PHP data is localized via `wp_localize_script` - check `functions.php` for available properties
- **Template hierarchy**: WordPress looks for most specific template first (e.g., `single-event.php` before `single.php`)
- **CMB2 fields**: Custom meta boxes are defined in `lib/meta/` - check existing patterns before creating new ones

## Development Workflow

### Making Changes

1. **Start with understanding**: Review related files before making changes
2. **Follow patterns**: Use existing code as a guide (e.g., look at similar modules or layouts)
3. **Build frequently**: Run `npm run build` to catch errors early
4. **Keep changes minimal**: Only modify what's necessary to achieve the goal
5. **Test the build**: Ensure webpack compiles without errors

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
