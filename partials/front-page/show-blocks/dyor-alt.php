<?php
/**
 * ALT layout of the Do Your Own Research front-page show block.
 *
 * Built from Figma node 2184-4815 for side-by-side comparison against the
 * current `dyor.php`. Both render the featured episode as a YouTube embed
 * (falling back to the thumbnail when no video is set) and pull from the same
 * posts; only the layout differs.
 *
 * Differences vs current: full-width decorative banner hero on top (widescreen
 * overlay on desktop, the taller standard overlay on mobile); intro copy + dual
 * CTA row (Subscribe / Explore map); a large featured episode with its title
 * beside the embed on XXL and below it on smaller screens; and the remaining
 * episodes as a vertical sidebar list instead of the current 2×2 grid.
 */

$dyor_category = get_term_by( 'slug', 'do-your-own-research', 'category' );

if ( ! $dyor_category ) {
  return;
}

$category_link   = get_category_link( $dyor_category->term_id );
$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/dyor/';
$figma_file_key  = get_term_meta( $dyor_category->term_id, '_nm_dyor_figma_file_key', true );
$podcast_url     = get_term_meta( $dyor_category->term_id, '_nm_podcast_url', true );

$dyor_posts = get_posts(
  array(
    'posts_per_page' => 6,
    'category'       => $dyor_category->term_id,
  )
);

if ( empty( $dyor_posts ) ) {
  return;
}

$featured         = $dyor_posts[0];
$featured_youtube = get_post_meta( $featured->ID, '_cmb_utube', true );
$recent           = array_slice( $dyor_posts, 1 );
?>
<section class="container mt-4 mb-4">
  <div class="grid-item is-xxl-24">
    <div class="front-page-dyor-alt ui-rounded-box background-white layout-overflow-hidden">

      <?php // ── Hero banner: clouds background (reuses .front-page-dyor → dyor-background) + transparent head/text overlay. Widescreen overlay on desktop; the taller standard dyor-hero overlay on mobile (the widescreen one is too skinny at narrow widths). ── ?>
      <div class="front-page-dyor-alt__banner front-page-dyor layout-overflow-hidden u-position-relative">
        <picture>
          <source media="(max-width: 759px)" srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.avif' ); ?>" type="image/avif">
          <source media="(max-width: 759px)" srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.webp' ); ?>" type="image/webp">
          <source media="(max-width: 759px)" srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.png' ); ?>" type="image/png">
          <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero-wide.avif' ); ?>" type="image/avif">
          <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero-wide.webp' ); ?>" type="image/webp">
          <img class="front-page-dyor-alt__banner-overlay u-display-block" src="<?php echo esc_url( $base_image_path . 'dyor-hero-wide.png' ); ?>" alt="Do Your Own Research" />
        </picture>
      </div>

      <div class="pt-4 pb-4">

        <?php // ── Intro copy + dual CTA buttons ── ?>
        <div class="grid-row grid--nested mb-4">
          <div class="grid-item is-xxl-16 is-s-24 mb-s-3">
            <?php if ( $dyor_category->description ) { ?>
            <p class="font-size-12 font-size-s-11 text-wrap-pretty mb-0">
              <span class="font-weight-bold">Do Your Own Research</span> <?php echo esc_html( wp_strip_all_tags( preg_replace( '/^Do Your Own Research\s*/i', '', $dyor_category->description ) ) ); ?>
            </p>
            <?php } ?>
          </div>
          <div class="grid-item is-xxl-8 is-s-24">
            <div class="front-page-dyor-alt__cta">
              <a class="ui-button ui-button--small ui-button--red" href="<?php echo esc_url( ! empty( $podcast_url ) ? $podcast_url : $category_link ); ?>"<?php echo ! empty( $podcast_url ) ? ' target="_blank" rel="nofollow noopener noreferrer"' : ''; ?>>Subscribe to the podcast</a>
              <?php if ( ! empty( $figma_file_key ) ) { ?>
              <a class="ui-button ui-button--small ui-button--black" href="<?php echo esc_url( $category_link . '#map' ); ?>">Explore the map</a>
              <?php } ?>
            </div>
          </div>
        </div>

        <?php // ── Featured episode (large, horizontal) + recent list sidebar ── ?>
        <div class="grid-row grid--nested">

          <div class="grid-item is-xxl-16 is-s-24 mb-s-4">
            <div class="grid-row grid--nested">
              <div class="grid-item is-xxl-15 is-xl-24 mb-xl-3">
                <?php if ( ! empty( $featured_youtube ) ) { ?>
                <div class="u-video-embed-container ui-rounded-box">
                  <?php echo render_youtube_embed_iframe( $featured_youtube, false, 'lazy', get_the_title( $featured->ID ) ); ?>
                </div>
                <?php } else { ?>
                <a href="<?php echo esc_url( get_the_permalink( $featured->ID ) ); ?>" class="ui-hover u-display-block">
                  <?php render_thumbnail( $featured->ID, 'col12-16to9', array( 'class' => 'ui-rounded-box u-display-block' ) ); ?>
                </a>
                <?php } ?>
              </div>
              <div class="grid-item is-xxl-9 is-xl-24">
                <a href="<?php echo esc_url( get_the_permalink( $featured->ID ) ); ?>" class="ui-hover u-display-block">
                  <h3 class="font-size-14 font-size-xl-13 font-size-s-12 font-weight-bold text-wrap-pretty mb-1"><?php echo esc_html( get_the_title( $featured->ID ) ); ?></h3>
                  <div class="font-size-11 font-weight-bold text-wrap-pretty"><?php render_standfirst( $featured->ID ); ?></div>
                </a>
              </div>
            </div>
          </div>

          <?php if ( ! empty( $recent ) ) { ?>
          <div class="grid-item is-xxl-8 is-s-24">
            <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover u-display-block mb-3">
              <div class="layout-split-level font-size-8 font-weight-bold">
                <h4 class="font-weight-bold text-uppercase mb-0">Recent Episodes</h4>
                <span>See All</span>
              </div>
            </a>
            <?php foreach ( $recent as $recent_post ) { ?>
            <a href="<?php echo esc_url( get_the_permalink( $recent_post->ID ) ); ?>" class="grid-row grid--nested-tight ui-hover mb-3">
              <div class="grid-item is-xxl-8 is-xl-10 is-s-6">
                <?php render_thumbnail( $recent_post->ID, 'col4-16to9', array( 'class' => 'ui-rounded-box u-display-block' ) ); ?>
              </div>
              <div class="grid-item is-xxl-16 is-xl-14 is-s-18">
                <h5 class="font-size-10 font-weight-bold text-wrap-pretty mb-0"><?php echo esc_html( get_the_title( $recent_post->ID ) ); ?></h5>
              </div>
            </a>
            <?php } ?>
          </div>
          <?php } ?>

        </div>

      </div>
    </div>
  </div>
</section>
