<?php
get_header();

$term = $wp_query->get_queried_object();

$credits = get_term_meta($term->term_id, '_nm_focus_credits', true);
?>

<!-- main content -->

<main id="main-content">

  <!-- main posts loop -->
  <section id="posts" class="container">    
    <div class="flex-grid-row margin-top-small margin-bottom-mid">
      <div class="flex-grid-item flex-item-s-12 flex-item-l-12 flex-item-xxl-12">
        <h4 class="margin-bottom-micro">Focus</h4>
        <h1 class="margin-bottom-micro font-color-blue-neon" style="font-size: 239px;font-weight: 700;line-height: 203px;letter-spacing: -0.04em;">Breaking Britain.</h1>
        <div class="font-size-h2">
          <?php echo category_description(); ?>
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
    <article class="flex-grid-item flex-item-s-12"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <?php
          if (!empty($credits)) {
            echo apply_filters('the_content', $credits);
          }
        ?>
      </div>
    </div>

  <!-- end posts -->
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>