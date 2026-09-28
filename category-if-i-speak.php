<?php
get_header();

$category = get_category( get_query_var( 'cat' ) );

$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/if-i-speak/';

$podcast_url = ! empty( get_term_meta( $category->term_id, '_nm_podcast_url', true ) ) ? get_term_meta( $category->term_id, '_nm_podcast_url', true ) : false;
$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Subscribe to the podcast';

// Hero blurb is the category's formatted description (bold/italic allowed); empty falls back
// to the core description, which stays plain text for Open Graph and meta descriptions.
$formatted_description = get_term_meta( $category->term_id, '_nm_category_formatted_description', true );
?>
<main id="main-content" class="category-archive category-archive__if-i-speak" data-testid="main-content">
  <?php // ── Section 1: Hero ── ?>
  <section class="container mt-4 mb-4" data-testid="if-i-speak-hero">
    <div class="grid-item is-xxl-24">
      <div class="grid-row background-white ui-rounded-box ui-backgrounded-box-padding ui-backgrounded-box-padding--flush-bottom">
        <div class="grid-item is-s-24 is-xxl-12 pb-4">
          <h1 class="category-archive__if-i-speak__wordmark mt-4 mb-5 mb-s-4" aria-label="If I Speak">
            <?php echo nm_get_file( '/dist/img/products/if-i-speak/if-i-speak-wordmark.svg' ); ?>
          </h1>
          <div class="category-archive__if-i-speak__copy font-size-12 font-size-s-11 font-weight-bold mb-5 mb-s-4" data-testid="if-i-speak-blurb">
            <?php echo ! empty( $formatted_description ) ? wp_kses_post( wpautop( $formatted_description ) ) : category_description(); ?>
          </div>
          <?php if ( $podcast_url ) { ?>
          <a class="category-archive__if-i-speak__cta ui-button ui-button--red ui-button--auto-height" data-testid="if-i-speak-subscribe" href="<?php echo esc_url( $podcast_url ); ?>" target="_blank" rel="nofollow noopener noreferrer"><?php echo esc_html( $podcast_copy ); ?></a>
          <?php } ?>
        </div>
        <div class="category-archive__if-i-speak__presenters grid-item is-s-24 is-xxl-12">
          <?php
          // 1080w for phones and tablets (photo is ~90% of the box below 759px, up to 3x);
          // 1717w for the half-width desktop column. The browser picks by width; avif/webp
          // keep the cut-out's alpha.
          $presenters_sizes = '(max-width: 759px) calc(90vw - 3rem), (max-width: 1400px) 50vw, 700px';
          ?>
          <picture>
            <source srcset="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters-1080.avif' ); ?> 1080w, <?php echo esc_url( $base_image_path . 'if-i-speak-presenters.avif' ); ?> 1717w" sizes="<?php echo esc_attr( $presenters_sizes ); ?>" type="image/avif" />
            <source srcset="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters-1080.webp' ); ?> 1080w, <?php echo esc_url( $base_image_path . 'if-i-speak-presenters.webp' ); ?> 1717w" sizes="<?php echo esc_attr( $presenters_sizes ); ?>" type="image/webp" />
            <img class="u-display-block" src="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters.png' ); ?>" srcset="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters-1080.png' ); ?> 1080w, <?php echo esc_url( $base_image_path . 'if-i-speak-presenters.png' ); ?> 1717w" sizes="<?php echo esc_attr( $presenters_sizes ); ?>" alt="Ash Sarkar and Moya Lothian-McLean" width="1717" height="1468" loading="eager" fetchpriority="high" />
          </picture>
        </div>
      </div>
    </div>
  </section>

  <?php // ── Section 2: Episodes ── ?>
  <section class="container category-archive__if-i-speak__episodes mt-5 mb-5" data-testid="if-i-speak-episodes">
    <div class="grid-row">
      <?php
      if ( have_posts() ) {
        while ( have_posts() ) {
          the_post();

          get_template_part(
            'partials/post-layouts/archive-post-pure-text',
            null,
            array(
              'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-5 mb-s-4',
            )
          );
        }
      } else {
        ?>
      <article class="grid-item is-s-24"><?php esc_html_e( 'Sorry, nothing matched your criteria :/' ); ?></article>
        <?php
      }
      ?>
    </div>
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
