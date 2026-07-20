<?php
$dyor_category = get_term_by( 'slug', 'do-your-own-research', 'category' );

if ( ! $dyor_category ) {
  return;
}

$category_link   = get_category_link( $dyor_category->term_id );
$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/dyor/';
$figma_file_key  = get_term_meta( $dyor_category->term_id, '_nm_dyor_figma_file_key', true );

$dyor_posts = get_posts(
  array(
    'posts_per_page' => 5,
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
    <div class="grid-row front-page-dyor background-cover-image ui-rounded-box ui-backgrounded-box-padding">

      <?php // Top: 50:50 — hero/description/CTA left, embed+title right. Bottom-aligned so white boxes share a baseline. ?>

      <div class="grid-item is-xxl-24">
        <div class="grid-row grid-row--nested front-page-dyor__top-row">

          <div class="grid-item is-s-24 is-xxl-12 mb-4">
            <div class="dyor-archive__hero">
              <picture>
                <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.avif' ); ?>" type="image/avif">
                <source srcset="<?php echo esc_url( $base_image_path . 'dyor-hero.webp' ); ?>" type="image/webp">
                <img class="dyor-archive__hero-image" src="<?php echo esc_url( $base_image_path . 'dyor-hero.png' ); ?>" alt="Do Your Own Research" />
              </picture>
            </div>
            <?php if ( $dyor_category->description ) { ?>
            <div class="background-white ui-rounded-box pt-3 pb-3 pl-4 pr-4">
              <p class="font-size-12 font-size-s-11 font-weight-bold mb-2"><?php echo esc_html( wp_strip_all_tags( $dyor_category->description ) ); ?></p>
              <?php if ( ! empty( $figma_file_key ) ) { ?>
              <a href="<?php echo esc_url( $category_link . '#map' ); ?>" class="ui-button ui-button--black">Explore the Map</a>
              <?php } ?>
            </div>
            <?php } ?>
          </div>

          <div class="grid-item is-s-24 is-xxl-12 mb-4">
            <div class="background-white ui-rounded-box pt-3 pb-3 pl-4 pr-4">
              <?php if ( ! empty( $featured_youtube ) ) { ?>
              <div class="ui-embed-container ui-rounded-box mb-3">
                <?php echo render_youtube_embed_iframe( $featured_youtube, false, 'lazy', get_the_title( $featured->ID ) ); ?>
              </div>
              <?php } else { ?>
              <div class="mb-3">
                <a href="<?php echo esc_url( get_the_permalink( $featured->ID ) ); ?>" class="ui-hover">
                  <?php render_thumbnail( $featured->ID, 'col12-16to9', array( 'class' => 'ui-rounded-box' ) ); ?>
                </a>
              </div>
              <?php } ?>
              <a href="<?php echo esc_url( get_the_permalink( $featured->ID ) ); ?>" class="ui-hover">
                <h3 class="font-size-13 font-weight-bold text-wrap-pretty mb-1"><?php echo esc_html( get_the_title( $featured->ID ) ); ?></h3>
                <div class="font-size-11 mb-0"><?php render_standfirst( $featured->ID ); ?></div>
              </a>
            </div>
          </div>

        </div>
      </div>

      <?php // Bottom: full-width recent episodes 2×2 grid ?>

      <?php if ( ! empty( $recent ) ) { ?>
      <div class="grid-item is-xxl-24">
        <div class="background-white ui-rounded-box pt-3 pb-3 pl-4 pr-4">
          <div class="grid-row grid-row--nested">
            <div class="grid-item is-xxl-24">
              <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">
                <div class="layout-split-level font-size-8 font-weight-bold">
                  <h4 class="font-weight-bold text-uppercase">Recent Episodes</h4>
                  <span>See All</span>
                </div>
              </a>
            </div>
            <?php foreach ( $recent as $recent_post ) { ?>
            <div class="grid-item is-s-12 is-xxl-6 mt-3">
              <a href="<?php echo esc_url( get_the_permalink( $recent_post->ID ) ); ?>" class="ui-hover">
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
