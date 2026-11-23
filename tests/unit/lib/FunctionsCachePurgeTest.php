<?php
/**
 * Tests for lib/functions-cache-purge.php.
 *
 * The purge functions run at most once per request (static guards), so each
 * test runs in its own process to start from a clean request.
 */

namespace Cloudflare\APO\WordPress {
  if ( ! class_exists( Hooks::class ) ) {
    /**
     * Stand-in for the Cloudflare plugin's Hooks class.
     */
    class Hooks {
      public int $everything = 0;

      public function purgeCacheEverything(): void {
        ++$this->everything;
      }

      public function purgeCacheByRelevantURLs( $post_ids ): void {}
    }
  }
}

namespace NM\Tests\Lib {

  use Brain\Monkey\Functions;
  use Cloudflare\APO\WordPress\Hooks;
  use NM\Tests\TestCase;
  use PHPUnit\Framework\Attributes\PreserveGlobalState;
  use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

  /**
   * Stand-in for the Kinsta mu-plugin's cache purge object.
   */
  final class FakeKinstaCachePurge {
    public int $complete = 0;

    public function purge_complete_caches(): void {
      ++$this->complete;
    }
  }

  #[RunTestsInSeparateProcesses]
  #[PreserveGlobalState( false )]
  final class FunctionsCachePurgeTest extends TestCase {

    /** @var array Options written during the test, by name. */
    private array $options = array();

    protected function setUp(): void {
      parent::setUp();
      self::requireThemeFile( 'lib/functions-cache-purge.php' );

      $GLOBALS['kinsta_muplugin'] = null;
      $GLOBALS['wp_filter']       = array();
      $this->options              = array();

      Functions\when( 'get_current_user_id' )->justReturn( 7 );
      Functions\when( 'get_option' )->alias( fn( $name, $default = false ) => $this->options[ $name ] ?? $default );
      Functions\when( 'update_option' )->alias(
        function ( $name, $value ) {
          $this->options[ $name ] = $value;
          return true;
        }
      );
    }

    private function install_kinsta(): FakeKinstaCachePurge {
      $purge = new FakeKinstaCachePurge();
      $GLOBALS['kinsta_muplugin'] = (object) array( 'kinsta_cache_purge' => $purge );
      return $purge;
    }

    private function install_cloudflare(): Hooks {
      $hooks = new Hooks();
      $GLOBALS['wp_filter']['deleted_post'] = (object) array(
        'callbacks' => array(
          PHP_INT_MAX => array(
            'cf' => array( 'function' => array( $hooks, 'purgeCacheByRelevantURLs' ) ),
          ),
        ),
      );
      return $hooks;
    }

    public function test_options_save_purges_kinsta_and_cloudflare_and_logs_both(): void {
      $kinsta     = $this->install_kinsta();
      $cloudflare = $this->install_cloudflare();
      Functions\expect( 'wp_cache_flush' )->never();

      nm_purge_caches_on_options_save( 'nm_front_page_above_the_fold_featured_options' );

      $this->assertSame( 1, $kinsta->complete );
      $this->assertSame( 1, $cloudflare->everything );

      $log = $this->options['nm_cache_purge_log'];
      $this->assertSame( array( 'kinsta', 'cloudflare' ), array_column( $log, 'layer' ) );
      $this->assertSame( 'options:nm_front_page_above_the_fold_featured_options', $log[0]['trigger'] );
      $this->assertSame( 7, $log[0]['user'] );
    }

    public function test_purge_runs_once_per_request(): void {
      $kinsta     = $this->install_kinsta();
      $cloudflare = $this->install_cloudflare();

      nm_purge_caches_on_options_save( 'nm_front_page_options' );
      nm_purge_caches_on_options_save( 'nm_fundraising_options' );

      $this->assertSame( 1, $kinsta->complete );
      $this->assertSame( 1, $cloudflare->everything );
      $this->assertCount( 2, $this->options['nm_cache_purge_log'] );
    }

    public function test_without_kinsta_or_cloudflare_falls_back_to_object_cache_flush(): void {
      Functions\expect( 'wp_cache_flush' )->once();

      $this->assertTrue( nm_purge_all_page_caches( 'options:nm_front_page_options' ) );
      $this->assertArrayNotHasKey( 'nm_cache_purge_log', $this->options );
    }

    public function test_menu_or_term_change_purges_cloudflare_only(): void {
      $kinsta     = $this->install_kinsta();
      $cloudflare = $this->install_cloudflare();
      Functions\when( 'current_action' )->justReturn( 'edited_term' );

      nm_purge_cloudflare_on_menu_or_term_change( 12 );

      $this->assertSame( 0, $kinsta->complete, 'Kinsta purges menus and terms itself.' );
      $this->assertSame( 1, $cloudflare->everything );
      $this->assertSame( 'edited_term:12', $this->options['nm_cache_purge_log'][0]['trigger'] );
    }

    public function test_cloudflare_is_not_purged_twice_after_a_full_purge(): void {
      $this->install_kinsta();
      $cloudflare = $this->install_cloudflare();
      Functions\when( 'current_action' )->justReturn( 'edited_term' );

      nm_purge_caches_on_options_save( 'nm_front_page_options' );
      nm_purge_cloudflare_on_menu_or_term_change( 12 );

      $this->assertSame( 1, $cloudflare->everything );
    }

    public function test_cloudflare_hooks_ignores_unrelated_deleted_post_callbacks(): void {
      $GLOBALS['wp_filter']['deleted_post'] = (object) array(
        'callbacks' => array(
          10 => array(
            'x' => array( 'function' => 'some_other_callback' ),
          ),
        ),
      );

      $this->assertNull( nm_get_cloudflare_hooks() );
    }

    public function test_log_keeps_only_the_newest_entries(): void {
      $this->options['nm_cache_purge_log'] = array_fill( 0, NM_CACHE_PURGE_LOG_LIMIT, array( 'trigger' => 'old' ) );

      nm_log_cache_purge( 'kinsta', 'new' );

      $log = $this->options['nm_cache_purge_log'];
      $this->assertCount( NM_CACHE_PURGE_LOG_LIMIT, $log );
      $this->assertSame( 'new', end( $log )['trigger'] );
    }

    private function contributor( string $title, string $slug, string $status = 'publish', string $type = 'contributor' ): \stdClass {
      return (object) array(
        'post_type'   => $type,
        'post_status' => $status,
        'post_title'  => $title,
        'post_name'   => $slug,
      );
    }

    public function test_contributor_rename_purges_all_page_caches(): void {
      $kinsta = $this->install_kinsta();

      nm_purge_caches_on_contributor_rename( 5, $this->contributor( 'Ash Sarkar', 'ash-sarkar' ), $this->contributor( 'Ash Sakar', 'ash-sarkar' ) );

      $this->assertSame( 1, $kinsta->complete );
      $this->assertSame( 'contributor_rename:5', $this->options['nm_cache_purge_log'][0]['trigger'] );
    }

    public function test_contributor_slug_change_purges_all_page_caches(): void {
      $kinsta = $this->install_kinsta();

      nm_purge_caches_on_contributor_rename( 5, $this->contributor( 'Ash Sarkar', 'ash-sarkar' ), $this->contributor( 'Ash Sarkar', 'ash-sakar' ) );

      $this->assertSame( 1, $kinsta->complete );
    }

    public function test_contributor_edit_without_rename_does_not_purge(): void {
      $kinsta = $this->install_kinsta();

      nm_purge_caches_on_contributor_rename( 5, $this->contributor( 'Ash Sarkar', 'ash-sarkar' ), $this->contributor( 'Ash Sarkar', 'ash-sarkar' ) );

      $this->assertSame( 0, $kinsta->complete );
    }

    public function test_rename_of_unpublished_contributor_or_other_post_type_does_not_purge(): void {
      $kinsta = $this->install_kinsta();

      nm_purge_caches_on_contributor_rename( 5, $this->contributor( 'New', 'new', 'draft' ), $this->contributor( 'Old', 'old', 'draft' ) );
      nm_purge_caches_on_contributor_rename( 6, $this->contributor( 'New', 'new', 'publish', 'post' ), $this->contributor( 'Old', 'old', 'publish', 'post' ) );

      $this->assertSame( 0, $kinsta->complete );
    }

    public function test_mailchimp_key_change_purges_all_page_caches(): void {
      $kinsta = $this->install_kinsta();

      nm_purge_caches_on_mailchimp_key_change( 99, 42, '_nm_mailchimp_key' );

      $this->assertSame( 1, $kinsta->complete );
      $this->assertSame( 'mailchimp_key:42', $this->options['nm_cache_purge_log'][0]['trigger'] );
    }

    public function test_other_meta_changes_do_not_purge(): void {
      $kinsta = $this->install_kinsta();

      nm_purge_caches_on_mailchimp_key_change( 99, 42, '_nm_banner_headline' );

      $this->assertSame( 0, $kinsta->complete );
    }
  }
}
