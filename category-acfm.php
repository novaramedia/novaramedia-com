<?php
get_header();

$category = get_category( get_query_var( 'cat' ) );

$podcast_url = ! empty( get_term_meta( $category->term_id, '_nm_podcast_url', true ) ) ? get_term_meta( $category->term_id, '_nm_podcast_url', true ) : false;
$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Subscribe to the podcast';

$newsletter = get_posts(
  array(
    'post_type'   => 'newsletter',
    'title'       => 'ACFM',
    'numberposts' => 1,
  )
);

$newsletter_post_id = false;
$newsletter_mailchimp_key = false;

if ( ! empty( $newsletter ) ) {
  $newsletter_post_id = $newsletter[0]->ID;
  $newsletter_meta    = get_post_meta( $newsletter_post_id );
  $newsletter_mailchimp_key = ! empty( $newsletter_meta['_nm_mailchimp_key'] ) ? $newsletter_meta['_nm_mailchimp_key'][0] : false;
}

$should_render_newsletter = $newsletter_post_id && $newsletter_mailchimp_key;
?>
<main id="main-content" class="category-archive category-archive__acfm">
  <style type="text/css">
    .category-archive__acfm__logo svg {
      width: auto;
      max-height: 200px;
    }

    @media screen and (max-width: 1104px) {
      .category-archive__acfm__logo svg {
        max-height: 180px;
      }
    }

    @media screen and (max-width: 910px) {
      .category-archive__acfm__logo svg {
        max-height: 120px;
      }
    }

    @media screen and (max-width: 759px) {
      .category-archive__acfm__logo svg {
        max-height: 120px;
      }
    }
  </style>
  <div class="container">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <div class="grid-row grid-row--nested background-acfm-pink font-color-white ui-rounded-box ui-backgrounded-box-padding">
          <div class="grid-item is-s-16 is-xxl-8 font-size-12 font-weight-bold text-paragraph-breaks">
            <?php echo category_description(); ?>
          </div>
          <div class="category-archive__acfm__logo grid-item is-s-8 is-xxl-8 text-align-center">
            <?php echo nm_get_file( '/dist/img/products/acfm/acfm-logo.svg' ); ?>
          </div>
          <div class="grid-item is-s-24 is-xxl-8">
            <?php
            if ( get_term_meta( $category->term_id, '_nm_podcast_url', true ) ) {
              $podcast_url = get_term_meta( $category->term_id, '_nm_podcast_url', true );
              ?>
            <a class="ui-button ui-button--white ui-button--small mb-3" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
              <?php
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="grid-row mb-4">
<?php
$newsletter_inserted = false;
$has_posts = false;

if ( have_posts() ) {
  $has_posts = true;
  $post_count = 0;
  while ( have_posts() ) {
    the_post();
    ++$post_count;

    get_template_part(
        'partials/post-layouts/flex-post',
        null,
        array(
      'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
      'image-size'        => 'col12-16to9',
    )
        );

    // Display newsletter signup after 6th post.
    if ( $post_count === 6 && $should_render_newsletter && ! $newsletter_inserted ) {
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
