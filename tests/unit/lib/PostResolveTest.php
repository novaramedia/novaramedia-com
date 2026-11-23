<?php
/**
 * Tests for the visibility gate in nm_resolve_post() (lib/admin/post-resolve.php).
 */

namespace NM\Tests\Lib;

use Brain\Monkey\Functions;
use NM\Tests\TestCase;

final class PostResolveTest extends TestCase {

  public static function setUpBeforeClass(): void {
    if ( ! defined( 'ABSPATH' ) ) {
      define( 'ABSPATH', NM_THEME_ROOT . '/' );
    }
    self::requireThemeFile( 'lib/admin/post-resolve.php' );
  }

  protected function setUp(): void {
    parent::setUp();
    Functions\when( 'absint' )->alias( fn( $v ) => abs( (int) $v ) );
    Functions\when( 'get_post_status_object' )->justReturn( (object) array( 'label' => 'Published' ) );
    Functions\when( 'get_the_title' )->justReturn( 'Title' );
    Functions\when( 'wp_strip_all_tags' )->alias( 'strip_tags' );
    Functions\when( 'get_the_date' )->justReturn( '1 Jan 2026' );
    Functions\when( 'get_the_post_thumbnail_url' )->justReturn( false );
  }

  public function test_missing_post_is_not_found(): void {
    Functions\when( 'get_post' )->justReturn( null );

    $this->assertFalse( nm_resolve_post( 5 )['found'] );
  }

  public function test_zero_id_is_not_found_without_reading_the_global_post(): void {
    Functions\expect( 'get_post' )->never();

    $this->assertFalse( nm_resolve_post( 0 )['found'] );
  }

  public function test_title_markup_is_stripped(): void {
    $this->stub_post( 'post', 'publish', true, false );
    Functions\when( 'get_the_title' )->justReturn( 'Review: <em>Capital</em> at 150' );

    $this->assertSame( 'Review: Capital at 150', nm_resolve_post( 5 )['title'] );
  }

  public function test_published_viewable_post_resolves_without_edit_rights(): void {
    $this->stub_post( 'post', 'publish', true, false );

    $info = nm_resolve_post( 5 );
    $this->assertTrue( $info['found'] );
    $this->assertSame( 'Title', $info['title'] );
    $this->assertNull( $info['thumbnail'] );
  }

  public function test_draft_hidden_from_user_who_cannot_edit_it(): void {
    $this->stub_post( 'post', 'draft', true, false );

    $info = nm_resolve_post( 5 );
    $this->assertFalse( $info['found'] );
    $this->assertNull( $info['title'] );
  }

  public function test_draft_resolves_for_user_who_can_edit_it(): void {
    $this->stub_post( 'post', 'draft', true, true );

    $this->assertTrue( nm_resolve_post( 5 )['found'] );
  }

  public function test_published_internal_type_hidden_from_user_who_cannot_edit_it(): void {
    $this->stub_post( 'wp_block', 'publish', false, false );

    $this->assertFalse( nm_resolve_post( 5 )['found'] );
  }

  private function stub_post( string $type, string $status, bool $viewable, bool $can_edit ): void {
    Functions\when( 'get_post' )->justReturn( (object) array( 'ID' => 5, 'post_type' => $type ) );
    Functions\when( 'get_post_status' )->justReturn( $status );
    Functions\when( 'is_post_type_viewable' )->justReturn( $viewable );
    Functions\when( 'current_user_can' )->justReturn( $can_edit );
  }
}
