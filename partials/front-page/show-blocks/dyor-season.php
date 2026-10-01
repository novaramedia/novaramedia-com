<?php
/**
 * Current-season layout of the Do Your Own Research front-page show block.
 *
 * Same structure as `dyor.php`, but scoped to the season of the newest
 * episode so a new season never shares the block with the tail of the last
 * one: the season intro and a map button on the left, the newest episode on
 * the right, and a row of four more episodes from the same season once it has
 * five. The block's job is to drive readers to the archive, so every link
 * goes there rather than to single posts.
 *
 * Falls back to `dyor.php` when the newest episode has no season set.
 */

$dyor_category = get_term_by( 'slug', 'do-your-own-research', 'category' );

if ( ! $dyor_category ) {
  return;
}

$latest_posts = get_posts(
  array(
    'posts_per_page' => 1,
    'category'       => $dyor_category->term_id,
  )
);

if ( empty( $latest_posts ) ) {
  return;
}

$season_number = (int) get_post_meta( $latest_posts[0]->ID, '_nm_season', true );

if ( $season_number < 1 ) {
  get_template_part( 'partials/front-page/show-blocks/dyor' );
  return;
}

$dyor_seasons    = nm_get_dyor_seasons();
$season       = isset( $dyor_seasons[ $season_number ] ) ? $dyor_seasons[ $season_number ] : array();
$season_title = ! empty( $season['title'] ) ? $season['title'] : 'Season ' . $season_number;

// First paragraph only: the archive shows the full season description. The
// field is WYSIWYG, so it may hold blank-line paragraphs or <p> markup;
// wpautop() normalises both to <p> before the first one is taken.
$season_intro = '';
if ( ! empty( $season['description'] ) ) {
  $season_intro_html = wpautop( $season['description'] );
  $season_intro      = preg_match( '#<p[^>]*>(.*?)</p>#s', $season_intro_html, $season_intro_match ) ? $season_intro_match[1] : $season_intro_html;
  $season_intro      = trim( wp_strip_all_tags( $season_intro ) );
}

$category_link   = get_category_link( $dyor_category->term_id );
$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/dyor/';

$season_posts = get_posts(
  array(
    'posts_per_page' => 5,
    'category'       => $dyor_category->term_id,
    'meta_key'       => '_nm_season', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one category, five posts.
    'meta_value'     => $season_number, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
  )
);

$featured         = $season_posts[0];
$featured_youtube = get_post_meta( $featured->ID, '_cmb_utube', true );
$featured_label   = nm_get_season_episode_label( $featured->ID );
// The row needs four more episodes to fill it; until then, the newest only.
$recent = count( $season_posts ) === 5 ? array_slice( $season_posts, 1 ) : array();

// Without the row below, the top row's bottom margin would stack on the box
// padding. Keep the gap between the two columns once they stack on mobile.
$top_left_spacing  = empty( $recent ) ? 'mb-0 mb-s-4' : 'mb-4';
$top_right_spacing = empty( $recent ) ? 'mb-0' : 'mb-4';
?>
<section class="container mt-4 mb-4" data-testid="front-page-dyor-season">
  <div class="grid-item is-xxl-24">
    <div class="grid-row front-page-dyor front-page-dyor--season-<?php echo esc_attr( $season_number ); ?> background-cover-image ui-rounded-box ui-backgrounded-box-padding">

      <?php // Top: 50:50 — hero, season intro and map button left; newest episode right. Bottom-aligned so white boxes share a baseline. ?>

      <div class="grid-item is-xxl-24">
        <div class="grid-row grid-row--nested front-page-dyor__top-row">

          <div class="grid-item is-s-24 is-xxl-12 <?php echo esc_attr( $top_left_spacing ); ?>">
            <div class="dyor-archive__hero">
              <picture>
                <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.avif' ); ?>" type="image/avif">
                <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.webp' ); ?>" type="image/webp">
                <img class="dyor-archive__hero-image" src="<?php echo esc_url( $base_image_path . 'dyor-hero.png' ); ?>" alt="Do Your Own Research" />
              </picture>
            </div>
            <?php if ( $season_intro !== '' || ! empty( $season['figma_file_key'] ) ) { ?>
            <div class="background-white ui-rounded-box pt-3 pb-3 pl-4 pr-4">
              <?php if ( $season_intro !== '' ) { ?>
              <p class="font-size-12 font-size-m-11 font-size-s-12 text-wrap-pretty mb-2"><?php echo esc_html( wp_strip_all_tags( $season_intro ) ); ?></p>
              <?php } ?>
              <?php if ( ! empty( $season['figma_file_key'] ) ) { ?>
              <a href="<?php echo esc_url( $category_link . '#season-' . $season_number ); ?>" class="ui-button ui-button--black"><?php echo esc_html( 'Explore the Season ' . $season_number . ' map' ); ?></a>
              <?php } ?>
            </div>
            <?php } ?>
          </div>

          <div class="grid-item is-s-24 is-xxl-12 <?php echo esc_attr( $top_right_spacing ); ?>">
            <div class="background-white ui-rounded-box pt-3 pb-3 pl-4 pr-4">
              <?php if ( ! empty( $featured_youtube ) ) { ?>
              <div class="ui-embed-container ui-rounded-box mb-3">
                <?php echo render_youtube_embed_iframe( $featured_youtube, false, 'lazy', get_the_title( $featured->ID ) ); ?>
              </div>
              <?php } else { ?>
              <div class="mb-3">
                <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">
                  <?php render_thumbnail( $featured->ID, 'col12-16to9', array( 'class' => 'ui-rounded-box' ) ); ?>
                </a>
              </div>
              <?php } ?>
              <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">
                <?php if ( $featured_label !== '' ) { ?>
                <h4 class="font-size-8 font-weight-bold mb-1" data-testid="episode-label"><?php echo esc_html( $featured_label ); ?></h4>
                <?php } ?>
                <h3 class="font-size-13 font-weight-bold text-wrap-pretty mb-1"><?php echo esc_html( get_the_title( $featured->ID ) ); ?></h3>
                <div class="font-size-11 mb-0"><?php render_standfirst( $featured->ID ); ?></div>
              </a>
            </div>
          </div>

        </div>
      </div>

      <?php // Bottom: four more episodes of this season, once it has five. ?>

      <?php if ( ! empty( $recent ) ) { ?>
      <div class="grid-item is-xxl-24">
        <div class="background-white ui-rounded-box pt-3 pb-3 pl-4 pr-4">
          <div class="grid-row grid-row--nested">
            <div class="grid-item is-xxl-24">
              <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">
                <div class="layout-split-level font-size-8 font-weight-bold">
                  <h4 class="font-weight-bold"><?php echo esc_html( 'More from ' . $season_title ); ?></h4>
                  <span>See All</span>
                </div>
              </a>
            </div>
            <?php foreach ( $recent as $recent_post ) { ?>
            <div class="grid-item is-s-12 is-xxl-6 mt-3">
              <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">
                <div class="layout-thumbnail-frame mb-2">
                  <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                    <?php render_post_ui_tags( $recent_post->ID, false, true, 'no-border' ); ?>
                  </div>
                  <?php render_thumbnail( $recent_post->ID, 'col12-16to9', array( 'class' => 'ui-rounded-box' ) ); ?>
                </div>
                <h4 class="font-size-11 font-size-l-10 font-size-s-11 font-weight-bold text-wrap-pretty"><?php echo esc_html( get_the_title( $recent_post->ID ) ); ?></h4>
              </a>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php } ?>

    </div>
  </div>
</section>
