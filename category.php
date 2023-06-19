<?php
get_header();

$video = get_category_by_slug('video');
$category = get_category(get_query_var('cat'));

$podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);
$youtube_copy_override = get_term_meta($category->term_id, '_nm_youtube_text', true);

$podcast_copy = !empty($podcast_copy_override) ? $podcast_copy_override : 'Subscribe to the podcast';
$youtube_copy = !empty($youtube_copy_override) ? $youtube_copy_override : 'Subscribe to our YouTube channel';

$is_video = $video->term_id === $category->term_id || $video->term_id === $category->category_parent;

$podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : false;

$is_one_button = $is_video === false || $podcast_url === false ? true : false;

$button_grid_item_classes = $is_one_button ? 'flex-grid-item flex-offset-s-0 flex-offset-l-6 flex-item-s-6 flex-item-l-6 flex-offset-xxl-3 flex-item-xxl-3' : 'flex-grid-item flex-item-s-6 flex-item-l-6 flex-item-xxl-3';

if ($category->slug === 'video') {
  $button_grid_item_classes = 'mobile-margin-top-tiny flex-grid-item flex-item-s-12 flex-offset-l-0 flex-item-l-6 flex-offset-xxl-2 flex-item-xxl-4';
}
?>
<main id="main-content" class="category-archive">
  <section id="posts" class="container margin-top-small">
    <div class="flex-grid-row margin-bottom-basic">
      <?php
        if (in_array($category->slug, array('articles', 'audio', 'video'))) {
        ?>
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">
        <span class="font-uppercase font-bold"><?php echo $category->name; ?></span> <?php
          wp_nav_menu(
            array(
              'theme_location' => $category->slug . '-archive-menu',
              'container' => false,
              'menu_class' => 'category-archive__submenu',
              'fallback_cb' => false,
            )
          );
        ?>
      </div>
      <?php
        } else { ?>
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
        <?php
          if (get_term_meta($category->term_id, '_nm_category_logo_id', true)) {
            $logo_id = get_term_meta($category->term_id, '_nm_category_logo_id', true);

            echo wp_get_attachment_image($logo_id, 'col12', false, array('class' => 'category-archive__logo'));
          } else {
        ?>
        <h4><a href="<?php echo get_category_link($category->term_id); ?>"><?php echo $category->name; ?></a></h4>
      <?php
        }
      ?>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-3">
        <?php echo category_description(); ?>
      </div>
        <?php } ?>
      <?php
        if ($is_video) {
      ?>
      <div class="<?php echo $button_grid_item_classes; ?>">
        <a class="nm-button nm-button--red" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow"><?php echo $youtube_copy; ?></a>
      </div>
      <?php
        }
      ?>
      <?php
        if (get_term_meta($category->term_id, '_nm_podcast_url', true)) {
          $podcast_url = get_term_meta($category->term_id, '_nm_podcast_url', true);
      ?>
      <div class="<?php echo $button_grid_item_classes; ?>">
        <a class="nm-button nm-button--white" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow"><?php echo $podcast_copy; ?></a>
      </div>
      <?php
        }
      ?>
    </div>
  </section>
<?php
  if ($is_video) {
      if (have_posts()) {
        $i = 0;
        the_post();
      ?>
    <div class="container">
      <div class="row margin-bottom-large only-desktop">
        <div class="col col16">
          <?php
          $meta = get_post_meta($post->ID);
          if (!empty($meta['_cmb_utube'])) {
          ?>
          <div class="u-video-embed-container">
            <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($meta['_cmb_utube'][0]); ?>"></iframe>
          </div>
          <a href="<?php the_permalink(); ?>">
            <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
          </a>
          <?php
          } else {
            echo 'Someone messed up :/';
          }
          ?>
        </div>
        <div class="col col4">
          <?php
          if (have_posts()) {
            while(have_posts() && $i < 6) {
              the_post();
          ?>
          <a href="<?php the_permalink(); ?>">
           <div class="single-tv-related-tv margin-bottom-small">
             <?php the_post_thumbnail('col4-16to9'); ?>
             <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
           </div>
         </a>
          <?php
            if ($i === 2) {
              echo '</div><div class="col col4">';
            }

            $i++;
            }
          }
          ?>
        </div>
      </div>
    </div>
    <?php
      }

      // reset pointer for have_posts
      global $wp_query;
      $wp_query->current_post = -1;
  }
?>
  <div class="container">
    <div class="flex-grid-row margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4 margin-bottom-basic',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="flex-grid-item flex-item-s-12"><?php _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </div>
</main>
<?php
get_footer();
?>
