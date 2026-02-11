# Multi-Newsletter Signup Module

## Context

The current newsletter signup system renders one form per newsletter. Users wanting to subscribe to multiple newsletters must fill in their name, email, and GDPR consent separately for each one. The newsletters archive page (`archive-newsletter.php`) stacks these individual forms vertically.

This plan adds a **multi-newsletter signup module** - a reusable template partial where users enter their details once, select which newsletters they want, and submit a single form. The existing single-newsletter signup (Gutenberg block, single newsletter template, email-signup partial) stays as-is.

Both the theme and the Netlify microservice need changes. The microservice currently only accepts one newsletter name per request.

## How the current system works

**Theme side:**

- Newsletter CPT stores `_nm_mailchimp_key` (the interest group name in Mailchimp, e.g. "The Cortado")
- `render_mailchimp_signup_form()` in `lib/renderers.php` generates a form with a hidden `newsletter` field set to one key
- `MailchimpSignup.js` intercepts form submit, POSTs form-encoded data to the Netlify function
- Form data: `firstName`, `email`, `gdpr`, `newsletter` (single value)

**Microservice side** (`novaramedia/novara-media-mailchimp-signup`):

- Single Mailchimp audience list (env: `MAILCHIMP_LIST_ID`)
- Newsletters are "interests" (groups) within the first interest category on the list
- Matches the `newsletter` form value to an interest name
- Creates or updates the member, setting that interest to `true`
- Also sets "General Mailing" interest for new/resubscribing members
- Sets member status to `pending` (triggers double opt-in email)

---

## UI Design Options

Both options share the same form fields (name, email, GDPR) and backend logic. The difference is how newsletters are presented for selection.

### Option A: Compact list with checkboxes

Best for: embedding in articles, sidebars, smaller spaces, or when there are many newsletters.

```
┌─────────────────────────────────────────────┐
│  Sign up to our newsletters                 │
│                                             │
│  ☑ The Cortado                              │
│    Daily news briefing                      │
│                                             │
│  ☐ Novara FM                                │
│    Weekly podcast roundup                   │
│                                             │
│  ☑ The Bulletin                             │
│    Monthly deep dives                       │
│                                             │
│  ┌───────────────────────────────────────┐  │
│  │ First name                            │  │
│  └───────────────────────────────────────┘  │
│  ┌───────────────────────────────────────┐  │
│  │ Email                                 │  │
│  └───────────────────────────────────────┘  │
│  ☑ I agree to the Privacy Policy            │
│  [ Sign up ]                                │
└─────────────────────────────────────────────┘
```

- Each newsletter: checkbox + name (bold) + short description (from `_nm_banner_text`)
- Simple vertical list
- Minimal footprint

### Option B: Visual cards with toggles

Best for: dedicated newsletter pages, archive replacement, marketing-focused layouts.

```
┌─────────────────────────────────────────────────────────┐
│  Sign up to our newsletters                             │
│                                                         │
│  ┌──────────────────────────────┐  ┌──────────────────┐ │
│  │ [Image]                      │  │ [Image]          │ │
│  │ The Cortado          [  ☑ ]  │  │ Novara FM  [ ☐ ] │ │
│  │ Daily news briefing          │  │ Weekly podcast   │ │
│  │ from the Novara Media team   │  │ roundup          │ │
│  └──────────────────────────────┘  └──────────────────┘ │
│                                                         │
│  First name: [_____________]                            │
│  Email:      [_____________]                            │
│  ☑ I agree to the Privacy Policy                        │
│  [ Sign up ]                                            │
└─────────────────────────────────────────────────────────┘
```

- Each newsletter: card with optional image, headline, description, toggle/checkbox
- Uses the grid system for responsive layout (cards side by side on desktop, stacked on mobile)
- More visual, better for discovery
- Uses existing `_nm_banner_image` if available

### Recommendation

Start with **Option A** (compact) as the default. It works everywhere, is simpler to build, and the existing newsletter metadata already supports it. Option B can be added later as a variant (controlled by a partial argument) without changing the underlying logic.

---

## Implementation Plan

### Theme Changes

#### 1. New template partial: `partials/multi-newsletter-signup.php`

A reusable partial that renders the multi-signup form. Accepts arguments to control behaviour:

**Arguments:**
| Argument | Type | Default | Description |
|----------|------|---------|-------------|
| `newsletter_ids` | array\|null | null | Specific newsletter post IDs to include. If null, queries all published newsletters |
| `headline` | string | "Sign up to our newsletters" | Section heading |
| `description` | string | null | Optional intro text |
| `background-color` | string | "black" | Container background |
| `text-color` | string | "white" | Text colour |
| `button-color` | string | "red" | Submit button colour |
| `preselected` | array | [] | Newsletter IDs to pre-check |
| `layout` | string | "compact" | "compact" (Option A) or "cards" (Option B, future) |

**Logic:**

1. If `newsletter_ids` is null, query all published newsletters: `get_posts(['post_type' => 'newsletter', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC'])`
2. Filter out any without a `_nm_mailchimp_key`
3. Render newsletter selection list (checkboxes with name + description)
4. Render shared form fields: firstName, email, GDPR checkbox, submit button
5. Each newsletter checkbox: `<input type="checkbox" name="newsletters[]" value="{mailchimp_key}" />`
6. Form action: `nm_get_netlify_url()` (same as existing)
7. Include processing/success/error feedback states (same pattern as existing `render_mailchimp_signup_form()`)

**HTML structure:**

```html
<div class="multi-newsletter-signup {background/color classes}">
  <div class="container">
    <h3 class="multi-newsletter-signup__headline">{headline}</h3>
    <p class="multi-newsletter-signup__description">{description}</p>

    <form
      class="multi-newsletter-signup__form"
      action="{netlify-url}"
      method="post"
    >
      <!-- Newsletter selection -->
      <div class="multi-newsletter-signup__options">
        <label class="multi-newsletter-signup__option">
          <input type="checkbox" name="newsletters[]" value="{mailchimp_key}" />
          <span class="multi-newsletter-signup__option-name">{headline}</span>
          <span class="multi-newsletter-signup__option-description"
            >{banner_text}</span
          >
        </label>
        <!-- repeat for each newsletter -->
      </div>

      <!-- Shared form fields (same markup pattern as existing signup form) -->
      <div class="multi-newsletter-signup__fields">
        <input name="firstName" type="text" placeholder="First name" />
        <input name="email" type="email" placeholder="Email" required />
        <label>
          <input name="gdpr" type="checkbox" value="accepted" required />
          I agree to the Privacy Policy
        </label>
        <input type="submit" value="Sign up" />
      </div>

      <!-- Feedback states (same as existing) -->
      <div class="multi-newsletter-signup__feedback-processing">...</div>
      <div class="multi-newsletter-signup__feedback-failed">...</div>
      <div class="multi-newsletter-signup__feedback-completed">...</div>
    </form>
  </div>
</div>
```

**Key difference from single signup:** The form sends `newsletters[]` (array of mailchimp keys) instead of `newsletter` (single key).

#### 2. New/modified JS: Multi-newsletter form handling

**Option A: Extend `MailchimpSignup.js`**

The existing module binds to `.email-signup__form`. The multi-signup form uses a different class (`.multi-newsletter-signup__form`). We can either:

1. Add a second binding in `MailchimpSignup.js` for `.multi-newsletter-signup__form`
2. Create a separate module

**Recommendation:** Extend `MailchimpSignup.js` to also handle `.multi-newsletter-signup__form`. The submission logic is nearly identical - the only difference is that jQuery's `$form.serialize()` will automatically serialize `newsletters[]` checkboxes as `newsletters[]=key1&newsletters[]=key2`. The microservice will need to handle this format.

Changes to `MailchimpSignup.js`:

- Add validation: at least one newsletter must be checked before submit
- Show a validation message if no newsletters are selected
- Bind to both `.email-signup__form` and `.multi-newsletter-signup__form`

Alternatively, if we want to keep the modules cleanly separated, a new `MultiNewsletterSignup.js` module could extend the same pattern. But the logic is simple enough that extending the existing module makes more sense.

#### 3. CSS: `src/styl/components/multi-newsletter-signup.styl`

New Stylus file for the multi-signup component:

- `.multi-newsletter-signup` - Container with padding, background colour
- `.multi-newsletter-signup__options` - Newsletter list container
- `.multi-newsletter-signup__option` - Individual newsletter row: flex layout, checkbox + text
- `.multi-newsletter-signup__option-name` - Bold newsletter name
- `.multi-newsletter-signup__option-description` - Lighter description text
- `.multi-newsletter-signup__fields` - Shared form fields section
- Feedback overlays: reuse existing `.email-signup__overlay` pattern or duplicate for independence

Import in `src/styl/site.styl` under COMPONENTS.

#### 4. Update `archive-newsletter.php`

Replace the current loop of individual signup forms with the new partial:

```php
get_template_part('partials/multi-newsletter-signup', null, [
    'headline' => 'Sign up to our newsletters',
    'hide-discover' => true,
]);
```

This is optional and can be done as a follow-up. The archive page can continue working as-is while the new partial is tested elsewhere first.

---

### Microservice Changes

**Repo:** `novaramedia/novara-media-mailchimp-signup`
**File:** `functions/mailchimp-signup/mailchimp-signup.js`

#### 1. Accept multiple newsletter names

Currently the handler destructures `newsletter` as a single string. Change to support both:

- Single: `newsletter=The+Cortado` (existing format, backwards compatible)
- Multiple: `newsletters[]=The+Cortado&newsletters[]=Novara+FM` (new format from multi-signup)

```javascript
const {
  email,
  newsletter,
  'newsletters[]': newslettersArray,
  firstName,
} = querystring.decode(body);

// Normalise to array
let newsletterNames = [];
if (newslettersArray) {
  newsletterNames = Array.isArray(newslettersArray)
    ? newslettersArray
    : [newslettersArray];
} else if (newsletter) {
  newsletterNames = [newsletter];
}

if (email === '' || newsletterNames.length === 0) {
  return output(400, 'No data');
}
```

This is **backwards compatible** - the existing single-signup forms will continue to work because they send `newsletter` (singular) which gets normalised to a single-element array.

#### 2. Look up all matching interests

Instead of finding one `signupInterest`, find all:

```javascript
const signupInterests = interests.interests.filter((interest) => {
  return newsletterNames.includes(interest.name);
});

if (signupInterests.length === 0) {
  return output(400, 'Form configuration issue. Newsletter(s) not found');
}

if (signupInterests.length !== newsletterNames.length) {
  // Some newsletters didn't match - log warning but continue with what we found
  const found = signupInterests.map((i) => i.name);
  const missing = newsletterNames.filter((n) => !found.includes(n));
  console.warn('Unmatched newsletters:', missing);
}
```

#### 3. Set all interests in one member update

Build the interests object with all selected newsletters:

```javascript
const interestUpdates = {};
signupInterests.forEach((interest) => {
  interestUpdates[interest.id] = true;
});
```

Then use `interestUpdates` where the current code uses `{ [signupInterest.id]: true }`.

**For existing members (subscribed):** Check if already signed up to ALL selected interests. If yes, return "Already signed up". If signed up to some, update the missing ones.

**For existing members (unsubscribed/archived):** Same as current logic but with all selected interests + General Mailing.

**For new members:** Create with all selected interests + General Mailing.

#### 4. Update notes

Add a note listing all newsletters:

```javascript
const names = signupInterests.map((i) => i.name).join(', ');
const note = `Signed up for ${names} on the website`;
```

#### 5. Fix existing bugs (while we're here)

The current code has a couple of issues worth fixing:

- **`output()` function bug:** `statusCode => 400` is an arrow function (always returns 400), should be `statusCode >= 400`
- **Unused `setMailchimpTags()` function:** Can be removed
- **`mailchimp-api-v3` package:** Old but functional. Consider updating to `@mailchimp/mailchimp_marketing` (official SDK) in a future PR, not in scope here

#### 6. Full refactored handler (summary)

```
Parse body → normalise newsletter(s) to array
    ↓
Hash email → check if member exists
    ↓
Fetch interest categories → find all matching interests
    ↓
┌─ Member exists + subscribed:
│    Check which interests are new → update with new ones
│
├─ Member exists + unsubscribed/archived:
│    Reset interests → set selected + General → status: pending
│
└─ New member:
     Create with selected interests + General → status: pending
    ↓
Add note with all newsletter names → return 200
```

---

## Files Summary

### Theme - New

| File                                               | Purpose                   |
| -------------------------------------------------- | ------------------------- |
| `partials/multi-newsletter-signup.php`             | Reusable template partial |
| `src/styl/components/multi-newsletter-signup.styl` | Component styles          |

### Theme - Modified

| File                                | Change                                                                       |
| ----------------------------------- | ---------------------------------------------------------------------------- |
| `src/js/modules/MailchimpSignup.js` | Add binding for multi-signup form, validate at least one newsletter selected |
| `src/styl/site.styl`                | Add `@import "components/multi-newsletter-signup"`                           |
| `archive-newsletter.php`            | (Optional) Replace stacked forms with multi-signup partial                   |

### Microservice - Modified

| File                                             | Change                                                                                                                             |
| ------------------------------------------------ | ---------------------------------------------------------------------------------------------------------------------------------- |
| `functions/mailchimp-signup/mailchimp-signup.js` | Accept newsletter array, look up multiple interests, set all in one update. Fix `output()` bug. Remove unused `setMailchimpTags()` |

---

## Implementation Sequence

1. **Microservice first** - Update the Netlify function to accept arrays. Deploy. Existing single forms still work (backwards compatible).
2. **Theme CSS** - Create `multi-newsletter-signup.styl`, add import.
3. **Theme PHP** - Create `partials/multi-newsletter-signup.php`.
4. **Theme JS** - Extend `MailchimpSignup.js` for multi-signup forms.
5. **Build + test** - `npm run build`, test multi-signup partial.
6. **Integration** - Add the partial where needed (archive page, other templates).

Steps 1 is independent and can be deployed first. Steps 2-5 are one theme PR. Step 6 can be a follow-up.

---

## Usage Examples

**On the newsletter archive page:**

```php
get_template_part('partials/multi-newsletter-signup', null, [
    'headline' => 'Sign up to our newsletters',
    'background-color' => 'black',
]);
```

**Embedded in a specific page with curated newsletters:**

```php
get_template_part('partials/multi-newsletter-signup', null, [
    'newsletter_ids' => [123, 456, 789],
    'headline' => 'Stay informed',
    'preselected' => [123],
]);
```

**In a support/about page:**

```php
get_template_part('partials/multi-newsletter-signup', null, [
    'headline' => 'Never miss a story',
    'description' => 'Choose which newsletters you want to receive.',
    'background-color' => 'white',
    'text-color' => 'black',
    'button-color' => 'black',
]);
```

---

## Verification

- [ ] Microservice accepts single `newsletter` param (backwards compat with existing forms)
- [ ] Microservice accepts `newsletters[]` array param
- [ ] Microservice sets all selected interests on new member creation
- [ ] Microservice adds interests to existing subscribed member without removing existing ones
- [ ] Microservice triggers single opt-in email (not one per newsletter)
- [ ] Multi-signup partial renders all published newsletters when no IDs specified
- [ ] Multi-signup partial renders only specified newsletters when IDs provided
- [ ] Form validation prevents submit with no newsletters selected
- [ ] Form submits correctly with multiple newsletters selected
- [ ] Success/error/processing states work as expected
- [ ] Existing single-signup forms continue to work unchanged
- [ ] `npm run build` succeeds
- [ ] Responsive layout works on mobile

---

## Future Enhancements

- **Option B card layout** - Add `layout: "cards"` variant to the partial with visual cards and images
- **Gutenberg multi-signup block** - A block that wraps the partial with configurable newsletter selection in the editor
- **"Manage subscriptions" mode** - For existing subscribers to update their newsletter preferences (would need a different microservice endpoint)
- **Pre-check based on existing subscriptions** - If we can identify the user (e.g. via email param in URL from a Mailchimp campaign), pre-check their current subscriptions
