<?php
/**
 * Tests for the save-time empty paragraph hooks in lib/functions-filters.php.
 */

namespace NM\Tests\Lib;

use Brain\Monkey\Functions;
use NM\Tests\TestCase;

final class FunctionsFiltersTest extends TestCase {

  public static function setUpBeforeClass(): void {
    self::requireThemeFile( 'lib/functions-utility.php' );
    self::requireThemeFile( 'lib/functions-filters.php' );
  }

  public function test_post_content_and_excerpt_are_stripped_for_editor_post_types(): void {
    Functions\expect( 'post_type_supports' )->once()->with( 'notice', 'editor' )->andReturn( true );

    $data = nm_strip_empty_paragraphs_on_post_save(
      array(
        'post_type'    => 'notice',
        'post_title'   => 'T',
        'post_content' => "One.\n\n&nbsp;",
        'post_excerpt' => "Ex.\n\n&nbsp;",
      )
    );

    $this->assertSame( 'One.', $data['post_content'] );
    $this->assertSame( 'Ex.', $data['post_excerpt'] );
    $this->assertSame( 'T', $data['post_title'] );
  }

  public function test_types_without_editor_support_are_untouched(): void {
    Functions\when( 'post_type_supports' )->justReturn( false );

    $data = array(
      'post_type'    => 'oembed_cache',
      'post_content' => '<p></p>',
      'post_excerpt' => '',
    );

    $this->assertSame( $data, nm_strip_empty_paragraphs_on_post_save( $data ) );
  }
}
