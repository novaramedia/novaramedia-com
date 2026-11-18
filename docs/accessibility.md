# Accessibility

How we build accessible templates, scripts and styles in this theme. Target is WCAG 2.2 AA. Apply these rules to new code, and bring older code into line when you touch it.

## Principles

1. **Don't change what default users see.** Prefer fixes that are invisible to mouse and touch users: native elements, ARIA state, tab order, heading levels, user-preference media queries. A change that alters the visual design or how something behaves for everyone is a design decision. Raise it rather than ship it as an accessibility fix.
2. **Gate, don't remove.** Don't delete a feature (autoplay, a ticker, a secondary link) to satisfy a criterion. Turn it off for users who ask for that through their settings, or take duplicates out of the keyboard and screen reader path.
3. **Native first.** A real `<button>`, `<a href>` or `<input>` brings keyboard handling, focus and semantics for free. ARIA on a `<div>` means re-implementing all of that in JavaScript.

## Interactive controls

- Actions are `<button type="button">`. Navigation is `<a href="…">`. Nothing else is clickable.
- Restyle buttons by resetting browser chrome (`margin`, `padding`, `border`, `background`, `color`, `font`, `line-height`) in Stylus so they render the same as the element they replace. See `.site-header__toggle` in `src/styl/layouts/header.styl`.
- Icon-only controls carry an `aria-label`; the icon itself gets `aria-hidden="true"`.

```html
<!-- Don't: not focusable, no keyboard support -->
<li class="site-header__toggle" role="button" tabindex="0">…</li>
<a id="accept" class="ui-button">Accept</a>

<!-- Do -->
<li><button type="button" class="site-header__toggle" aria-label="Search">…</button></li>
<button type="button" id="accept" class="ui-button">Accept</button>
```

## Show/hide panels

- The toggle has `aria-expanded` and `aria-controls` pointing at the panel's real `id`.
- Don't use `aria-pressed` (that's for on/off toggle buttons) or `aria-haspopup="menu"` (that's for `role="menu"` widgets) on a disclosure.
- Set `aria-expanded` from the panel's actual visibility after toggling. Don't flip it blindly, and don't rely on `$(this)` inside an arrow function: it won't be the element.
- Moving focus into a panel when it opens is fine (the header nav moves focus to its first link).

## Custom radio groups

- Wrap in `role="radiogroup"` with a label. Each option is `role="radio"` with `aria-checked`.
- Roving `tabindex`: the selected option is `0`, the rest `-1`, so Tab enters and leaves the group once.
- Arrow keys move within the closest `[role="radiogroup"]` only, never across the whole form. See `Support.js`.
- For new forms, prefer native `<input type="radio">` in a `<fieldset>`; no script needed.

## Hidden and duplicate elements

- Anything visually hidden but still in the DOM (opacity 0, translated off-screen, carousel arrows at the end of their range) must also leave the tab order: `tabindex="-1"`, or `disabled` on a button.
- One tab stop per card. When an image, title and byline all link to the same post, keep the title link. Give the others `tabindex="-1"` and `aria-hidden="true"`. They stay clickable for mouse users.
- Links need an accessible name. An image-only link needs `alt` text, or must be hidden from assistive tech as a duplicate.
- Link text should make sense out of context. When it can't ("Listen now"), add `aria-describedby` pointing at the text that names the destination.

## Headings

- Headings describe structure, not size. Visual size comes from utility classes (`font-size-*`), so changing the tag doesn't change the look.
- Page sections get `<h2>`, items within them `<h3>`, and so on, without skipping levels downward.
- Bylines, dates, standfirsts and labels are not headings. Use `<p>` or `<span>` with the same classes.
- Never output an empty heading. Wrap conditional content so the tag only renders when there's text.
- When a logo or image stands in for a section title, add a `u-visuallyhidden` heading with the section name and give the image `aria-hidden="true"`.
- Watch `.text-copy`: it styles heading tags and gives `<p>` a serif font, so retagging inside it can change the look.

## Skip link and landmarks

- `header.php` renders a "Skip to main content" link as the first focusable element. It's off-screen until focused.
- Every template's `<main>` must be `<main id="main-content">`, or the skip link breaks.

## Motion

- Autoplay, tickers and other continuous motion respect `prefers-reduced-motion: reduce`.
- Scope reduced-motion rules to the component (carousel, ticker). Don't use a global `*` rule that kills every transition on the site.
- In JavaScript, check `window.matchMedia('(prefers-reduced-motion: reduce)')` before starting autoplay.

## Focus

- Never remove a focus outline without an equally visible replacement.
- Buttons and links use `:focus-visible` by default, so mouse clicks don't show a ring. Don't add focus styles that fire on click.

## Forms

- Every input has a `<label>`. `u-visuallyhidden` is acceptable where the design shows no label.
- `spellcheck="false"` on name and email inputs.
- Whole-number amounts use `type="text" inputmode="numeric" pattern="[0-9]*"`, not `type="number"`. It gives a better mobile keypad, and scrolling over the field can't change the value.

## Checking your change

1. **Keyboard pass:** reload, then use Tab, Shift+Tab, Enter, Space and the arrow keys. Every control is reachable, visibly focused, and operable. Nothing invisible takes focus.
2. **Reduced motion:** macOS System Settings → Accessibility → Display → Reduce motion, then reload.
3. **Screen reader spot check:** VoiceOver (Cmd+F5). Check names, states ("expanded", "checked") and the headings list (VO+U).
4. **Playwright:** assert behaviour and structure, not editorial content: an attribute flips, a panel shows, an element exists. See the keyboard steps in `tests/e2e/homepage.spec.js`.
