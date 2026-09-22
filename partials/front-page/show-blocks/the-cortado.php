<?php
/**
 * Front page product block: The Cortado.
 *
 * Three stacked sections — signup banner, latest Cortado, past Cortados — inside one ochre
 * box, following docs/architecture/boxed-sections.md and the inline signup block's
 * treatment. Bails out entirely if the category or its posts are missing, the same way the
 * other show blocks do.
 */

$cortado_category = get_term_by( 'slug', 'the-cortado', 'category' );

if ( ! $cortado_category ) {
  return;
}

// One query covers both sections: the first post is the featured "Latest Cortado", the
// remaining three fill the "Past Cortados" grid — the same split the category archive
// makes on its main query. WP_Query rather than get_posts() because the shared
// post-layout partial reads the global $post, so the grid needs a real loop.
$cortado_query = new WP_Query(
  array(
    'posts_per_page' => 4,
    'cat'            => $cortado_category->term_id,
  )
);

if ( ! $cortado_query->have_posts() ) {
  return;
}

$cortado_query->the_post();
$featured_post_id = get_the_ID();

$category_link   = get_category_link( $cortado_category->term_id );
$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/the-cortado/';

// The newsletter record supplies the Mailchimp key, as on the category archive.
$newsletter         = get_posts(
  array(
    'post_type'      => 'newsletter',
    'name'           => 'the-cortado',
    'posts_per_page' => 1,
  )
);
$newsletter_post_id = ! empty( $newsletter ) ? $newsletter[0]->ID : false;
$mailchimp_key      = $newsletter_post_id ? get_post_meta( $newsletter_post_id, '_nm_mailchimp_key', true ) : false;

// Signup copy and button label come off the same record, so a cadence or presenter change
// is an edit rather than a deploy. Copy is serif with the presenter names (the bold spans)
// in sans, as on the inline signup block — .front-page-cortado__copy carries the face switch.
$signup_copy  = $newsletter_post_id ? get_post_meta( $newsletter_post_id, '_nm_banner_text', true ) : '';
$button_label = $newsletter_post_id ? get_post_meta( $newsletter_post_id, '_nm_banner_button_label', true ) : '';

if ( empty( $button_label ) ) {
  $button_label = 'Sign up';
}
?>
<section class="container mt-4 mb-4" data-testid="front-page-cortado">
  <div class="grid-item is-xxl-24">
    <div class="grid-row front-page-cortado background-ochre ui-rounded-box ui-backgrounded-box-padding">

      <?php // ── Sign-up banner ── ?>
      <div class="grid-item is-xxl-24">
        <div class="grid-row grid-row--nested">
          <div class="grid-item is-s-24 is-l-12 is-xxl-8 mb-s-4 mb-l-4">
            <?php // Wordmark and copy share one link to the archive. The span names the SVG, so the link's accessible name reads "The Cortado" followed by the copy. ?>
            <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover u-display-block">
              <span class="front-page-cortado__wordmark" role="img" aria-label="The Cortado">
                <?php echo nm_get_file( '/dist/img/products/the-cortado/the-cortado-wordmark.svg' ); ?>
              </span>
              <?php if ( ! empty( $signup_copy ) ) { ?>
              <p class="front-page-cortado__copy font-serif font-size-11 mt-3 text-wrap-pretty"><?php echo wp_kses_post( $signup_copy ); ?></p>
              <?php } ?>
            </a>
          </div>

          <div class="grid-item is-s-24 is-l-12 is-xxl-8 mb-s-4 mb-l-4 front-page-cortado__presenters-col">
            <picture>
              <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.avif' ); ?>" type="image/avif">
              <source srcset="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.webp' ); ?>" type="image/webp">
              <img class="front-page-cortado__presenters" src="<?php echo esc_url( $base_image_path . 'the-cortado-presenters.png' ); ?>" alt="" width="1195" height="762" loading="lazy" />
            </picture>
          </div>

          <?php if ( $mailchimp_key ) { ?>
          <div class="grid-item is-s-24 is-l-24 is-xxl-8">
            <?php render_mailchimp_signup_form( $mailchimp_key, 'white', 'black', $button_label ); ?>
          </div>
          <?php } ?>

          <?php // No top margin: the rule sits directly under the presenters, which are flush to it. ?>
          <div class="grid-item is-xxl-24">
            <div class="ui-border-bottom ui-border--black"></div>
          </div>
        </div>
      </div>

      <?php // ── Latest Cortado: one white inner card, image left, copy right ── ?>
      <div class="grid-item is-xxl-24 mt-4">
        <div class="background-white ui-rounded-box ui-rounded-box--nested pt-3 pb-3 pl-4 pr-4">
          <div class="grid-row grid-row--nested">
            <div class="grid-item is-s-24 is-xxl-12 mb-s-4">
              <a href="<?php echo esc_url( get_the_permalink( $featured_post_id ) ); ?>" class="ui-hover u-display-block">
                <?php render_thumbnail( $featured_post_id, 'col12-16to9', array( 'class' => 'ui-rounded-box u-display-block' ) ); ?>
              </a>
            </div>
            <div class="grid-item is-s-24 is-xxl-12">
              <p class="font-size-8 font-weight-bold text-uppercase">Latest Cortado</p>
              <a href="<?php echo esc_url( get_the_permalink( $featured_post_id ) ); ?>" class="ui-hover u-display-block">
                <h3 class="font-size-15 font-weight-bold text-wrap-pretty mt-2"><?php echo esc_html( get_the_title( $featured_post_id ) ); ?></h3>
                <p class="font-size-10 font-weight-bold text-uppercase mt-2"><?php render_bylines( $featured_post_id ); ?></p>
                <div class="font-size-10 mt-2"><?php render_standfirst( $featured_post_id ); ?></div>
              </a>
            </div>
          </div>
        </div>
      </div>

      <?php // ── Past Cortados: heading on the ochre, one white inner card per post ── ?>
      <?php if ( $cortado_query->have_posts() ) { ?>
      <div class="grid-item is-xxl-24 mt-4">
        <div class="grid-row grid-row--nested">
          <div class="grid-item is-xxl-24 mb-4">
            <?php
            // Two links to the same archive: the eyebrow reads as an eyebrow, "See all" as an action
            // link. The span keeps the action link inline — as a direct flex child it would be
            // blockified and its gradient underline would drop to the line box's bottom edge.
            ?>
            <div class="layout-split-level font-size-9 font-weight-bold">
              <h3 class="font-weight-bold text-uppercase"><a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">Past Cortados</a></h3>
              <span><a href="<?php echo esc_url( $category_link ); ?>" class="ui-action-link ui-action-link--small">See all</a></span>
            </div>
          </div>
          <?php
          while ( $cortado_query->have_posts() ) {
            $cortado_query->the_post();

            get_template_part(
              'partials/post-layouts/archive-post-no-thumbnail',
              null,
              array(
                'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-s-4 mb-l-4',
                'hide-excerpt'      => true,
                'boxed'             => true,
              )
            );
          }
          ?>
        </div>
      </div>
      <?php } ?>
      <?php wp_reset_postdata(); ?>

    </div>
  </div>
</section>
