<?php
get_header();

$category = get_category( get_query_var( 'cat' ) );

$podcast_url = ! empty( get_term_meta( $category->term_id, '_nm_podcast_url', true ) ) ? get_term_meta( $category->term_id, '_nm_podcast_url', true ) : false;
$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Subscribe in your podcast app';

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
            <a class="only-desktop ui-button ui-button--black ui-button--small mt-3" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
            <?php } ?>
          </div>
          <div class="grid-item is-xxl-24 only-mobile">
          <?php if ( $podcast_url ) { ?>
            <a class="ui-button ui-button--black ui-button--small" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
          <?php } ?>
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
