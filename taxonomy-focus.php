<?php
get_header();

$term = $wp_query->get_queried_object();

$splash_image_id = get_term_meta($term->term_id, '_nm_focus_splash_id', true);
$splash_image_caption = !empty($splash_image_id) ? wp_get_attachment_caption($splash_image_id) : false;

$credits = get_term_meta($term->term_id, '_nm_focus_credits', true);
?>
<main id="main-content">
  <section id="posts" class="container">

    <div class="flex-grid-row margin-top-small margin-bottom-mid">
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4">
        <h4 class="margin-bottom-micro">Focus</h4>
        <div class="only-desktop">
          <h1 class="margin-bottom-micro"><?php single_cat_title(); ?></h1>
          <div class="font-size-h2">
            <?php echo category_description(); ?>
          </div>
        </div>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-8">
        <?php
          if ($splash_image_id) {
            echo wp_get_attachment_image($splash_image_id, 'col18-16to9', false, array('class' => 'focus-archive__splash'));

            if ($splash_image_caption) {
          ?>
          <div class="font-smaller">
            <?php echo $splash_image_caption; ?>
          </div>
          <?php
            }
          }
        ?>
        <div class="only-mobile">
          <h1 class="margin-top-micro margin-bottom-micro"><?php single_cat_title(); ?></h1>
          <div class="font-size-h3">
            <?php echo category_description(); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="flex-grid-row margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    $content_type = get_the_top_category(get_the_ID());

    switch ($content_type->category_nicename) {
      case 'video':
        get_template_part('partials/post-layouts/flex-video-embed-post', null, array('grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xl-6 flex-item-xxl-4 margin-bottom-basic'));
        break;
      default:
        get_template_part('partials/post-layouts/flex-post', null, array('grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xl-6 flex-item-xxl-4 margin-bottom-basic'));
    }
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic font-smaller">
      <div class="flex-grid-item flex-item-s-12 flex-item-m-6 flex-item-xxl-4">
        <?php
          if (!empty($credits)) {
            echo apply_filters('the_content', $credits);
          }
        ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
