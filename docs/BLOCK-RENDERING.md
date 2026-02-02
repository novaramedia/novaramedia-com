# Block Rendering on Single Posts

## Overview

Single posts use custom block rendering logic to wrap content blocks in `.text-copy` containers while allowing certain blocks (like the newsletter signup) to render full-width.

**File:** `partials/singles/single-post-articles.php`

## How It Works

1. Get raw content with `get_the_content()`
2. Parse blocks with `parse_blocks()`
3. Check if content has real Gutenberg blocks
4. If classic editor: apply `the_content` filter once, wrap in `.text-copy`
5. If block editor: render each block individually with `render_block()`, apply `the_content` filter, wrap in `.text-copy` (except newsletter block)

## The `the_content` Filter

We apply `apply_filters('the_content', $rendered_content)` to each rendered block to ensure:

- Embeds are processed correctly
- Shortcodes work in Custom HTML blocks
- Content enhancement filters run (lazy loading, etc.)

This follows the [WordPress documentation for render_block()](https://developer.wordpress.org/reference/functions/render_block/) which recommends applying `the_content` filter to rendered output.

## wpautop Consideration

### Current Behaviour

We apply `the_content` filter to already-rendered block HTML. This means `wpautop()` may run on the output.

### Why This Works (Usually)

`wpautop()` is designed to add `<p>` tags to plain text content. It's smart enough not to wrap block-level HTML elements like:

- `<figure>` (images, embeds)
- `<div>` (groups, columns)
- `<blockquote>` (quotes)
- `<ul>`, `<ol>` (lists)
- `<h1>`-`<h6>` (headings)
- `<table>` (tables)

Since most Gutenberg blocks render as block-level elements, `wpautop` typically has no effect.

### How WordPress Core Handles It

In [`do_blocks()`](https://developer.wordpress.org/reference/functions/do_blocks/), WordPress removes `wpautop` when blocks are detected:

```php
// If there are blocks in this content, we shouldn't run wpautop() on it later.
$priority = has_filter( 'the_content', 'wpautop' );
if ( false !== $priority && doing_filter( 'the_content' ) && has_blocks( $content ) ) {
    remove_filter( 'the_content', 'wpautop', $priority );
    add_filter( 'the_content', '_restore_wpautop_hook', $priority + 1 );
}
```

### Why We Don't Do This (Yet)

Our implementation applies `the_content` to **already-rendered HTML**, so:

- `has_blocks()` returns `false` (it's HTML, not block markup)
- WordPress doesn't automatically remove `wpautop`

In practice, this hasn't caused issues because block output is block-level HTML.

### Potential Future Optimisation

If issues arise with unwanted `<p>` tags, implement the WordPress-recommended pattern:

```php
// Temporarily remove wpautop
$wpautop_priority = has_filter( 'the_content', 'wpautop' );
if ( false !== $wpautop_priority ) {
    remove_filter( 'the_content', 'wpautop', $wpautop_priority );
}

echo '<div class="text-copy">' . apply_filters( 'the_content', $rendered_content ) . '</div>';

// Restore wpautop
if ( false !== $wpautop_priority ) {
    add_filter( 'the_content', 'wpautop', $wpautop_priority );
}
```

Or create a helper function:

```php
/**
 * Apply the_content filter without wpautop for block content.
 *
 * @param string $content Rendered block content.
 * @return string Filtered content.
 */
function nm_filter_block_content( $content ) {
    $wpautop_priority = has_filter( 'the_content', 'wpautop' );

    if ( false !== $wpautop_priority ) {
        remove_filter( 'the_content', 'wpautop', $wpautop_priority );
    }

    $filtered = apply_filters( 'the_content', $content );

    if ( false !== $wpautop_priority ) {
        add_filter( 'the_content', 'wpautop', $wpautop_priority );
    }

    return $filtered;
}
```

## Special Block Handling

### Newsletter Signup Block (`nm-wp/newsletter-signup`)

Rendered without `.text-copy` wrapper to allow full-width styling.

```php
if ( $block['blockName'] === 'nm-wp/newsletter-signup' ) {
    echo render_block( $block );
}
```

### Empty Blocks

Skipped to avoid empty `.text-copy` containers:

```php
if ( trim( $rendered_content ) !== '' ) {
    // render
}
```

## References

- [render_block() - WordPress Developer Reference](https://developer.wordpress.org/reference/functions/render_block/)
- [do_blocks() - WordPress Developer Reference](https://developer.wordpress.org/reference/functions/do_blocks/)
- [Display Gutenberg Blocks Outside Content - Florian Brinkmann](https://florianbrinkmann.com/en/display-specific-gutenberg-blocks-of-a-post-outside-of-the-post-content-in-the-theme-5620/)
- [wpautop() - WordPress Developer Reference](https://developer.wordpress.org/reference/functions/wpautop/)
