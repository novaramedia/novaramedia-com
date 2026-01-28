# Newsletter Signup Block

## Overview

Gutenberg block for inserting inline newsletter signup forms into post/page content. Editors select a newsletter from the `newsletter` custom post type, and the block renders a signup form styled with the theme's existing CSS.

**Block name:** `nm-wp/newsletter-signup`
**Category:** Widgets
**Status:** In development

---

## Architecture Decision

This block was migrated from a standalone plugin (`nm-wpblock-newsletter-signup`) into the theme on 2025-01-23.

**Rationale:**

- Block output relies on 20+ theme CSS classes (`.email-signup__*`, `.ui-button`, `.grid-row`, etc.)
- Theme's `MailchimpSignup.js` module already handles form submission for `.email-signup__form`
- Same team maintains both, only used on this site
- More blocks planned - establishes pattern for future blocks

See `/wp-content/plugins/nm-wpblock-newsletter-signup/ARCHITECTURE-DECISION.md` for full analysis.

---

## Current Status

### Completed

- [x] Editor component - select newsletter from dropdown
- [x] Block registration via `lib/blocks.php`
- [x] Build process integrated into theme (`npm run build:blocks`)
- [x] Migration from plugin to theme
- [x] **Dynamic PHP rendering implemented** - Using `render.php` for server-side rendering
- [x] Pull newsletter meta fields (mailchimp_key, headline, copy) dynamically at render time
- [x] Form action URL wired up via `nm_get_netlify_url()` helper
- [x] Integrated with theme's `render_mailchimp_signup_form()` helper for consistency

### Ready for Testing

- [ ] Test block appears in editor inserter (Embed category)
- [ ] Test newsletter dropdown populates with newsletter posts
- [ ] Test selecting newsletter updates block attributes
- [ ] Test saved block displays form on frontend with correct data
- [ ] Test form submission works (processes through MailchimpSignup.js)
- [ ] Test success/error states display correctly
- [ ] Test form is accessible (labels, focus states)
- [ ] Test responsive layout works

---

## Data Flow

```
Newsletter CPT (WordPress)
    │
    ├── title.rendered → Form headline (optional override)
    ├── meta.mailchimp_key → Hidden input value
    └── meta.banner_* → Styling options (future)
           │
           ▼
Editor (edit.js)
    │
    ├── Queries /wp/v2/newsletter via REST
    ├── Renders dropdown selector
    └── Saves selected newsletter object to attributes
           │
           ▼
Frontend (save.js)
    │
    ├── Outputs form HTML with theme CSS classes
    ├── Theme's MailchimpSignup.js handles submission
    └── Form POSTs to Netlify function
```

---

## Key Files

| File          | Purpose                                        |
| ------------- | ---------------------------------------------- |
| `block.json`  | Block metadata, attributes, asset registration |
| `index.js`    | Block registration entry point                 |
| `edit.js`     | Editor UI (React component)                    |
| `save.js`     | Frontend HTML output                           |
| `editor.scss` | Editor-only styles                             |

**Theme integration:**

- `lib/blocks.php` - Registers all blocks in `/blocks/*/`
- `src/js/modules/MailchimpSignup.js` - Form submission handler
- `src/styl/layouts/email-signup.styl` - Form styles

---

## Newsletter Post Type

Defined in `lib/post-types.php`. Key meta fields (CMB2):

| Field           | Key               | Purpose                          |
| --------------- | ----------------- | -------------------------------- |
| Mailchimp Key   | `mailchimp_key`   | Newsletter identifier for signup |
| Banner Headline | `banner_headline` | Optional headline text           |
| Banner Text     | `banner_text`     | Optional description             |

Meta boxes defined in `lib/meta/meta-boxes-posttype-newsletter.php`.

---

## Implementation Notes

### Dynamic PHP Render (✅ Implemented)

**Previous state:** Static `save()` baked HTML into post content at save time.

**Current implementation:** Dynamic PHP render using `render.php`.

#### Why dynamic render makes sense here

1. **Access to theme PHP helpers** - Can reuse existing renderer functions from `lib/renderers.php`
2. **Fresh data on each load** - Newsletter meta (mailchimp_key, headlines) pulled at render time, not baked in
3. **Consistent patterns** - Theme already uses PHP templating; blocks should follow same approach
4. **Easier maintenance** - Change output without re-saving every post containing the block
5. **Server-side logic** - Could check subscription status, A/B test, or conditionally show

#### Implementation details

**1. ✅ Updated block.json:**

```json
{
  "render": "file:./render.php"
}
```

**2. ✅ Simplified save.js to return null:**

```js
export default function save() {
  return null; // PHP handles rendering
}
```

**3. ✅ Created render.php:**

The implementation pulls fresh newsletter meta data and uses the theme's existing `render_mailchimp_signup_form()` helper. This ensures:

- Consistent styling with other newsletter forms
- Automatic integration with `MailchimpSignup.js` module
- Correct form action URL via `nm_get_netlify_url()`
- Fresh data on every page load (no stale content)

See `src/blocks/newsletter-signup/render.php` for the complete implementation.

#### Attributes note

With dynamic render, `save()` returns null but attributes are still saved to post content as a JSON comment:

```html
<!-- wp:nm-wp/newsletter-signup {"newsletter":{"id":123,"title":{"rendered":"Weekly"}}} /-->
```

PHP receives these via `$attributes` parameter.

### Form submission

The form uses class `.email-signup__form` which the theme's `MailchimpSignup.js` module automatically binds to. No additional JS needed in the block - just output the right HTML structure.

### CSS classes used

From `save.js` output:

```
.grid-row, .grid-item, .is-xxl-24     // Grid layout
.fs-8, .fs-6, .fs-4-sans, .fs-2       // Font sizes
.mb-4, .mb-2, .mr-6, .ml-2            // Spacing
.email-signup__*                       // Component styles
.ui-input, .ui-checkbox, .ui-button   // Form controls
.u-visuallyhidden                      // Accessibility
.layout-flex-align-center              // Flexbox utility
.layout-split-level                    // Layout pattern
```

All defined in theme's Stylus files.

---

## Build Commands

```bash
# Build blocks only
npm run build:blocks

# Watch mode for development
npm run dev:blocks

# Build everything (theme + blocks)
npm run build
```

---

## Testing Checklist

- [ ] Block appears in editor inserter (Embed category)
- [ ] Newsletter dropdown populates with newsletter posts
- [ ] Selecting newsletter updates block attributes
- [ ] Saved block displays form on frontend
- [ ] Form submission works (processes through MailchimpSignup.js)
- [ ] Success/error states display correctly
- [ ] Form is accessible (labels, focus states)
- [ ] Responsive layout works

---

## Related

- Original plugin: `/wp-content/plugins/nm-wpblock-newsletter-signup/`
- Newsletter archive: `/newsletters/`
- Privacy policy link in form: `/privacy-policy/`
