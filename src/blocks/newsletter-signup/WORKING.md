# Newsletter Signup Block

## Overview

Gutenberg block for inserting inline newsletter signup forms into post/page content. Editors select a newsletter from the `newsletter` custom post type, and the block renders a signup form styled with the theme's existing CSS.

**Block name:** `flavor3/newsletter-signup`
**Category:** Embed
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

### In Progress
- [ ] **save.js** - Currently outputs placeholder form, needs to use newsletter post data

### TODO
- [ ] Pull newsletter meta fields (mailchimp_key, headline, copy) into save output
- [ ] Wire up form action URL (Netlify function endpoint)
- [ ] Test with theme's `MailchimpSignup.js` form handler
- [ ] Consider dynamic rendering (PHP) vs static save if data changes frequently

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

| File | Purpose |
|------|---------|
| `block.json` | Block metadata, attributes, asset registration |
| `index.js` | Block registration entry point |
| `edit.js` | Editor UI (React component) |
| `save.js` | Frontend HTML output |
| `editor.scss` | Editor-only styles |

**Theme integration:**
- `lib/blocks.php` - Registers all blocks in `/blocks/*/`
- `src/js/modules/MailchimpSignup.js` - Form submission handler
- `src/styl/layouts/email-signup.styl` - Form styles

---

## Newsletter Post Type

Defined in `lib/post-types.php`. Key meta fields (CMB2):

| Field | Key | Purpose |
|-------|-----|---------|
| Mailchimp Key | `mailchimp_key` | Newsletter identifier for signup |
| Banner Headline | `banner_headline` | Optional headline text |
| Banner Text | `banner_text` | Optional description |

Meta boxes defined in `lib/meta/meta-boxes-posttype-newsletter.php`.

---

## Implementation Notes

### Dynamic PHP Render (Recommended Approach)

**Current state:** Static `save()` bakes HTML into post content at save time.

**Recommended:** Switch to dynamic PHP render using `render.php`.

#### Why dynamic render makes sense here

1. **Access to theme PHP helpers** - Can reuse existing renderer functions from `lib/renderers.php`
2. **Fresh data on each load** - Newsletter meta (mailchimp_key, headlines) pulled at render time, not baked in
3. **Consistent patterns** - Theme already uses PHP templating; blocks should follow same approach
4. **Easier maintenance** - Change output without re-saving every post containing the block
5. **Server-side logic** - Could check subscription status, A/B test, or conditionally show

#### Implementation outline

**1. Update block.json:**
```json
{
  "render": "file:./render.php"
}
```

**2. Simplify save.js to return null:**
```js
export default function save() {
  return null; // PHP handles rendering
}
```

**3. Create render.php:**
```php
<?php
/**
 * Newsletter Signup Block - Server-side render
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content (empty for dynamic blocks).
 * @param WP_Block $block      Block instance.
 */

$newsletter = $attributes['newsletter'] ?? null;

if ( ! $newsletter || empty( $newsletter['id'] ) ) {
    return; // No newsletter selected
}

$newsletter_id = $newsletter['id'];

// Pull fresh meta from newsletter post
$mailchimp_key = get_post_meta( $newsletter_id, 'mailchimp_key', true );
$headline      = get_post_meta( $newsletter_id, 'banner_headline', true );
$description   = get_post_meta( $newsletter_id, 'banner_text', true );
$form_action   = get_theme_mod( 'newsletter_form_endpoint', '' );

// Fallbacks
if ( empty( $headline ) ) {
    $headline = get_the_title( $newsletter_id );
}

// Use theme helper if available
if ( function_exists( 'nm_render_email_signup_form' ) ) {
    nm_render_email_signup_form( $mailchimp_key, $headline, $description );
    return;
}

// Otherwise render inline (structure matches theme's email-signup partial)
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
    <div class="grid-row">
        <div class="grid-item is-xxl-24">
            <h3 class="fs-8 fs-s-6 mb-4 js-fix-widows"><?php echo esc_html( $headline ); ?></h3>
            <?php if ( $description ) : ?>
                <p class="fs-6 fs-s-4-sans mr-6"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
        </div>
        <div class="grid-item is-xxl-24">
            <form class="email-signup__form" action="<?php echo esc_url( $form_action ); ?>" method="post">
                <input type="hidden" name="newsletter" value="<?php echo esc_attr( $mailchimp_key ); ?>" />
                <!-- Form fields... -->
            </form>
        </div>
    </div>
</div>
<?php
```

**4. Consider creating theme helper:**

Add to `lib/renderers.php`:
```php
function nm_render_email_signup_form( $mailchimp_key, $headline, $description = '' ) {
    // Centralised form rendering used by block and existing templates
    get_template_part( 'partials/email-signup-form', null, [
        'mailchimp_key' => $mailchimp_key,
        'headline'      => $headline,
        'description'   => $description,
    ] );
}
```

This lets both the block and existing theme templates share the same form markup.

#### Migration steps

1. Create `render.php` in `src/blocks/newsletter-signup/`
2. Add `"render": "file:./render.php"` to `block.json`
3. Change `save.js` to return `null`
4. Rebuild blocks (`npm run build:blocks`)
5. Test - existing blocks should render correctly without re-saving
6. Optionally extract shared helper to `lib/renderers.php`

#### Attributes note

With dynamic render, `save()` returns null but attributes are still saved to post content as a JSON comment:
```html
<!-- wp:flavor3/newsletter-signup {"newsletter":{"id":123,"title":{"rendered":"Weekly"}}} /-->
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
