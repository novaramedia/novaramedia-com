<?php
/**
 * Tests for lib/functions-utility.php.
 *
 * phpcs:disable WordPress.WP.AlternativeFunctions -- tests create and remove
 * their own temp files; WP_Filesystem is not available or wanted here.
 */

namespace NM\Tests\Lib;

use Brain\Monkey\Functions;
use NM\Tests\TestCase;

final class FunctionsUtilityTest extends TestCase {

  public static function setUpBeforeClass(): void {
    self::requireThemeFile( 'lib/functions-utility.php' );
  }

  public function test_nm_is_production_is_true_only_for_the_production_environment(): void {
    Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
    $this->assertTrue( nm_is_production() );

    Functions\when( 'wp_get_environment_type' )->justReturn( 'staging' );
    $this->assertFalse( nm_is_production() );
  }

  public function test_production_assets_are_versioned_by_the_theme_version(): void {
    Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
    Functions\expect( 'get_template_directory' )->never();

    $this->assertSame( '4.10.0', nm_asset_version_for_environment( '/dist/main.css', '4.10.0' ) );
  }

  public function test_elsewhere_a_missing_asset_falls_back_to_the_theme_version(): void {
    Functions\when( 'wp_get_environment_type' )->justReturn( 'staging' );
    Functions\when( 'get_template_directory' )->justReturn( sys_get_temp_dir() . '/nm-theme-missing' );

    $this->assertSame( '4.10.0', nm_asset_version_for_environment( '/dist/main.css', '4.10.0' ) );
  }

  public function test_elsewhere_an_existing_asset_is_versioned_by_its_modification_time(): void {
    $dir = sys_get_temp_dir() . '/nm-theme-' . uniqid();
    mkdir( $dir . '/dist', 0777, true );
    $file = $dir . '/dist/main.css';
    file_put_contents( $file, 'body{}' );
    touch( $file, 1700000000 );
    clearstatcache( true, $file );
    $expected = (string) filemtime( $file );

    Functions\when( 'wp_get_environment_type' )->justReturn( 'local' );
    Functions\when( 'get_template_directory' )->justReturn( $dir );

    try {
      $this->assertSame( $expected, nm_asset_version_for_environment( '/dist/main.css', '4.10.0' ) );
      $this->assertNotSame( '4.10.0', $expected );
    } finally {
      unlink( $file );
      rmdir( $dir . '/dist' );
      rmdir( $dir );
    }
  }
}
