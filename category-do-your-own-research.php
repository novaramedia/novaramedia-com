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

// Map embed settings
$figma_file_key     = get_term_meta( $category->term_id, '_nm_dyor_figma_file_key', true );
$figma_default_node = get_term_meta( $category->term_id, '_nm_dyor_figma_default_node_id', true );

// Node ID priority: latest episode's node > default node > none
$map_node_id = '';
if ( ! empty( $figma_file_key ) ) {
  $latest_node_query = new WP_Query( array(
    'cat'            => $category->term_id,
    'posts_per_page' => 1,
    'post_status'    => 'publish',
    'meta_key'       => '_nm_dyor_figma_node_id',
    'meta_compare'   => '!=',
    'meta_value'     => '',
  ) );
  if ( $latest_node_query->have_posts() ) {
    $latest_node_query->the_post();
    $map_node_id = get_post_meta( get_the_ID(), '_nm_dyor_figma_node_id', true );
    wp_reset_postdata();
  }
  if ( empty( $map_node_id ) && ! empty( $figma_default_node ) ) {
    $map_node_id = $figma_default_node;
  }
}

get_header();
?>

<main id="main-content" class="dyor-archive" data-testid="main-content">

  <?php // ── Section 1: Hero ── ?>
  <section class="container mt-4 mb-5">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <div class="grid-row--nested dyor-archive__hero-background ui-rounded-box ui-rounded-box--top">
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

  <?php // ── Section 3: Latest Episode (page 1 only) ── ?>
  <?php if ( ! is_paged() ) { ?>
  <section class="container mb-5">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 text-align-center mb-4">
        <h4 class="ui-boxed-title">The Latest Episode</h4>
      </div>
    </div>

     <?php
        // Get the latest post in the category
        $latest_args = array(
          'cat'            => $category->term_id,
          'posts_per_page' => 1,
          'post_status'    => 'publish',
        );
        $latest_query = new WP_Query( $latest_args );
        if ( $latest_query->have_posts() ) {
          $latest_query->the_post();
          $latest_meta = get_post_meta( get_the_ID() );
          $latest_description = ! empty( $latest_meta['_cmb_short_desc'][0] ) ? $latest_meta['_cmb_short_desc'][0] : get_the_excerpt();
          ?>
          <div class="dyor-archive__latest-episode grid-row">
            <div class="dyor-archive__latest-episode-image grid-item is-xxl-16 is-s-24 mb-s-4">
              <div class="u-video-embed-container ui-rounded-image">
                <?php if ( ! empty( $latest_meta['_cmb_utube'][0] ) ) { ?>
                  <?php echo render_youtube_embed_iframe( $latest_meta['_cmb_utube'][0], true, false ); ?>
                <?php } else { ?>
                  <?php render_thumbnail( get_the_ID(), 'col16-16to9', array( 'class' => 'ui-rounded-image' ) ); ?>
                <?php } ?>
              </div>
            </div>
            <div class="dyor-archive__latest-episode-text grid-item is-xxl-8 is-s-24">
              <h2 class="font-size-14 font-size-s-13 font-weight-bold text-wrap-pretty">
                <?php the_title(); ?>
              </h2>
              <h3 class="font-size-12 font-size-s-11 font-weight-bold mt-3 mt-s-2 text-wrap-pretty">
                <?php render_standfirst( get_the_ID() ); ?>
              </h3>
              <div class="font-size-10 mt-3 mt-s-2 text-wrap-pretty">
                <?php echo wp_kses_post( $latest_description ); ?>
              </div>
            </div>
          </div>
          <?php
          wp_reset_postdata();
        }
        ?>
  </section>
  <?php } ?>

  <?php // ── Section 4: Explore the Map ── ?>
  <?php // TODO: When cookie consent gate is implemented, this Figma embed must be gated behind it (third-party iframe). ?>
  <?php
  if ( ! empty( $figma_file_key ) ) {
    $embed_params = array(
      'embed-host'    => 'share',
      'footer'        => 'false',
      'page-selector' => 'false',
    );
    if ( ! empty( $map_node_id ) ) {
      $embed_params['node-id'] = $map_node_id;
    }
    $map_src = 'https://embed.figma.com/board/' . rawurlencode( $figma_file_key ) . '/Do-Your-Own-Research-Map?' . http_build_query( $embed_params );
  ?>
  <section class="container mb-5">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <div class="grid-row--nested background-white ui-rounded-box pt-4 pb-4">
          <div class="grid-item is-xxl-24 mb-4 text-align-center">
            <h4 class="ui-boxed-title">Explore the Map</h4>
          </div>
          <div class="grid-item is-xxl-24">
            <div class="dyor-archive__map-embed">
              <iframe
                src="<?php echo esc_url( $map_src ); ?>"
                title="Do Your Own Research – Interactive Map"
                loading="lazy"
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php } ?>

  <?php // ── Section 5: Newsletter Signup ──  But just via partial, and only once it actually exists ?>

  <?php // ── Section 6: Post Grid ── ?>
  <section class="container" data-testid="post-list">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 text-align-center mb-4">
        <h4 class="ui-boxed-title">The Full Archive</h4>
      </div>
      <?php
      if ( have_posts() ) {
        while ( have_posts() ) {
          the_post();

          get_template_part(
          'partials/post-layouts/archive-post',
          null,
          array(
            'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
            'image-size'        => 'col12-16to9',
            'text-size'         => 'large',
          )
          );
        }
      } else {
        ?>
        <p>Sorry, nothing matched your criteria :/</p>
        <?php
      }
      ?>
      <div class="grid-item is-xxl-24">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
    </div>
  </section>

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
