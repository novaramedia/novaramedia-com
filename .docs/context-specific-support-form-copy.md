# Support Form Documentation

## render_support_form()

Renders the donation support form with optional context-specific copy overrides.

### Parameters

- `$variant` (string) - Display variant: 'banner' or 'condensed'
- `$white_mobile_schedule` (bool) - Use white background for mobile schedule buttons
- `$container_classes` (string) - Additional CSS classes for container
- `$copy` (array, optional) - Context-specific text overrides for donation modes

### Copy Override Structure

```php
array(
  'regular' => array(
    'heading' => 'Heading for monthly donations',
    'text'    => 'Body text for monthly donations'
  ),
  'oneoff' => array(
    'heading' => 'Heading for one-off donations',
    'text'    => 'Body text for one-off donations'
  )
)
```

### Basic Usage

```php
render_support_form( 'banner', false, '', array(
  'regular' => array(
    'heading' => 'Support Our Work',
    'text'    => 'Help us fund independent journalism.'
  )
));
```

### Usage with Template Parts

```php
get_template_part( 'partials/support-section', null, array(
  'container_classes' => 'mb-4',
  'copy' => array(
    'regular' => array(
      'heading' => 'Support Quality Journalism',
      'text'    => 'Subscribe monthly to keep our newsletters free.'
    ),
    'oneoff' => array(
      'heading' => 'One-Time Support',
      'text'    => 'Make a single contribution.'
    )
  )
));
```

## Priority Order

Copy is selected in the following order:

1. Context-specific overrides (via `$copy` parameter)
2. Global donation mode settings
3. Global default settings
4. Hardcoded defaults

## Notes

- All parameters are optional and validated
- Invalid overrides are silently ignored
- Empty strings are treated as invalid
- Fully backward compatible with existing code
