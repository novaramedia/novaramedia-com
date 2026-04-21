<?php
get_header();

$category = get_category( get_query_var( 'cat' ) );

$podcast_url = ! empty( get_term_meta( $category->term_id, '_nm_podcast_url', true ) ) ? get_term_meta( $category->term_id, '_nm_podcast_url', true ) : false;
$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Subscribe in your podcast app';

$query_var_paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$is_first_page = ( $query_var_paged === 1 );

$newsletter = get_posts(
  array(
    'post_type'      => 'newsletter',
    'name'           => 'the-downstream-newsletter',
    'posts_per_page' => 1,
  )
);

$newsletter_post_id = false;
$newsletter_mailchimp_key = false;
$display_newsletter_after = 6; // Display newsletter signup after this many posts.

if ( $is_first_page ) {
  ++$display_newsletter_after; // On first page, display after one more (to account for featured post).
}

if ( ! empty( $newsletter ) ) {
  $newsletter_post_id = $newsletter[0]->ID;
  $newsletter_meta = get_post_meta( $newsletter_post_id );
  $newsletter_mailchimp_key = ! empty( $newsletter_meta['_nm_mailchimp_key'] ) ? $newsletter_meta['_nm_mailchimp_key'][0] : false;
}

$should_render_newsletter = $newsletter_post_id && $newsletter_mailchimp_key;
?>
<main id="main-content" class="category-archive category-archive__downstream">
  <div class="container">
    <div class="grid-row mt-4 mb-4">
      <div class="grid-item is-xxl-24">
        <div class="grid-row grid-row--nested background-gray-base ui-rounded-box ui-backgrounded-box-padding">
          <div class="grid-item is-xxl-24">
            <h4 class="font-size-13"><strong>Downstream</strong> is an in-depth interview show featuring conversations with activists, authors, economists, politicians, scientists, philosophers and thinkers of all stripes.</h4>
            <?php if ( $podcast_url ) { ?>
            <a class="only-desktop ui-button ui-button--black ui-button--small mt-3" href="<?php echo esc_url( $podcast_url ); ?>" target="_blank" rel="nofollow"><?php echo esc_html( $podcast_copy ); ?></a>
            <?php } ?>
          </div>
          <div class="grid-item is-xxl-24 only-mobile">
          <?php if ( $podcast_url ) { ?>
            <a class="ui-button ui-button--black ui-button--small" href="<?php echo esc_url( $podcast_url ); ?>" target="_blank" rel="nofollow"><?php echo esc_html( $podcast_copy ); ?></a>
          <?php } ?>
        </div>
        </div>
      </div>
    </div>
  </div>

<?php
$newsletter_inserted = false;
$has_posts = false;

if ( have_posts() ) {
  $has_posts = true;
  $post_count = 0;

  // First page: Show featured layout for the first post
  if ( $is_first_page ) {
    ?>
  <div class="container">
    <div class="grid-row">
      <?php
      the_post();
      ++$post_count;
      $featured_post_id = get_the_ID();
      $featured_meta = get_post_meta( $featured_post_id );
      $featured_youtube_id = ! empty( $featured_meta['_cmb_utube'] ) ? $featured_meta['_cmb_utube'][0] : false;

      $has_related = false;
      if ( ! empty( $featured_meta['_cmb_related_posts'] ) ) {
        $related_args = array(
          'posts_per_page' => 1,
          'post__in'       => explode( ', ', $featured_meta['_cmb_related_posts'][0] ),
          'orderby'        => 'rand',
        );
        $related_posts = new WP_Query( $related_args );
        $has_related = $related_posts->have_posts();
      }

      ?>
      <div class="grid-item is-s-24 is-l-14 is-xxl-16 mb-s-4">
        <?php if ( $featured_youtube_id ) { ?>
          <div class="u-video-embed-container ui-rounded-image">
            <?php echo render_youtube_embed_iframe( $featured_youtube_id, false, 'eager', get_the_title( $featured_post_id ) ); ?>
          </div>
        <?php } else { ?>
          <div class="layout-thumbnail-frame">
            <div class="layout-thumbnail-frame__inner mt-1 ml-1">
              <?php render_post_ui_tags( $featured_post_id, true, true, 'no-border' ); ?>
            </div>
            <a href="<?php the_permalink(); ?>" class="ui-hover">
              <?php render_thumbnail( $featured_post_id, 'col24-16to9', array( 'class' => 'ui-rounded-image' ) ); ?>
            </a>
          </div>
        <?php } ?>
      </div>
      <div class="grid-item is-s-24 is-l-10 is-xxl-8">
        <a href="<?php the_permalink(); ?>" class="ui-hover">
          <h6 class="font-size-15 font-weight-bold font-size-m-13 text-wrap-pretty"><?php the_title(); ?></h6>
          <h5 class="font-size-12 font-weight-bold mt-2 text-wrap-balance">
            <?php render_standfirst( $featured_post_id ); ?>
          </h5>
        </a>

        <div class="mt-4 font-size-11">
          <?php render_short_description( $featured_post_id ); ?>
        </div>
      </div>
      <div class="grid-item is-xxl-24 mt-4 mb-4">
        <hr />
      </div>
    </div>
  </div>
    <?php
  }
  ?>

  <div class="container">
    <div class="grid-row mb-4">
  <?php
  // Continue with remaining posts in grid layout
  while ( have_posts() ) {
    the_post();
    ++$post_count;

    get_template_part(
      'partials/post-layouts/archive-post',
      null,
      array(
        'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
        'image-size'        => 'col12-16to9',
        'text-size'         => 'regular',
      )
    );

    if ( $post_count === $display_newsletter_after && $should_render_newsletter && ! $newsletter_inserted ) {
      ?>
    </div>
  </div>
      <?php
      get_template_part(
        'partials/email-signup',
        null,
        array(
          'newsletter_post_id' => $newsletter_post_id,
        )
      );

      $newsletter_inserted = true;
      ?>
  <div class="container">
    <div class="grid-row mb-4">
      <?php
    }
  }
} else {
  ?>
    <article class="grid-item is-s-24">Sorry, nothing matched your criteria :/</article>
  <?php
}
?>
    </div>
  <?php
  if ( $has_posts && $should_render_newsletter && ! $newsletter_inserted ) {
    ?>
      </div>
    <?php
    get_template_part(
      'partials/email-signup',
      null,
      array(
      'newsletter_post_id' => $newsletter_post_id,
      )
    );

    $newsletter_inserted = true;
    ?>
    <div class="container">
    <?php
  }
  ?>
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
    </div>
  </div>
</main>
<?php
get_footer();
?>
