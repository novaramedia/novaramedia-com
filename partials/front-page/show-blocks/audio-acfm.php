<?php
/**
 * Render full-width ACFM-only audio block for front page.
 *
 * Top row: logo, blurb and the featured latest episode (artwork + SoundCloud
 * embed) in a 50%-width box, all on one line. Bottom row: the next 4 episodes
 * in a 4-up grid (one line on desktop).
 */

$acfm = get_term_by( 'slug', 'acfm', 'category' );

if ( $acfm ) {
  $latest = get_posts(
    array(
      'posts_per_page' => 5,
      'category'       => $acfm->term_id,
    )
  );

  if ( $latest ) {
    $term_link = get_term_link( $acfm );
    if ( is_wp_error( $term_link ) ) {
      return;
    }
    $featured  = $latest[0];
    $featured_id = $featured->ID;
    $featured_meta = get_post_meta( $featured_id );
    $recent = array_slice( $latest, 1 );
    ?>
    <section class="front-page__acfm container mt-4 mb-3">
      <div class="grid-row">
        <div class="grid-item is-s-24 is-xxl-24 font-color-black">
          <div class="background-light-blue ui-rounded-box pt-3 pl-4 pr-4 pb-3">
            <div class="grid-row grid--nested grid-row--align-center">
              <div class="grid-item is-s-24 is-xxl-3 mb-s-3">
                <div class="front-page__acfm-logo">
                  <a href="<?php echo esc_url( $term_link ); ?>" class="ui-hover">
                    <?php echo nm_get_file( '/dist/img/products/acfm/acfm-logo.svg' ); ?>
                  </a>
                </div>
              </div>
              <div class="grid-item is-s-24 is-xxl-9 mb-s-3">
                <a href="<?php echo esc_url( $term_link ); ?>" class="ui-hover">
                  <div class="font-size-11 text-wrap-pretty">
                    The home of the weird left. Nadia Idle, Jeremy Gilbert and Keir Milburn examine the links between left-wing politics, culture, music and experiences of collective joy.
                  </div>
                </a>
              </div>
              <div class="grid-item is-s-24 is-xxl-12">
                <div class="background-white font-color-black ui-rounded-box ui-rounded-box--nested pt-4 pb-4 pl-4 pr-4">
                  <div class="grid-row grid--nested">
                    <div class="grid-item is-s-24 is-xxl-7 is-l-10">
                      <div class="layout-thumbnail-frame">
                        <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                          <?php render_post_ui_tags( $featured_id, true, true, 'no-border' ); ?>
                        </div>
                        <a href="<?php echo esc_url( get_the_permalink( $featured_id ) ); ?>" class="ui-hover">
                          <?php
                          render_thumbnail(
                            $featured_id,
                            'col6-16to9',
                            array(
                              'class' => 'ui-rounded-box',
                            )
                          );
                          ?>
                        </a>
                      </div>
                    </div>
                    <div class="grid-item is-s-24 is-xxl-17 is-l-14">
                      <a href="<?php echo esc_url( get_the_permalink( $featured_id ) ); ?>" class="ui-hover">
                        <h3 class="font-size-11 font-weight-bold mb-2"><?php echo esc_html( get_the_title( $featured_id ) ); ?></h3>
                        <div class="font-size-10 mb-3 text-wrap-pretty">
                          <?php render_short_description( $featured_id ); ?>
                        </div>
                      </a>
                      <?php
                      if ( ! empty( $featured_meta['_cmb_sc'][0] ) ) {
                        render_soundcloud_embed_iframe(
                          $featured_meta['_cmb_sc'][0],
                          'mini',
                          true,
                          array(
                            'inverse' => 'false',
                          )
                        );
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="ui-border-top mt-3 pt-3">
              <a href="<?php echo esc_url( $term_link ); ?>" class="ui-hover">
                <div class="grid-row grid--nested mb-2">
                  <div class="grid-item is-xxl-12">
                    <h4 class="font-size-9 font-weight-bold text-uppercase">Recent Episodes</h4>
                  </div>
                  <div class="grid-item is-xxl-12 text-align-right">
                    <span class="font-size-9 font-weight-bold">See All</span>
                  </div>
                </div>
              </a>
              <div class="grid-row grid--nested">
                <?php
                foreach ( $recent as $episode ) {
                  $episode_id = $episode->ID;
                  ?>
                  <div class="grid-item is-s-24 is-m-12 is-xxl-6 mt-2 mb-2">
                    <a href="<?php echo esc_url( get_the_permalink( $episode_id ) ); ?>" class="ui-hover">
                      <div class="font-size-8 font-weight-bold mb-2">
                        <?php echo esc_html( get_the_time( NM_DATE_FORMAT_LONG, $episode_id ) ); ?>
                      </div>
                    </a>
                    <h4 class="font-size-10 font-weight-bold mb-2">
                      <?php render_post_ui_tags( $episode_id, false, true ); ?> <a href="<?php echo esc_url( get_the_permalink( $episode_id ) ); ?>" class="ui-hover"><?php echo esc_html( get_the_title( $episode_id ) ); ?></a>
                    </h4>
                    <a href="<?php echo esc_url( get_the_permalink( $episode_id ) ); ?>" class="ui-hover">
                      <div class="text-wrap-pretty">
                        <?php render_short_description( $episode_id ); ?>
                      </div>
                    </a>
                  </div>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
  }
}
