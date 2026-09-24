<?php
/**
 * Front page product block: Downstream.
 *
 * Show description, then the latest episode beside a grid of recent ones, inside one white
 * box following docs/architecture/boxed-sections.md. Bails out entirely if the category or
 * its posts are missing.
 */

$downstream_category = get_term_by( 'slug', 'downstream', 'category' );

if ( ! $downstream_category ) {
  return;
}

// One query: the first post is the featured episode, the remaining six fill the grid.
$latest_video = new WP_Query(
  array(
    'posts_per_page' => 7,
    'cat'            => $downstream_category->term_id,
  )
);

if ( ! $latest_video->have_posts() ) {
  return;
}

$category_link = get_category_link( $downstream_category->term_id );
?>
<section class="container mt-4 mb-4" data-testid="front-page-downstream">
  <div class="grid-item is-xxl-24">
    <div class="grid-row background-white ui-rounded-box ui-backgrounded-box-padding">

      <?php // ── Show description ── ?>
      <div class="grid-item is-xxl-24 mb-4">
        <h4 class="font-size-13 text-wrap-pretty"><a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover"><strong>Downstream</strong> is an in-depth interview show featuring conversations with activists, authors, economists, politicians, scientists, philosophers and thinkers of all stripes.</a></h4>
      </div>

      <?php
      // ── Latest episode ──
      $latest_video->the_post();
      $featured_post_id = get_the_ID();

      $meta        = get_post_meta( $featured_post_id );
      $has_related = false;

      if ( ! empty( $meta['_cmb_related_posts'] ) ) {
        $related_posts = new WP_Query(
          array(
            'posts_per_page' => 1,
            'post__in'       => explode( ', ', $meta['_cmb_related_posts'][0] ),
            'orderby'        => 'rand',
          )
        );
        $has_related   = $related_posts->have_posts();
      }

      $title_classes = $has_related ? 'is-m-24 is-xxl-16' : 'is-xxl-24';
      ?>
      <div class="grid-item is-s-24 is-l-14 is-xxl-16 mb-s-5">
        <div class="layout-thumbnail-frame">
          <div class="layout-thumbnail-frame__inner mt-1 ml-1">
            <?php render_post_ui_tags( $featured_post_id, true, true, 'no-border' ); ?>
          </div>
          <a href="<?php echo esc_url( get_the_permalink( $featured_post_id ) ); ?>" class="ui-hover u-display-block">
            <?php render_thumbnail( $featured_post_id, 'col24-16to9', array( 'class' => 'ui-rounded-box u-display-block' ) ); ?>
          </a>
        </div>

        <div class="grid-row grid-row--nested mt-4">
          <div class="grid-item <?php echo esc_attr( $title_classes ); ?>">
            <a href="<?php echo esc_url( get_the_permalink( $featured_post_id ) ); ?>" class="ui-hover">
              <h6 class="font-size-15 font-weight-bold font-size-m-13 text-wrap-pretty"><?php echo esc_html( get_the_title( $featured_post_id ) ); ?></h6>
              <h5 class="font-size-12 font-weight-bold mt-2 text-wrap-balance">
                <?php render_standfirst( $featured_post_id ); ?>
              </h5>
            </a>
          </div>
          <?php if ( $has_related ) { ?>
          <div class="grid-item is-m-24 is-xxl-8 ui-border-left ui-border--not-m mt-m-4">
            <?php render_see_also( $related_posts ); ?>
          </div>
            <?php
            wp_reset_postdata();
          }
          ?>
        </div>
      </div>

      <?php // ── Recent episodes ── ?>
      <div class="grid-item is-s-24 is-l-10 is-xxl-8">
        <a href="<?php echo esc_url( $category_link ); ?>" class="ui-hover">
          <div class="layout-split-level font-size-8 font-weight-bold mb-4">
            <h5 class="font-weight-bold text-uppercase">Recent Episodes</h5>
            <span>See All</span>
          </div>
        </a>
        <div class="grid-row grid-row--nested">
          <?php
          while ( $latest_video->have_posts() ) {
            $latest_video->the_post();

            // The grid is always two across, so the last row is the final two posts; their
            // bottom margin would add to the box's own padding.
            $is_last_row = $latest_video->current_post >= $latest_video->post_count - 2;
            ?>
          <div class="grid-item is-xxl-12<?php echo $is_last_row ? '' : ' mb-4'; ?>">
            <div class="layout-thumbnail-frame">
              <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                <?php render_post_ui_tags( get_the_ID(), false, true, 'no-border' ); ?>
              </div>
              <a href="<?php the_permalink(); ?>" class="ui-hover u-display-block">
                <?php render_thumbnail( get_the_ID(), 'col24-16to9', array( 'class' => 'ui-rounded-box u-display-block' ) ); ?>
              </a>
            </div>
            <a href="<?php the_permalink(); ?>" class="ui-hover">
              <h6 class="font-size-9 font-weight-bold mt-1">
                <?php render_video_title_and_standfirst( get_the_ID() ); ?>
              </h6>
            </a>
          </div>
            <?php
          }
          ?>
        </div>
      </div>
      <?php wp_reset_postdata(); ?>

    </div>
  </div>
</section>
