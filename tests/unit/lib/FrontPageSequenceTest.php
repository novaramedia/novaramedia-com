<?php
/**
 * Tests for the front-page join logic in lib/front-page-sequence.php.
 */

namespace NM\Tests\Lib;

use Brain\Monkey\Functions;
use NM\Tests\TestCase;
use NM_Front_Page_Sequence;
use PHPUnit\Framework\Attributes\DataProvider;

final class FrontPageSequenceTest extends TestCase {

  public static function setUpBeforeClass(): void {
    if ( ! defined( 'ABSPATH' ) ) {
      define( 'ABSPATH', NM_THEME_ROOT . '/' );
    }

    self::requireThemeFile( 'lib/front-page-sequence.php' );
  }

  protected function setUp(): void {
    parent::setUp();
    Functions\when( 'esc_attr' )->returnArg();
  }

  #[DataProvider( 'frame_pairs' )]
  public function test_join_kind_comes_from_the_pair_of_frames( string $previous, string $next, string $kind ): void {
    $this->assertSame( $kind, nm_get_front_page_join_kind( $previous, $next ) );
  }

  public static function frame_pairs(): array {
    return array(
      'two boxes'       => array( 'contained', 'contained', 'tight' ),
      'box then open'   => array( 'contained', 'open', 'loose' ),
      'open then box'   => array( 'open', 'contained', 'loose' ),
      'two open'        => array( 'open', 'open', 'divided' ),
    );
  }

  public function test_unknown_join_kind_falls_back_to_loose_classes(): void {
    $this->assertSame( nm_get_front_page_join_classes( 'loose' ), nm_get_front_page_join_classes( 'nonsense' ) );
  }

  public function test_block_frame_reads_the_registry(): void {
    Functions\when( 'nm_get_front_page_block_registry' )->justReturn(
      array( 'highlight-block' => array( 'frame' => 'open' ) )
    );

    $this->assertSame( 'open', nm_get_front_page_block_frame( 'highlight-block' ) );
  }

  public function test_newsletter_and_unknown_slugs_are_contained(): void {
    Functions\when( 'nm_get_front_page_block_registry' )->justReturn( array() );

    $this->assertSame( 'contained', nm_get_front_page_block_frame( 'newsletter-signup-12' ) );
    $this->assertSame( 'contained', nm_get_front_page_block_frame( '' ) );
  }

  public function test_only_a_divided_join_draws_a_rule(): void {
    $this->assertStringContainsString( '<hr>', $this->capture( 'nm_render_front_page_join', 'divided' ) );
    $this->assertStringNotContainsString( '<hr>', $this->capture( 'nm_render_front_page_join', 'tight' ) );
    $this->assertStringNotContainsString( '<hr>', $this->capture( 'nm_render_front_page_join', 'loose' ) );
  }

  public function test_sequence_joins_rendered_sections_and_skips_empty_ones(): void {
    $sequence = new NM_Front_Page_Sequence();

    ob_start();
    $sequence->render( 'open', static function () { echo '<p>one</p>'; } );
    $sequence->render( 'contained', static function () { echo "  \n"; } ); // renders nothing
    $sequence->render( 'open', static function () { echo '<p>two</p>'; } );
    $sequence->render( 'contained', static function () { echo '<p>three</p>'; } );
    $sequence->render( 'contained', static function () { echo '<p>four</p>'; } );
    $html = ob_get_clean();

    $this->assertSame( 4, substr_count( $html, 'data-testid="front-page-section"' ) );

    preg_match_all( '/data-join="([a-z]+)"/', $html, $joins );
    $this->assertSame( array( 'divided', 'loose', 'tight' ), $joins[1] );

    // No join leads or trails the sequence.
    $this->assertStringStartsWith( '<div class="front-page-section"', $html );
    $this->assertStringEndsWith( '<p>four</p></div>', $html );
  }

  private function capture( callable $fn, string $arg ): string {
    ob_start();
    $fn( $arg );

    return ob_get_clean();
  }
}
