<?php
get_header();
?>
<main id="main-content" class="category-archive novaralive-archive">
<?php
  $is_first_page = get_query_var( 'paged', 0 ) === 0 ? true : false;

  $video = get_category_by_slug('video');
  $category = get_category(get_query_var('cat'));

  $embed_id = !empty(get_term_meta($category->term_id, '_nm_novaralive_latest_youtube_id', true)) ? get_term_meta($category->term_id, '_nm_novaralive_latest_youtube_id', true) : false;

  $team_image_id = !empty(get_term_meta($category->term_id, '_nm_novaralive_team_image_id', true)) ? get_term_meta($category->term_id, '_nm_novaralive_team_image_id', true) : false;

  $podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);
  $youtube_copy_override = get_term_meta($category->term_id, '_nm_youtube_text', true);

  $podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : 'https://podfollow.com/novaramedia/view';

  if ($is_first_page) {
?>
<div class="background-black font-color-white">
  <section class="container pt-4">
    <div class="grid-row mb-4">
      <div class="grid-item is-s-14 is-l-12 is-xxl-6 mb-s-2">
        <h1 class="font-size-15 font-weight-bold">Novara Live</h1>
      </div>
      <div class="grid-item offset-s-0 is-s-12 offset-l-0 is-l-6 offset-xl-6 is-xl-6 offset-xxl-10 is-xxl-4">
        <a class="ui-button ui-button--white ui-button--small ui-button--fill-width ui-button--auto-height" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow">Subscribe to<br/>the podcast</a>
      </div>
      <div class="grid-item offset-s-0 is-s-12 is-l-6 is-xl-6 is-xxl-4">
        <a class="ui-button ui-button--red ui-button--small ui-button--fill-width ui-button--auto-height" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow">Subscribe to our<br/>YouTube channel</a>
      </div>
    </div>
    <div class="novara-live-archive__liveplayer grid-row">
      <div class="grid-item is-xxl-24">
        <div class="u-video-embed-container">
          <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url($embed_id, true); ?>" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
    </div>
    <div class="grid-row mt-4">
      <div class="grid-item is-s-24 is-m-18 is-l-12 is-xxl-10 pt-4 pt-s-0 font-size-11">
        <?php echo category_description(); ?>
      </div>
      <div class="grid-item offset-s-2 is-s-20 offset-m-6 is-m-14 offset-l-0 is-l-12 offset-xxl-4 is-xxl-10">
        <?php echo wp_get_attachment_image($team_image_id, 'gallery', false, array('class' => 'novara-live-archive__about-team-image u-display-block')); ?>
      </div>
    </div>
  </section>
</div>
<div class="background-yellow">
  <section class="container pt-4 pb-4">
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24">
        <h2 class="font-size-12 font-weight-bold">Missed the show? Catch up here:</h2>
      </div>
    </div>
    <div class="grid-row">
      <?php
        $i = 0;
        if (have_posts()) {
          while(have_posts() && $i < 4) {
            the_post();
        ?>
        <div class="grid-item is-s-12 is-xxl-6 mb-4">
          <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('col6-16to9'); ?>
            <h6 class="font-size-10 font-weight-semibold mt-1"><?php the_time('j F Y'); ?></h6>
            <h6 class="text-wrap-pretty font-size-11 font-size-S-10 font-weight-semibold mt-1"><?php the_title(); ?></h6>
          <a href="<?php the_permalink(); ?>">
        </div>
        <?php
          $i++;
          }
        }
      ?>
    </div>
  </section>
</div>
<?php get_template_part('partials/support-section'); ?>
<?php
  } // end if first page
?>
  <section id="posts" class="container mt-6 mt-s-5">
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold"><?php echo $is_first_page ? 'More Novara Live' : 'Novara Live'; ?></h4>
      </div>
    </div>
    <div class="grid-row mb-4">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/archive-post', null, array(
      'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="grid-row mb-5">
      <div class="grid-item is-s-24">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
