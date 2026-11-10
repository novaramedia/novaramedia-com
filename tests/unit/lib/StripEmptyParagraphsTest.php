<?php
/**
 * Tests for nm_strip_empty_paragraphs() in lib/functions-utility.php.
 */

namespace NM\Tests\Lib;

use NM\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class StripEmptyParagraphsTest extends TestCase {

  public static function setUpBeforeClass(): void {
    self::requireThemeFile( 'lib/functions-utility.php' );
  }

  public function test_trailing_nbsp_lines_are_removed(): void {
    $this->assertSame( 'One.', nm_strip_empty_paragraphs( "One.\n\n&nbsp;\n\n&nbsp;" ) );
  }

  public function test_leading_blank_lines_are_removed(): void {
    $this->assertSame( 'One.', nm_strip_empty_paragraphs( "&nbsp;\n\nOne." ) );
  }

  public function test_mid_content_nbsp_line_is_removed(): void {
    $this->assertSame( "One.\n\nTwo.", nm_strip_empty_paragraphs( "One.\n\n&nbsp;\n\nTwo." ) );
  }

  public function test_empty_p_elements_are_removed_anywhere(): void {
    $this->assertSame(
      "<p>One.</p>\n<p>Two.</p>",
      nm_strip_empty_paragraphs( "<p>One.</p>\n<p>&nbsp;</p>\n<p></p>\n<p>Two.</p>\n<p><br></p>" )
    );
  }

  public function test_empty_p_with_attributes_and_slashes_is_removed(): void {
    $this->assertSame( '<p>One.</p>', nm_strip_empty_paragraphs( '<p>One.</p><p class=\"x\">&nbsp;</p>' ) );
  }

  public function test_trailing_br_in_a_paragraph_is_removed(): void {
    $this->assertSame( "<p>One.</p>\n\nTwo.", nm_strip_empty_paragraphs( "<p>One.<br></p>\n\nTwo.<br />" ) );
  }

  public function test_soft_br_inside_text_is_kept(): void {
    $this->assertSame( "Line one<br>\nLine two", nm_strip_empty_paragraphs( "Line one<br>\nLine two" ) );
  }

  public function test_inline_nbsp_is_kept(): void {
    $this->assertSame( 'It is 10&nbsp;km away.', nm_strip_empty_paragraphs( 'It is 10&nbsp;km away.' ) );
  }

  public function test_oembed_url_line_is_kept(): void {
    $value = "One.\n\nhttps://www.youtube.com/watch?v=abc\n\nTwo.";
    $this->assertSame( $value, nm_strip_empty_paragraphs( $value ) );
  }

  public function test_table_cell_nbsp_is_kept(): void {
    $value = '<table><tr><td>&nbsp;</td></tr></table>';
    $this->assertSame( $value, nm_strip_empty_paragraphs( $value ) );
  }

  public function test_nbsp_entity_variants_and_uppercase_tags(): void {
    $this->assertSame( 'One.', nm_strip_empty_paragraphs( "One.\n\n&#160;\n\n<P>&#xA0;</P>\n\n\u{00A0}<BR/>" ) );
  }

  public function test_crlf_line_endings(): void {
    $this->assertSame( "One.\r\n\r\nTwo.", nm_strip_empty_paragraphs( "One.\r\n\r\n&nbsp;\r\n\r\nTwo." ) );
  }

  public function test_all_blank_value_becomes_empty_string(): void {
    $this->assertSame( '', nm_strip_empty_paragraphs( "&nbsp;\n\n<p></p>\n" ) );
  }

  public function test_empty_string(): void {
    $this->assertSame( '', nm_strip_empty_paragraphs( '' ) );
  }

  public function test_invalid_utf8_is_returned_unchanged(): void {
    $value = "One.\xC3\n\n&nbsp;";
    $this->assertSame( $value, nm_strip_empty_paragraphs( $value ) );
  }

  public function test_preformatted_and_raw_text_regions_are_untouched(): void {
    $pre      = "<pre>\na\n   \n   \n   \nb\n&nbsp;\n<p></p>\n</pre>";
    $script   = "<script>\nvar b = '<p></p>';\n\n\n\nvar c = 1;\n</script>";
    $style    = "<STYLE>\n.a{}\n\n\n\n.b{}\n</STYLE>";
    $textarea = "<textarea name=\\\"x\\\">\n&nbsp;\n</textarea>";

    $this->assertSame(
      $pre . "\n\n" . $script . "\n\nText.\n\n" . $style . "\n\n" . $textarea,
      nm_strip_empty_paragraphs( "&nbsp;\n\n" . $pre . "\n\n&nbsp;\n\n" . $script . "\n\nText.\n\n<p></p>\n\n" . $style . "\n\n" . $textarea . "\n\n&nbsp;" )
    );
  }

  public function test_empty_paragraph_blocks_are_removed(): void {
    $one   = "<!-- wp:paragraph -->\n<p>One.</p>\n<!-- /wp:paragraph -->";
    $two   = "<!-- wp:paragraph {\"align\":\"left\"} -->\n<p class=\"has-text-align-left\">Two.</p>\n<!-- /wp:paragraph -->";
    $empty = "<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph -->";
    $nbsp  = "<!-- wp:paragraph -->\n<p>&nbsp;</p>\n<!-- /wp:paragraph -->";

    $this->assertSame(
      $one . "\n\n" . $two,
      nm_strip_empty_paragraphs( $one . "\n\n" . $empty . "\n\n" . $two . "\n\n" . $nbsp )
    );
  }

  public function test_other_blocks_are_untouched(): void {
    $value = "<!-- wp:group -->\n<div class=\"wp-block-group\"><!-- wp:html -->\n<p></p>\n<!-- /wp:html --></div>\n<!-- /wp:group -->\n\n"
      . "<!-- wp:spacer -->\n<div style=\"height:100px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer -->\n\n"
      . "<!-- wp:html -->\n&nbsp;\n\n&nbsp;\n<!-- /wp:html -->";

    $this->assertSame( $value, nm_strip_empty_paragraphs( $value ) );
  }

  #[DataProvider( 'classic_inputs' )]
  public function test_is_idempotent( string $value ): void {
    $once = nm_strip_empty_paragraphs( $value );
    $this->assertSame( $once, nm_strip_empty_paragraphs( $once ) );
  }

  public static function classic_inputs(): array {
    return array(
      'trailing nbsp' => array( "One.\n\n&nbsp;\n\n&nbsp;" ),
      'mid nbsp'      => array( "One.\n\n&nbsp;\n\nTwo." ),
      'empty p'       => array( "<p>One.</p>\n<p>&nbsp;</p>\n<p></p>\n<p>Two.</p>\n<p><br></p>" ),
      'trailing br'   => array( "<p>One.<br></p>\n\nTwo.<br />" ),
      'soft br'       => array( "Line one<br>\nLine two" ),
      'entities'      => array( "One.\n\n&#160;\n\n<P>&#xA0;</P>\n\n\u{00A0}<BR/>" ),
      'crlf'          => array( "One.\r\n\r\n&nbsp;\r\n\r\nTwo." ),
      'table'         => array( '<table><tr><td>&nbsp;</td></tr></table>' ),
      'pre'           => array( "&nbsp;\n\n<pre>a\n\n\n\nb</pre>\n\n&nbsp;" ),
    );
  }
}
