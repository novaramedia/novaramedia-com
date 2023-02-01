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
  <section class="container padding-top-small">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-7 flex-item-l-6 flex-item-xxl-3 mobile-margin-bottom-tiny">
        <h1 class="font-size-4">Novara Live</h1>
      </div>
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-6 flex-offset-l-0 flex-item-l-3 flex-offset-xl-3 flex-item-xl-3 flex-offset-xxl-5 flex-item-xxl-2">
        <a class="nm-button nm-button--black font-size-s-0" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow">Subscribe to<br/>the podcast</a>
      </div>
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-6 flex-item-l-3 flex-item-xl-3 flex-item-xxl-2">
        <a class="nm-button nm-button--red font-size-s-0" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow">Subscribe to our<br/>YouTube channel</a>
      </div>
    </div>
    <div class="novara-live-archive__liveplayer flex-grid-row">
      <div class="flex-grid-item flex-item-xxl-12">
        <div class="u-video-embed-container">
          <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($embed_id, true); ?>"></iframe>
        </div>
      </div>
    </div>
    <div class="flex-grid-row margin-top-basic mobile-margin-top-small">
      <div class="flex-grid-item flex-item-s-12 flex-item-m-9 flex-item-l-6 flex-item-xxl-5 font-size-2 font-size-s-1 padding-top-small mobile-padding-top-none">
        <?php echo category_description(); ?>
      </div>
      <div class="flex-grid-item flex-offset-s-1 flex-item-s-10 flex-offset-m-3 flex-item-m-7 flex-offset-l-0 flex-item-l-6 flex-offset-xxl-2 flex-item-xxl-5">
        <?php echo wp_get_attachment_image($team_image_id, 'gallery', false, array('class' => 'novara-live-archive__about-team-image u-display-block')); ?>
      </div>
    </div>
  </section>
</div>
<div class="background-yellow">
  <section class="container padding-top-basic padding-bottom-small mobile-padding-top-small">
    <div class="flex-grid-row margin-bottom-basic mobile-margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <h2 class="font-size-3 font-size-s-2">Missed the show? Catch up here:</h2>
      </div>
    </div>
    <div class="flex-grid-row">
      <?php
        $i = 0;
        if (have_posts()) {
          while(have_posts() && $i < 4) {
            the_post();
        ?>
        <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-small">
          <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('col6-16to9'); ?>
            <h6 class="font-size-1 font-size-s-1 font-semibold margin-top-micro"><?php the_time('j F Y'); ?></h6>
            <h6 class="js-fix-widows font-size-2 font-size-s-1 font-semibold"><?php the_title(); ?></h6>
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
  <section id="posts" class="container <?php echo $is_first_page ? 'margin-top-basic mobile-margin-top-small' : 'margin-top-small'; ?>">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <h4><?php echo $is_first_page ? 'More Novara Live' : 'Novara Live'; ?></h4>
      </div>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4 margin-bottom-basic mobile-margin-bottom-tiny',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
