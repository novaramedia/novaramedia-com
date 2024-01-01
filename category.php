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

$button_grid_item_classes = $is_one_button ? 'grid-item offset-s-0 offset-l-12 is-s-12 is-l-12 offset-xxl-6 is-xxl-6' : 'grid-item is-s-12 is-l-12 is-xxl-6';

if ($category->slug === 'video') {
  $button_grid_item_classes = 'mobile-margin-top-tiny grid-item is-s-24 offset-l-0 is-l-12 offset-xxl-4 flex-item-xxl-4';
}
?>
<main id="main-content" class="category-archive">
  <section id="posts" class="container mt-3">
    <div class="grid-row mb-4">
      <?php
        if (in_array($category->slug, array('articles', 'audio', 'video'))) {
        ?>
      <div class="grid-item is-s-24 flex-item-xxl-6">
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
      <div class="grid-item is-s-24 is-l-12 is-xxl-6">
        <?php
          if (get_term_meta($category->term_id, '_nm_category_logo_id', true)) {
            $logo_id = get_term_meta($category->term_id, '_nm_category_logo_id', true);

            echo wp_get_attachment_image($logo_id, 'col12', false, array('class' => 'category-archive__logo'));
          } else {
        ?>
        <h4 class="fs-4-sans"><a href="<?php echo get_category_link($category->term_id); ?>"><?php echo $category->name; ?></a></h4>
      <?php
        }
      ?>
      </div>
      <div class="grid-item is-s-24 is-l-12 is-xxl-6 text-paragraph-breaks">
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
      <div class="grid-row mb-5 only-desktop">
        <div class="grid-item is-xxl-16">
          <?php
          $meta = get_post_meta($post->ID);
          if (!empty($meta['_cmb_utube'])) {
          ?>
          <div class="u-video-embed-container">
            <iframe class="youtube-player lazyload" data-src="<?php echo generate_youtube_embed_url($meta['_cmb_utube'][0]); ?>" frameborder="0" allowfullscreen></iframe>
          </div>
          <a href="<?php the_permalink(); ?>">
            <h6 class="js-fix-widows mt-2 fs-7"><?php the_title(); ?></h6>
            <h5 class="fs-6 mt-2">
              <?php render_standfirst($post->ID); ?>
            </h5>
          </a>
          <?php
          } else {
            echo 'Someone messed up :/';
          }
          ?>
        </div>
        <div class="grid-item is-xxl-8">
          <div class="grid-row grid--nested-tight">
          <?php
          if (have_posts()) {
            while(have_posts() && $i < 6) {
              the_post();
          ?>
          <div class="grid-item grid-item--tight is-xxl-12 mb-5">
            <a href="<?php the_permalink(); ?>">
              <div class="layout-thumbnail-frame">
                <div class="layout-thumbnail-frame__inner mt-1 ml-1">
                  <?php render_post_ui_tags($post->ID, false, true, true); ?>
                </div>
                <?php render_thumbnail($post->ID, 'col24-16to9', array(
                  'class' => 'ui-rounded-image'
                )); ?>
              </div>
              <h6 class="js-fix-widows fs-3-sans font-bold mt-1"><?php the_title(); ?></h6>
            </a>
          </div>
          <?php
              $i++;
            }
          }
          ?>
          </div>
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
    <div class="grid-row mb-4">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'grid-item is-s-24 is-s-24 is-l-12 is-xxl-8 mb-4',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="grid-item is-s-24"><?php _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
} ?>
    </div>
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </div>
</main>
<?php
get_footer();
?>
