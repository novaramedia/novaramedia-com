# Context-Specific Support Form Text Override

## Overview

The `render_support_form` function now supports context-specific copy overrides that take priority over global configurations. This allows you to customize the heading and text for both donation modes (regular and one-off) on a per-template basis.

## Usage

Pass an array of copy overrides to the `render_support_form` function via the `$copy` parameter:

```php
<?php
render_support_form(
  'banner',  // $variant
  false,     // $white_mobile_schedule
  '',        // $container_classes
  array(     // $copy - context-specific overrides
    'regular' => array(
      'heading' => 'Support Our Newsletter',
      'text'    => 'Help us keep this newsletter free and independent.'
    ),
    'oneoff'  => array(
      'heading' => 'Make a One-Time Contribution',
      'text'    => 'Every donation helps us continue our work.'
    )
  )
);
?>
```

## Parameter Structure

The `$copy` parameter accepts an associative array with the following structure:

```php
array(
  'regular' => array(
    'heading' => 'Heading text for monthly donations',
    'text'    => 'Body text for monthly donations'
  ),
  'oneoff' => array(
    'heading' => 'Heading text for one-off donations',
    'text'    => 'Body text for one-off donations'
  )
)
```

### Fields

- **`regular`** (array, optional): Copy for monthly/regular donations
  - **`heading`** (string, optional): Heading text
  - **`text`** (string, optional): Body/description text
- **`oneoff`** (array, optional): Copy for one-off donations
  - **`heading`** (string, optional): Heading text
  - **`text`** (string, optional): Body/description text

## Priority Order

The copy is selected in the following priority order (highest to lowest):

1. **Context-specific overrides** - Passed via the `$copy` parameter
2. **Global donation mode overrides** - Configured in fundraising settings
3. **Global default settings** - Configured in fundraising settings
4. **Hardcoded defaults** - "Support Novara Media" and "Help us fund independent journalism."

## Validation

The function validates all copy overrides:

- Only string values are accepted
- Empty strings (after trimming whitespace) are ignored
- Invalid data structures are ignored silently
- The form continues to work with global settings if overrides are invalid

## Examples

### Example 1: Newsletter Page

```php
<?php
get_template_part(
  'partials/support-section',
  null,
  array(
    'container_classes' => 'mb-4',
    'copy' => array(
      'regular' => array(
        'heading' => 'Support Quality Journalism',
        'text'    => 'Subscribe monthly to keep our newsletters free.'
      ),
      'oneoff'  => array(
        'heading' => 'One-Time Support',
        'text'    => 'Make a single contribution to our work.'
      )
    )
  )
);
?>
```

### Example 2: Event Page

```php
<?php
render_support_form(
  'condensed',
  false,
  'grid-item is-m-24 is-xxl-12',
  array(
    'regular' => array(
      'heading' => 'Fund Our Events',
      'text'    => 'Monthly supporters help us organize more events like this.'
    ),
    'oneoff'  => array(
      'heading' => 'Support This Event',
      'text'    => 'Help cover the costs of bringing people together.'
    )
  )
);
?>
```

### Example 3: Partial Override

You can override only specific fields. Other fields will fall back to global settings:

```php
<?php
render_support_form(
  'banner',
  false,
  '',
  array(
    'regular' => array(
      'heading' => 'Custom Heading for Monthly'
      // 'text' will fall back to global settings
    )
    // 'oneoff' will use global settings entirely
  )
);
?>
```

## Template Part Usage

When using `get_template_part` with the `partials/support-section` partial, pass the copy overrides in the `$args` array:

```php
<?php
get_template_part(
  'partials/support-section',
  null,
  array(
    'container_classes' => 'mb-4',
    'copy' => array(
      'regular' => array(
        'heading' => 'Your Heading',
        'text'    => 'Your text'
      )
    )
  )
);
?>
```

Then update the `partials/support-section.php` file to pass the copy parameter:

```php
<?php
$copy = isset( $args['copy'] ) ? $args['copy'] : array();
render_support_form( 'banner', $on_colored_background, 'grid-item is-xxl-24', $copy );
?>
```

## Technical Implementation

### PHP Side

1. The `render_support_form` function validates the `$copy` parameter
2. Valid overrides are passed to `render_support_heading_and_text`
3. Overrides are also encoded as JSON and stored in a `data-copy-override` attribute on the form element

### JavaScript Side

1. The `Support.js` module reads the `data-copy-override` attribute on form initialization
2. The override data is stored using jQuery's `.data()` method
3. When switching between donation types, `updateSupportSectionCopy()` checks context-specific overrides first before falling back to global settings

## Backward Compatibility

The feature is fully backward compatible:

- All existing calls to `render_support_form()` continue to work without modification
- The `$copy` parameter is optional and defaults to an empty array
- If no overrides are provided, the function behaves exactly as before
