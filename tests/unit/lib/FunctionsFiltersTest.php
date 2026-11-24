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

  public function test_cmb2_value_is_default_sanitised_then_stripped(): void {
    $sanitizer = $this->sanitizer_returning( "Desc.\n\n&nbsp;" );

    $this->assertSame( 'Desc.', nm_cmb2_strip_empty_paragraphs( null, 'raw', 1, array( 'type' => 'wysiwyg' ), $sanitizer ) );
    $this->assertSame( 1, $sanitizer->calls );
  }

  public function test_cmb2_existing_override_passes_through(): void {
    $sanitizer = $this->sanitizer_returning( 'unused' );

    $this->assertSame( 'kept', nm_cmb2_strip_empty_paragraphs( 'kept', 'raw', 1, array( 'type' => 'wysiwyg' ), $sanitizer ) );
    $this->assertSame( 0, $sanitizer->calls );
  }

  public function test_cmb2_array_values_are_stripped_deeply(): void {
    $sanitizer = $this->sanitizer_returning( array( "A.\n\n&nbsp;", array( 'k' => '<p></p>B.' ), 5 ) );

    $this->assertSame(
      array( 'A.', array( 'k' => 'B.' ), 5 ),
      nm_cmb2_strip_empty_paragraphs( null, array(), 1, array( 'type' => 'textarea_small' ), $sanitizer )
    );
  }

  public function test_cmb2_repeatable_entries_left_empty_by_stripping_are_dropped(): void {
    $sanitizer = $this->sanitizer_returning( array( 'Yara Eid&nbsp;', '&nbsp;', '0', 'Tareq Baconi' ) );

    $this->assertSame(
      array( 'Yara Eid', '0', 'Tareq Baconi' ),
      nm_cmb2_strip_empty_paragraphs( null, array(), 1, array( 'type' => 'text', 'repeatable' => true ), $sanitizer )
    );
  }

  public function test_cmb2_non_repeatable_array_keeps_entries_left_empty(): void {
    $sanitizer = $this->sanitizer_returning( array( 'a' => 'A.', 'b' => '&nbsp;' ) );

    $this->assertSame(
      array( 'a' => 'A.', 'b' => '' ),
      nm_cmb2_strip_empty_paragraphs( null, array(), 1, array( 'type' => 'text' ), $sanitizer )
    );
  }

  /**
   * Stand-in for CMB2_Sanitize: any type method returns $result, as CMB2's
   * default sanitiser for that type would.
   */
  private function sanitizer_returning( $result ): object {
    return new class( $result ) {
      public int $calls = 0;

      public function __construct( private $result ) {}

      public function __call( $type, $args ) {
        $this->calls++;
        return $this->result;
      }
    };
  }
}
