<?php
/**
 * PHPUnit bootstrap for the theme's unit tests.
 *
 * Theme files are loaded per test class with TestCase::requireThemeFile(), and
 * WordPress functions are stubbed with Brain Monkey, so no WordPress install
 * or database is needed. See docs/testing/testing.md.
 */

define( 'NM_THEME_ROOT', dirname( __DIR__, 2 ) );

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';
