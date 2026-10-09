<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$category = get_category( get_query_var( 'cat' ) );

$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );
$youtube_copy_override = get_term_meta( $category->term_id, '_nm_youtube_text', true );

$podcast_url_meta = get_term_meta( $category->term_id, '_nm_podcast_url', true );
$podcast_url = ! empty( $podcast_url_meta ) ? $podcast_url_meta : false;

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Listen to the podcast';
$youtube_copy = ! empty( $youtube_copy_override ) ? $youtube_copy_override : 'Watch on YouTube';

$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/dyor/';

// Every published episode once, newest first, grouped by season below.
$dyor_posts = get_posts( array(
  'cat'            => $category->term_id,
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'date',
  'order'          => 'DESC',
  'no_found_rows'  => true,
) );

$dyor_seasons = nm_get_dyor_seasons();

// Season number => posts. 0 collects posts with no season set.
$posts_by_season = array();
foreach ( $dyor_posts as $dyor_post ) {
  $posts_by_season[ (int) get_post_meta( $dyor_post->ID, '_nm_season', true ) ][] = $dyor_post;
}

$unseasoned_posts = isset( $posts_by_season[0] ) ? $posts_by_season[0] : array();
unset( $posts_by_season[0] );
krsort( $posts_by_season );

/**
 * Figma embed URL for a season's board, opened on the newest episode in the
 * season that has its own node ID, else the season default, else no node.
 */
$dyor_map_src = function ( $season, $season_posts ) {
  $node_id = '';

  foreach ( $season_posts as $season_post ) {
    $node_id = (string) get_post_meta( $season_post->ID, '_nm_dyor_figma_node_id', true );

    if ( '' !== $node_id ) {
      break;
    }
  }

  if ( '' === $node_id ) {
    $node_id = $season['figma_default_node_id'];
  }

  $embed_params = array(
    'embed-host'    => 'share',
    'footer'        => 'false',
    'page-selector' => 'false',
  );

  if ( '' !== $node_id ) {
    $embed_params['node-id'] = $node_id;
  }

  return 'https://embed.figma.com/board/' . rawurlencode( $season['figma_file_key'] ) . '/Do-Your-Own-Research-Map?' . http_build_query( $embed_params );
};

$latest_post = ! empty( $dyor_posts ) ? $dyor_posts[0] : null;

get_header();
?>

<main id="main-content" class="dyor-archive" data-testid="main-content">

  <?php // ── Section 1: Hero ── ?>
  <section class="container mt-4 mb-4">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <div class="grid-row dyor-archive__hero-background ui-rounded-box ui-rounded-box--top">
          <div class="grid-item is-xxl-24">
            <div class="dyor-archive__hero">
              <picture>
                <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.avif' ); ?>" type="image/avif">
                <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.webp' ); ?>" type="image/webp">
                <img class="dyor-archive__hero-image" src="<?php echo esc_url( $base_image_path . 'dyor-hero.png' ); ?>" alt="Do Your Own Research" />
              </picture>
            </div>
          </div>
        </div>
        <div class="grid-row background-white ui-rounded-box ui-rounded-box--bottom">
          <div class="grid-item is-xxl-24 mt-5 mt-s-4 mb-5 mb-s-4">
            <div class="dyor-archive__intro">
              <div class="dyor-archive__intro-text font-size-13 font-size-s-12 mb-4 text-align-center">
                <?php echo category_description(); ?>
              </div>
              <div class="dyor-archive__intro-buttons">
                <?php if ( $podcast_url ) { ?>
                <a class="ui-button ui-button--black" href="<?php echo esc_url( $podcast_url ); ?>" target="_blank" rel="nofollow noopener"><?php echo esc_html( $podcast_copy ); ?></a>
                <?php } ?>
                <a class="ui-button ui-button--red" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow noopener"><?php echo esc_html( $youtube_copy ); ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php // ── Latest Episode ── ?>
  <?php
  if ( $latest_post ) {
    // ID-based calls throughout, so the global $post is never swapped.
    $latest_id          = $latest_post->ID;
    $latest_meta        = get_post_meta( $latest_id );
    $latest_description = ! empty( $latest_meta['_cmb_short_desc'][0] ) ? $latest_meta['_cmb_short_desc'][0] : get_the_excerpt( $latest_post );
    $latest_label       = nm_get_season_episode_label( $latest_id );
    ?>
  <section class="container">
    <div class="dyor-archive__latest-episode grid-row">
      <div class="dyor-archive__latest-episode-image grid-item is-xxl-16 is-s-24 mb-s-4">
        <div class="ui-embed-container ui-rounded-box">
          <?php if ( ! empty( $latest_meta['_cmb_utube'][0] ) ) { ?>
            <?php echo render_youtube_embed_iframe( $latest_meta['_cmb_utube'][0], false, 'eager', get_the_title( $latest_id ) ); ?>
          <?php } else { ?>
            <?php render_thumbnail( $latest_id, 'col16-16to9', array( 'class' => 'ui-rounded-box' ) ); ?>
          <?php } ?>
        </div>
      </div>
      <div class="dyor-archive__latest-episode-text grid-item is-xxl-8 is-s-24">
        <h4 class="ui-boxed-title mb-3">The Latest Episode</h4>
        <?php if ( $latest_label !== '' ) { ?>
        <h4 class="font-size-9 font-weight-bold mb-2" data-testid="latest-episode-label"><?php echo esc_html( $latest_label ); ?></h4>
        <?php } ?>
        <h2 class="font-size-14 font-size-s-13 font-weight-bold text-wrap-pretty">
          <?php echo esc_html( get_the_title( $latest_id ) ); ?>
        </h2>
        <h3 class="font-size-12 font-size-s-11 font-weight-bold mt-3 mt-s-2 text-wrap-pretty">
          <?php render_standfirst( $latest_id ); ?>
        </h3>
        <div class="font-size-10 mt-3 mt-s-2 text-wrap-pretty">
          <?php echo wp_kses_post( $latest_description ); ?>
        </div>
      </div>
    </div>
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mt-5 mb-5">
        <hr />
      </div>
    </div>
  </section>
    <?php
  }
  ?>

  <?php // ── Section 5: Newsletter Signup ──  But just via partial, and only once it actually exists ?>

  <?php
  // ── Seasons, newest first ──
  // Maps load on click (ClickToLoad.js). Before loading it fires a cancelable
  // nm:click-to-load event on the container — the hook for the cookie
  // consent gate when it lands.
  $season_index = 0;
  // The newest season's map also answers #map, which older front-page DYOR
  // blocks (show-blocks/dyor.php, dyor-alt.php) still link to.
  $map_anchor_placed = false;

  foreach ( $posts_by_season as $season_number => $season_posts ) {
    // A support box divides consecutive seasons. It shows the site-wide
    // support copy for now; #633 passes DYOR's own copy from Products here.
    if ( $season_index++ > 0 ) {
      get_template_part(
        'partials/support-section',
        null,
        array(
          'container_classes'     => 'mb-6 dyor-archive__season-support',
          'on_colored_background' => false,
        )
      );
    }

    $season = isset( $dyor_seasons[ $season_number ] ) ? $dyor_seasons[ $season_number ] : array(
      'number'                => $season_number,
      'title'                 => '',
      'description'           => '',
      'figma_file_key'        => '',
      'figma_default_node_id' => '',
    );

    $season_title = '' !== $season['title'] ? $season['title'] : 'Season ' . $season_number;
    ?>
  <section class="container mb-5 dyor-archive__season" id="<?php echo esc_attr( 'season-' . $season_number ); ?>" data-testid="dyor-season" data-season="<?php echo esc_attr( $season_number ); ?>">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24 text-align-center">
        <h4 class="ui-boxed-title"><?php echo esc_html( $season_title ); ?></h4>
      </div>
      <?php if ( '' !== $season['description'] ) { ?>
      <?php // Centred on the grid but left-aligned, to keep the line length readable. ?>
      <div class="grid-item offset-xxl-5 is-xxl-14 offset-l-3 is-l-18 offset-s-0 is-s-24 mt-4">
        <div class="dyor-archive__season-description font-size-12 font-size-s-11 text-paragraph-breaks">
          <?php echo wp_kses_post( wpautop( $season['description'] ) ); ?>
        </div>
      </div>
      <?php } ?>
    </div>

    <?php if ( '' !== $season['figma_file_key'] ) { ?>
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <div class="grid-row background-white ui-rounded-box p-4">
          <div
            <?php
            if ( ! $map_anchor_placed ) {
              $map_anchor_placed = true;
              echo 'id="map"';
            }
            ?>
            class="dyor-archive__map dyor-archive__map--season-<?php echo esc_attr( $season_number ); ?> ui-rounded-box"
            data-testid="dyor-season-map"
            data-click-to-load
            data-click-to-load-src="<?php echo esc_url( $dyor_map_src( $season, $season_posts ) ); ?>"
            data-click-to-load-title="<?php echo esc_attr( 'Do Your Own Research – ' . $season_title . ' map' ); ?>"
          >
            <button type="button" class="ui-button ui-button--black dyor-archive__map-button" data-click-to-load-button>
              <?php echo esc_html( 'Load the ' . $season_title . ' map' ); ?>
            </button>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>

    <div class="grid-row">
      <?php
      foreach ( $season_posts as $season_post ) {
        get_template_part(
          'partials/post-layouts/archive-post-dyor',
          null,
          array(
            'post-id'           => $season_post->ID,
            'grid-item-classes' => 'grid-item is-xxl-8 is-l-12 is-s-24 mb-4',
          )
        );
      }
      ?>
    </div>
  </section>
    <?php
  }
  ?>

  <?php if ( ! empty( $unseasoned_posts ) ) { ?>
  <section class="container mb-5" data-testid="dyor-unseasoned">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 text-align-center mb-4">
        <h4 class="ui-boxed-title">More from Do Your Own Research</h4>
      </div>
      <?php
      foreach ( $unseasoned_posts as $unseasoned_post ) {
        get_template_part(
          'partials/post-layouts/archive-post-dyor',
          null,
          array(
            'post-id'           => $unseasoned_post->ID,
            'grid-item-classes' => 'grid-item is-xxl-8 is-l-12 is-s-24 mb-4',
          )
        );
      }
      ?>
    </div>
  </section>
  <?php } ?>

  <?php // ── Section 7: Support Module ── ?>
  <?php
  get_template_part(
    'partials/support-section',
    null,
    array(
      'container_classes'     => 'mt-4 mb-4',
      'on_colored_background' => false,
    )
  );
  ?>

</main>
<?php
get_footer();
?>
