<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$category = get_category( get_query_var( 'cat' ) );

$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );
$youtube_copy_override = get_term_meta( $category->term_id, '_nm_youtube_text', true );

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Listen to the podcast';
$youtube_copy = ! empty( $youtube_copy_override ) ? $youtube_copy_override : 'Watch on YouTube';

$base_image_path = get_stylesheet_directory_uri() . '/dist/img/specials/dyor/';

get_header();
?>
<style type="text/css">
  .dyor-archive {
    --dyor-green: #10E920;
    --dyor-bg: #F8F5F5;
  }

  /* Hero */
  .dyor-archive__hero {
    position: relative;
    overflow: hidden;
  }

  .dyor-archive__hero-image {
    width: 100%;
    height: auto;
    display: block;
    aspect-ratio: 1410 / 561;
    object-fit: cover;
  }

  .dyor-archive__hero-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    pointer-events: none;
  }

  .dyor-archive__intro-text {
    max-width: 897px;
    margin-left: auto;
    margin-right: auto;
  }

  .dyor-archive__intro-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
  }

  /* Map embed section */
  .dyor-archive__map-embed {
    width: 100%;
    border-radius: 4px;
    min-height: 450px;
  }

  .dyor-archive__map-embed iframe {
    width: 100%;
    height: 100%;
    min-height: 450px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 4px;
  }
</style>

<main id="main-content" class="dyor-archive" data-testid="main-content">

  <?php // ── Section 1: Hero ── ?>
  <section class="container">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <div class="dyor-archive__hero">
          <?php // ── TODO: render assets and add here ── ?>
          <img class="dyor-archive__hero-image" src="<?php echo esc_url( $base_image_path . 'dyor-hero.jpg' ); ?>" alt="Do Your Own Research" />
        </div>
      </div>
    </div>
  </section>

  <?php // ── Section 2: Intro / Description ── ?>
  <section class="container mb-6">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <div class="dyor-archive__intro">
          <div class="dyor-archive__intro-text font-size-13 mb-4 text-align-center">
            <?php echo category_description(); ?>
          </div>
          <div class="dyor-archive__intro-buttons">
            <a class="ui-button ui-button--black" href="<?php echo esc_url( $podcast_url ?? '#' ); ?>" target="_blank" rel="nofollow"><?php echo esc_html( $podcast_copy ); ?></a>
            <a class="ui-button ui-button--red" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow"><?php echo esc_html( $youtube_copy ); ?></a>
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
              <a href="<?php the_permalink(); ?>">
                  <?php // ── TODO: make this default to youtube embed ── ?>
                <?php the_post_thumbnail( 'col16-16to9', array( 'class' => 'index-post-thumbnail ui-rounded-box' ) ); ?>
              </a>
            </div>
            <div class="dyor-archive__latest-episode-text grid-item is-xxl-8 is-s-24">
              <h2 class="font-size-14 font-size-s-13 font-weight-bold text-wrap-pretty">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
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
  <section class="container mb-5">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-4 text-align-center">
        <h4 class="ui-boxed-title">Explore the Map</h4>
      </div>
      <div class="grid-item is-xxl-24">
        <div class="dyor-archive__map-embed">
          <iframe
            src="https://embed.figma.com/board/Twc9z7w8yaEzaO6m0PM1Kj/Do-Your-Own-Research--Map?&footer=false&page-selector=false&node-id=125-70&embed-host=share"
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </div>
  </section>

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
            'text-size'         => 'regular',
          )
          );
        }
      } else {
        ?>
        <p>Sorry, nothing matched your criteria :/</p>
        <?php
      }
      ?>
    </div>

    <div class="grid-row mt-4 mb-5">
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
      'container_classes'     => 'mt-5 mb-5',
      'on_colored_background' => false,
    )
  );
  ?>

</main>
<?php
get_footer();
?>
