<?php
/**
 * Base class for the theme's PHP unit tests.
 *
 * Sets up and tears down Brain Monkey around every test, so WordPress
 * functions can be stubbed with Brain\Monkey\Functions\when() / expect() and
 * hooks with Brain\Monkey\Actions / Filters, and nothing leaks between tests.
 */

namespace NM\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase {

  protected function setUp(): void {
    parent::setUp();
    Monkey\setUp();
  }

  protected function tearDown(): void {
    Monkey\tearDown();
    parent::tearDown();
  }

  /**
   * Load a theme file once, the way functions.php loads it with
   * get_template_part(). Call from setUpBeforeClass() in the test class.
   *
   * Files that call WordPress functions at load time (add_action, add_filter)
   * work: Brain Monkey provides those. Anything else WordPress-specific that
   * runs at load must be stubbed with Functions\when() before the require.
   *
   * @param string $relative Path from the theme root, e.g. 'lib/functions-utility.php'.
   */
  protected static function requireThemeFile( string $relative ): void {
    require_once NM_THEME_ROOT . '/' . ltrim( $relative, '/' );
  }
}
