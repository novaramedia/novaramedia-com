<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$category = get_category( get_query_var( 'cat' ) );

$podcast_copy_override = get_term_meta( $category->term_id, '_nm_podcast_text', true );
$youtube_copy_override = get_term_meta( $category->term_id, '_nm_youtube_text', true );

$podcast_copy = ! empty( $podcast_copy_override ) ? $podcast_copy_override : 'Subscribe to the podcast';
$youtube_copy = ! empty( $youtube_copy_override ) ? $youtube_copy_override : 'Subscribe to our YouTube channel';


get_header();
?>
<main id="main-content" data-testid="main-content">
  <section id="posts" class="container mt-3" data-testid="post-list">
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24 is-l-12 is-xxl-12">
        <h4 class="font-size-17 font-weight-bold text-wrap-pretty">
          <?php echo $category->name; ?>
        </h4>
      </div>
      <div class="grid-item is-s-24 is-l-12 is-xxl-12 text-paragraph-breaks">
        <div class="font-size-12">
        <?php echo category_description(); ?>
        </div>
        <div class="mt-2 mb-2">
          <a class="ui-button ui-button--red ui-button--small" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow"><?php echo $youtube_copy; ?></a>
        </div>
        <a class="ui-button ui-button--black ui-button--small" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
      </div>
    </div>
  </section>
  <section id="posts" class="container mt-3" data-testid="post-list">
    <div class="grid-row mb-5">
      <div class="grid-item is-xxl-24 mb-4">
<iframe style="border: 1px solid rgba(0, 0, 0, 0.1);" width="100%" height="450" src="https://embed.figma.com/board/Twc9z7w8yaEzaO6m0PM1Kj/Do-Your-Own-Research--Map?&footer=false&page-selector=false&node-id=125-70&embed-host=share"></iframe>      </div>
<?php
if ( have_posts() ) {
  $post_count = 0;
  while ( have_posts() ) {
    the_post();
    ++$post_count;

    get_template_part(
      'partials/post-layouts/flex-post',
      null,
      array(
        'grid-item-classes' => 'grid-item is-s-24 is-xxl-12 mb-4',
        'image-size'        => 'col12-16to9',
      )
    );

    // Insert embed placeholder after first 2 posts
    if ( $post_count === 2 ) {
      ?>
      <div class="grid-item is-s-24 mb-4">
      </div>
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
    <div class="grid-row mb-5">
      <div class="grid-item is-xxl-24">
        <?php get_template_part( 'partials/pagination' ); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
