<?php
/**
 * If I Speak follow links: direct links to the show on Spotify, Apple Podcasts and our
 * own feed. Shared by the category archive hero and the front-page product block.
 *
 * Replaces the single "Subscribe to the podcast" button, which pointed at Podfollow
 * via the category's _nm_podcast_url. The IDs are hardcoded here, as on the Committed,
 * Foreign Agent and Death in Westminster archives, until issue #597 moves platform IDs
 * into category term meta. Apple 1731863488 lists the feed below as the show's feed.
 *
 * @param array $args {
 *   @type string $classes Extra classes for the wrapper (spacing).
 *   @type string $testid  data-testid for the wrapper.
 * }
 */

$args = wp_parse_args(
  $args,
  array(
    'classes' => '',
    'testid'  => 'if-i-speak-follow-links',
  )
);

$follow_links = array(
  'Spotify'        => 'https://open.spotify.com/show/45yPBsgN4aNKDYX05uiCnF',
  'Apple Podcasts' => 'https://podcasts.apple.com/podcast/id1731863488',
  'RSS'            => 'https://feeds.novaramedia.com/podcasts/if-i-speak',
);
?>
<nav class="<?php echo esc_attr( trim( 'podcast-follow-links ' . $args['classes'] ) ); ?>" aria-label="Subscribe to If I Speak" data-testid="<?php echo esc_attr( $args['testid'] ); ?>">
  <ul class="podcast-follow-links__list">
    <?php foreach ( $follow_links as $label => $url ) { ?>
    <li><a class="ui-button ui-button--red ui-button--auto-height" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $label ); ?></a></li>
    <?php } ?>
  </ul>
</nav>
