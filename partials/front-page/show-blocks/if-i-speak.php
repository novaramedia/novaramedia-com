<?php
/**
 * Front page product block: If I Speak.
 *
 * One white box, three columns at desktop: wordmark, blurb and follow links; the
 * presenters cut-out sitting on the box's bottom edge; and a scrolling list of recent
 * episodes that fades out at the bottom. Below $breakpoint-s the columns stack and the
 * list shows three episodes with no scroller. Follows docs/architecture/boxed-sections.md.
 *
 * The blurb comes from the category's term meta, as on the archive (category-if-i-speak.php),
 * so the two stay in step without a deploy. The follow links are a partial shared with
 * the archive.
 *
 * Bails out entirely if the category or its posts are missing, the same way the other
 * show blocks do.
 */

$iis_category = get_term_by( 'slug', 'if-i-speak', 'category' );

if ( ! $iis_category ) {
  return;
}

$iis_posts = get_posts(
  array(
    'posts_per_page' => 6,
    'category'       => $iis_category->term_id,
  )
);

if ( empty( $iis_posts ) ) {
  return;
}

$category_link   = get_category_link( $iis_category->term_id );
$base_image_path = get_stylesheet_directory_uri() . '/dist/img/products/if-i-speak/';

// Formatted description first (bold/italic allowed), core description as the fallback —
// the same order the archive hero uses.
$formatted_description = get_term_meta( $iis_category->term_id, '_nm_category_formatted_description', true );
$blurb                 = ! empty( $formatted_description ) ? wpautop( $formatted_description ) : category_description( $iis_category->term_id );

// Same srcset as the archive hero; the column is narrower here, so sizes is too.
$presenters_sizes = '(max-width: 759px) calc(100vw - 3rem), (max-width: 1104px) 33vw, 600px';
?>
<section class="container mt-4 mb-4" data-testid="front-page-if-i-speak">
  <div class="grid-item is-xxl-24">
    <div class="grid-row front-page-if-i-speak background-white ui-rounded-box ui-backgrounded-box-padding ui-backgrounded-box-padding--flush-bottom">

      <?php // ── Wordmark, blurb, follow links ── ?>
      <div class="grid-item is-xxl-7 is-l-8 is-s-24 pb-4">
        <h3 class="mb-5 mb-s-4">
          <?php // The inlined SVG's <title> names the link "If I Speak"; the span is only a styling hook. ?>
          <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover u-display-block">
            <span class="front-page-if-i-speak__wordmark">
              <?php echo nm_get_file( '/dist/img/products/if-i-speak/if-i-speak-wordmark.svg' ); ?>
            </span>
          </a>
        </h3>
        <?php if ( ! empty( $blurb ) ) { ?>
        <div class="front-page-if-i-speak__copy font-size-12 font-size-l-11 text-wrap-pretty mb-5 mb-s-4">
          <?php echo wp_kses_post( $blurb ); ?>
        </div>
        <?php } ?>
        <?php
        get_template_part(
          'partials/specials/if-i-speak-follow-links',
          null,
          array( 'testid' => 'front-page-if-i-speak-follow-links' )
        );
        ?>
      </div>

      <?php // ── Presenters, flush to the box's bottom edge ── ?>
      <div class="front-page-if-i-speak__presenters grid-item is-xxl-10 is-l-8 is-s-24">
        <picture>
          <source srcset="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters-1080.avif' ); ?> 1080w, <?php echo esc_url( $base_image_path . 'if-i-speak-presenters.avif' ); ?> 1717w" sizes="<?php echo esc_attr( $presenters_sizes ); ?>" type="image/avif" />
          <source srcset="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters-1080.webp' ); ?> 1080w, <?php echo esc_url( $base_image_path . 'if-i-speak-presenters.webp' ); ?> 1717w" sizes="<?php echo esc_attr( $presenters_sizes ); ?>" type="image/webp" />
          <img class="u-display-block" src="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters.png' ); ?>" srcset="<?php echo esc_url( $base_image_path . 'if-i-speak-presenters-1080.png' ); ?> 1080w, <?php echo esc_url( $base_image_path . 'if-i-speak-presenters.png' ); ?> 1717w" sizes="<?php echo esc_attr( $presenters_sizes ); ?>" alt="Ash Sarkar and Moya Lothian-McLean" width="1717" height="1468" loading="lazy" />
        </picture>
      </div>

      <?php // ── Recent episodes ── ?>
      <div class="front-page-if-i-speak__episodes grid-item is-xxl-7 is-l-8 is-s-24 pt-s-4 pb-s-4" data-testid="front-page-if-i-speak-episodes">
        <?php // Span keeps the action link inline; as a direct flex child it would be blockified and its underline would drop. ?>
        <div class="layout-split-level font-size-8 font-weight-bold mb-4">
          <h4 class="font-weight-bold text-uppercase"><a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">Recent Episodes</a></h4>
          <span><a href="<?php echo esc_url( $category_link ); ?>" class="ui-action-link ui-action-link--small">See all</a></span>
        </div>
        <div class="front-page-if-i-speak__scroller ux-scroller">
          <div class="ux-scroller__fade-top"></div>
          <div class="ux-scroller__fade-bottom"></div>
          <div class="ux-scroller__inner">
            <?php foreach ( $iis_posts as $iis_post ) { ?>
            <div class="layout-flex pb-3 mb-3 ui-border-bottom">
              <?php // Audio icon links to the archive; it sits beside the title link, never inside it. ?>
              <div class="layout-flex-no-shrink mr-2">
                <?php render_post_ui_tags( $iis_post->ID, false, true ); ?>
              </div>
              <a href="<?php echo esc_url( get_the_permalink( $iis_post->ID ) ); ?>" class="ui-hover">
                <h5 class="front-page-if-i-speak__title font-size-11 font-weight-bold text-wrap-pretty"><?php echo esc_html( get_the_title( $iis_post->ID ) ); ?></h5>
              </a>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
