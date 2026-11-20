<?php
/**
 * Tests for nm_fill_above_the_fold_post_ids() in lib/functions-custom.php:
 * how the front page (and the admin preview) fill the 8 above-the-fold slots.
 */

namespace NM\Tests\Lib;

use Brain\Monkey\Functions;
use NM\Tests\TestCase;

final class AboveTheFoldFillTest extends TestCase {

  public static function setUpBeforeClass(): void {
    if ( ! defined( 'ABSPATH' ) ) {
      define( 'ABSPATH', NM_THEME_ROOT . '/' );
    }
    self::requireThemeFile( 'lib/functions-custom.php' );
  }

  protected function setUp(): void {
    parent::setUp();
    // Every ID is published except 900-999.
    Functions\when( 'get_post_status' )->alias( fn( $id ) => ( $id >= 900 && $id <= 999 ) ? 'draft' : 'publish' );
  }

  public function test_saved_slots_are_kept_in_position(): void {
    $this->assertSame(
      array( 1, 2, 3, 4, 5, 6, 7, 8 ),
      nm_fill_above_the_fold_post_ids( array( '1', '2', '3', '4', '5', '6', '7', '8' ), array( 50, 51 ) )
    );
  }

  public function test_unset_slots_fill_from_latest_in_order(): void {
    $this->assertSame(
      array( 1, 50, 3, 51, 5, 52, 7, 53 ),
      nm_fill_above_the_fold_post_ids( array( '1', '', '3', 0, '5', null, '7', '' ), array( 50, 51, 52, 53, 54 ) )
    );
  }

  public function test_fill_skips_latest_posts_already_saved_in_another_slot(): void {
    $this->assertSame(
      array( 50, 51, 52, 3, 4, 5, 6, 7 ),
      nm_fill_above_the_fold_post_ids( array( '50', '', '', '3', '4', '5', '6', '7' ), array( 50, 51, 52 ) )
    );
  }

  public function test_unpublished_saved_post_is_replaced_by_fill(): void {
    $this->assertSame(
      array( 50, 2, 3, 4, 5, 6, 7, 8 ),
      nm_fill_above_the_fold_post_ids( array( '950', '2', '3', '4', '5', '6', '7', '8' ), array( 50 ) )
    );
  }

  public function test_slots_stay_zero_when_the_pool_runs_out(): void {
    $this->assertSame(
      array( 50, 0, 0, 0, 0, 0, 0, 0 ),
      nm_fill_above_the_fold_post_ids( array_fill( 0, 8, '' ), array( 50 ) )
    );
  }
}
