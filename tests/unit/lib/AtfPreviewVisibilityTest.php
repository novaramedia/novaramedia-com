<?php
/**
 * Tests for nm_atf_preview_visible_ids() in lib/admin/atf-preview.php: slot
 * ids submitted to the preview route pass the nm_resolve_post() visibility
 * gate before anything about them is rendered.
 */

namespace NM\Tests\Lib;

use Brain\Monkey\Functions;
use NM\Tests\TestCase;

final class AtfPreviewVisibilityTest extends TestCase {

  public static function setUpBeforeClass(): void {
    if ( ! defined( 'ABSPATH' ) ) {
      define( 'ABSPATH', NM_THEME_ROOT . '/' );
    }
    self::requireThemeFile( 'lib/admin/post-resolve.php' );
    self::requireThemeFile( 'lib/admin/atf-preview.php' );
  }

  protected function setUp(): void {
    parent::setUp();
    Functions\when( 'absint' )->alias( fn( $v ) => abs( (int) $v ) );
    Functions\when( 'get_post_status_object' )->justReturn( (object) array( 'label' => 'Published' ) );
    Functions\when( 'get_the_title' )->justReturn( 'Title' );
    Functions\when( 'get_the_date' )->justReturn( '1 Jan 2026' );
    Functions\when( 'get_the_post_thumbnail_url' )->justReturn( false );
    Functions\when( 'get_post_status' )->justReturn( 'publish' );
    Functions\when( 'current_user_can' )->justReturn( false );
    // 1-99 are posts; 100+ are an internal, non-viewable type (e.g. wp_block).
    Functions\when( 'get_post' )->alias( fn( $id ) => (object) array( 'ID' => $id, 'post_type' => $id >= 100 ? 'wp_block' : 'post' ) );
    Functions\when( 'is_post_type_viewable' )->alias( fn( $type ) => 'post' === $type );
  }

  public function test_visible_and_unset_ids_pass_through(): void {
    $this->assertSame( array( 5, 0, 7 ), nm_atf_preview_visible_ids( array( 5, 0, 7 ) ) );
  }

  public function test_internal_type_the_user_cannot_edit_is_dropped(): void {
    $this->assertSame( array( 5, 0, 7 ), nm_atf_preview_visible_ids( array( 5, 150, 7 ) ) );
  }
}
