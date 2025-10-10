# WordPress Theme Security Guidelines

This document outlines the security best practices implemented in the Novara Media WordPress theme and guidelines for maintaining security in future development.

## Implemented Security Measures

### 1. Direct Access Protection

All PHP files now include ABSPATH checks:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
```

This prevents direct file access outside the WordPress context.

### 2. Input Sanitization

- All `$_GET`, `$_POST`, `$_SERVER` variables are properly sanitized
- Use `absint()` for integer values from user input
- Use `sanitize_text_field()` for text input
- Use `wp_unslash()` when dealing with WordPress-slashed data

**Examples:**

```php
// Correct
$post_id = absint( $_GET['post'] );
$host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );

// Incorrect
$post_id = $_GET['post']; // No sanitization
```

### 3. Output Escaping

All dynamic output is properly escaped:

- `esc_html()` for HTML content
- `esc_attr()` for HTML attributes
- `esc_url()` for URLs
- `esc_js()` for JavaScript

**Examples:**

```php
// Correct
echo esc_html( $job->post_title );
echo esc_url( get_permalink( $job ) );

// Incorrect
echo $job->post_title; // No escaping
```

### 4. File Inclusion Security

The `nm_get_file()` function includes:

- Path sanitization to remove traversal attempts
- `realpath()` validation to ensure files are within theme directory
- File existence checks

### 5. Security Headers

The following security headers are automatically added:

- `X-Frame-Options: SAMEORIGIN` - Prevents clickjacking
- `X-Content-Type-Options: nosniff` - Prevents MIME sniffing
- `X-XSS-Protection: 1; mode=block` - Enables XSS filtering
- `Referrer-Policy: strict-origin-when-cross-origin` - Privacy protection

### 6. Attack Surface Reduction

- XML-RPC disabled (reduces brute force vectors)
- WordPress version info removed from HTML and assets
- Generator meta tag removed

## Development Guidelines

### Required Practices

1. **Always add ABSPATH checks** to new PHP files:

   ```php
   <?php
   if ( ! defined( 'ABSPATH' ) ) {
       exit; // Exit if accessed directly
   }
   ```

2. **Sanitize all input** from `$_GET`, `$_POST`, `$_SERVER`:

   ```php
   $user_input = sanitize_text_field( wp_unslash( $_POST['field_name'] ) );
   ```

3. **Escape all output**:

   ```php
   echo esc_html( $variable );
   ```

4. **Use WordPress functions** for common operations:
   - `wp_redirect()` instead of `header('Location: ...')`
   - `wp_safe_redirect()` for user-controlled redirects
   - WordPress database functions instead of raw SQL

5. **Validate user capabilities** for admin functions:

   ```php
   if ( ! current_user_can( 'manage_options' ) ) {
       wp_die( 'Insufficient permissions' );
   }
   ```

6. **Use nonces** for forms and AJAX:

   ```php
   wp_nonce_field( 'action_name', 'nonce_name' );

   if ( ! wp_verify_nonce( $_POST['nonce_name'], 'action_name' ) ) {
       wp_die( 'Security check failed' );
   }
   ```

### Code Review Checklist

Before deploying code, verify:

- [ ] ABSPATH check at top of all PHP files
- [ ] All user input is sanitized
- [ ] All output is escaped
- [ ] No direct `$_GET`, `$_POST`, `$_SERVER` usage without sanitization
- [ ] User capability checks for admin functionality
- [ ] Nonce verification for forms
- [ ] No hardcoded secrets or credentials

### PHPCS Security Rules

The phpcs.xml configuration includes security-focused rules:

- `WordPress.Security.EscapeOutput`
- `WordPress.Security.NonceVerification`
- `WordPress.Security.ValidatedSanitizedInput`
- `WordPress.Security.SafeRedirect`

Run PHPCS regularly: `./vendor/bin/phpcs`

## Security Resources

- [WordPress Security Guidelines](https://developer.wordpress.org/plugins/security/)
- [Data Validation](https://developer.wordpress.org/plugins/security/data-validation/)
- [Securing Output](https://developer.wordpress.org/plugins/security/securing-output/)
- [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)

## Incident Response

If a security vulnerability is discovered:

1. Document the issue privately
2. Implement a fix following these guidelines
3. Test thoroughly in a staging environment
4. Deploy to production during low-traffic periods
5. Monitor for any issues post-deployment

## Updates and Maintenance

- Regularly update WordPress core, themes, and plugins
- Monitor security advisories
- Run PHPCS security checks before each deployment
- Conduct periodic security audits (annually or after major changes)
