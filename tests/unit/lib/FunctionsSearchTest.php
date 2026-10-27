<?php
/**
 * Tests for lib/functions-search.php.
 */

namespace NM\Tests\Lib;

use Brain\Monkey\Functions;
use NM\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class FunctionsSearchTest extends TestCase {

  public static function setUpBeforeClass(): void {
    self::requireThemeFile( 'lib/functions-search.php' );
  }

  /**
   * Queries that should suggest the destination, as [ lowercased query, keywords ].
   */
  public static function matching_queries(): array {
    return array(
      'exact name'                   => array( 'novara live', array( 'Novara Live' ) ),
      'name inside a longer query'   => array( 'novara live gaza', array( 'Novara Live' ) ),
      'start of a name'              => array( 'nov', array( 'Novara Live' ) ),
      'name joined to a prefix'      => array( 'bastani', array( 'IMOBastani' ) ),
      'alias'                        => array( 'dyor', array( 'Do Your Own Research', 'dyor' ) ),
      'plural of the name'           => array( 'interviews', array( 'Interview' ) ),
      'keyword with words around it' => array( 'novara merch shop', array( 'shop', 'merch' ) ),
    );
  }

  /**
   * Queries that only contain a keyword inside a longer word.
   */
  public static function non_matching_queries(): array {
    return array(
      'event inside prevent'         => array( 'prevent', array( 'event', 'events' ) ),
      'shop inside bishop'           => array( 'bishop of london', array( 'shop' ) ),
      'shop inside workshop'         => array( 'workshop', array( 'shop' ) ),
      'merch inside merchant'        => array( 'merchant navy', array( 'merch' ) ),
      'letters inside newsletters'   => array( 'newsletters', array( 'Letters' ) ),
      'news inside newsletters'      => array( 'newsletters', array( 'News' ) ),
      'feature inside featured'      => array( 'featured', array( 'Feature' ) ),
      'report inside reporter'       => array( 'reporter', array( 'Report' ) ),
      'subscribe inside unsubscribe' => array( 'unsubscribe', array( 'subscribe' ) ),
    );
  }

  #[DataProvider( 'matching_queries' )]
  public function test_query_matches_a_whole_keyword_or_part_of_one( string $query, array $keywords ): void {
    $this->assertTrue( nm_search_query_matches_keywords( $query, $keywords ) );
  }

  #[DataProvider( 'non_matching_queries' )]
  public function test_query_does_not_match_a_keyword_hidden_inside_another_word( string $query, array $keywords ): void {
    $this->assertFalse( nm_search_query_matches_keywords( $query, $keywords ) );
  }

  public function test_category_with_a_logo_uses_the_logo_tile(): void {
    Functions\when( 'get_term_meta' )->alias(
      fn( $id, $key ) => $key === '_nm_category_logo_id' ? '11' : ''
    );
    Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.com/logo.png' );

    $this->assertSame(
      array(
        'kind'          => 'logo',
        'attachment_id' => 11,
      ),
      nm_get_search_destination_image( $this->category() )
    );
  }

  public function test_category_with_a_deleted_logo_falls_back_to_its_share_image(): void {
    Functions\when( 'get_term_meta' )->alias(
      fn( $id, $key ) => array(
        '_nm_category_logo_id'     => '11',
        '_nm_category_og_image_id' => '22',
      )[ $key ] ?? ''
    );
    Functions\when( 'wp_get_attachment_url' )->alias(
      fn( $id ) => $id === 22 ? 'https://example.com/share.jpg' : false
    );

    $this->assertSame(
      array(
        'kind'          => 'image',
        'attachment_id' => 22,
      ),
      nm_get_search_destination_image( $this->category() )
    );
  }

  public function test_category_whose_images_are_all_deleted_falls_back_to_the_brand_tile(): void {
    Functions\when( 'get_term_meta' )->justReturn( '33' );
    Functions\when( 'wp_get_attachment_url' )->justReturn( false );

    $this->assertSame( 'tile', nm_get_search_destination_image( $this->category() )['kind'] );
  }

  public function test_newsletter_with_a_deleted_featured_image_falls_back_to_the_brand_tile(): void {
    Functions\when( 'get_post_thumbnail_id' )->justReturn( 44 );
    Functions\when( 'wp_get_attachment_url' )->justReturn( false );

    $destination = array(
      'type'      => 'newsletter',
      'object_id' => 5,
    );

    $this->assertSame( 'tile', nm_get_search_destination_image( $destination )['kind'] );
  }

  private function category(): array {
    return array(
      'type'      => 'category',
      'object_id' => 7,
    );
  }
}
