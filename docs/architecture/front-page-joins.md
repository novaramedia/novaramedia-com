# Front-page joins

Front-page sections carry no outer vertical margin. The gap between two
neighbouring sections — the *join* — is rendered by `NM_Front_Page_Sequence`
(`lib/front-page-sequence.php`) from the two sections' *frames*.

## Frames

Declared per block in the registry (`nm_get_front_page_block_registry()`,
`lib/theme-options/options-front-page.php`) and hardcoded for the two bookends
in `front-page.php`.

| Frame | Meaning |
|---|---|
| `contained` | The section has a visible edge of its own: a coloured rounded box, or a full-bleed coloured band |
| `open` | Content sits directly on the page background |

Newsletter signups are always `contained`: `partials/email-signup.php` draws its
box unless a caller passes `unboxed`, and the front page does not.
`nm_render_newsletter_signup()` (`lib/renderers.php`) passes
`container-classes => ''` to drop the partial's own outer margins; other
templates that use the partial keep them.

## Joins

| Previous | Next | Kind | Classes |
|---|---|---|---|
| contained | contained | `tight` | `pt-4` |
| contained | open (either order) | `loose` | `pt-5` |
| open | open | `divided` | `container pt-5 pb-5` around an `<hr>` |

Tune the values in `nm_get_front_page_join_classes()` only. Joins use padding,
so they never collapse with anything.

## Rules for section partials

1. **No outer `mt-*`/`mb-*` on the root element.** The Mega Block's `mb-5` is
   the single exception: it is the permanent tail and that margin is the lead-out
   to the footer.
2. **Return before emitting markup when there is nothing to show.** The sequence
   buffers each section and skips empty output; an empty `<section>` shell would
   still earn a join.
3. **End flush with the last content.** `.grid-row` is a flex container, so a
   trailing `mb-*` on a last-row item adds to the section's height. Put row gaps
   above items (`mt-*`) or use stack-only variants (`mb-s-4`, `mb-l-4`).
4. **Never draw your own divider.** The `divided` join does that when two open
   sections meet.

## Adding a block

Add the registry entry with a `frame`, follow the rules above, and the sequence
does the rest. The Layout editor needs no change: the frame belongs to the block,
not the placement.

## Markup

```html
<div class="front-page-section" data-testid="front-page-section" data-frame="open">…</div>
<div class="front-page-join front-page-join--divided container pt-5 pb-5" data-testid="front-page-join" data-join="divided"><hr></div>
<div class="front-page-section" data-testid="front-page-section" data-frame="open">…</div>
```

`tests/e2e/homepage.spec.js` asserts that every join sits between two sections
and that its kind matches their frames. `tests/unit/lib/FrontPageSequenceTest.php`
covers the join logic and the skipping of empty sections.
