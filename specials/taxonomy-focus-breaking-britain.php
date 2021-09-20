<?php
get_header();

$term = $wp_query->get_queried_object();

$quotes = get_term_meta($term->term_id, '_nm_focus_quotes', true);
$credits = get_term_meta($term->term_id, '_nm_focus_credits', true);
?>

<div style="position: absolute; width: 100vw; height: 100vh; overflow: hidden; z-index: 10; pointer-events: none; ">
  <div style="position: absolute; top: 0; left: -400px; animation: breakingbars 13131ms ease-in-out 111ms infinite normal forwards;">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-1.svg'); ?>
  </div>
  <div style="position: absolute; top: -250px; left: 27vw; animation: breakingbars 22222ms ease-in-out 707ms infinite normal forwards;">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-2.svg'); ?>
  </div>
  <div style="position: absolute; top: -50px; right: -250px; animation: breakingbars 11111ms ease-in-out 420ms infinite normal forwards;">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-3.svg'); ?>
  </div>
</div>

<style type="text/css">
  @keyframes breakingbars {
  0% {
    transform: translate(0) rotate(0deg);
  }

  20% {
    transform: translate(-10px, 10px) rotate(-2deg);
  }

  40% {
    transform: translate(-10px, -10px);
  }

  60% {
    transform: translate(10px, 10px) rotate(2deg);
  }

  80% {
    transform: translate(10px, -10px);
  }

  100% {
    transform: translate(0) rotate(0deg);
  }
}
</style>

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
      <div class="flex-grid-item flex-item-s-12 flex-item-xl-6 flex-item-xxl-4 margin-bottom-basic">
        <?php
          get_template_part('partials/components/quote', null, $quotes[0]);
        ?>
      </div>
      
      
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    
    $content_type = get_the_top_category(get_the_ID());
    
    switch ($content_type->category_nicename) {
      case 'video':
        get_template_part('partials/post-layouts/flex-video-embed-post', null, array(
          'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xl-6 flex-item-xxl-4 margin-bottom-basic'
        ));  
        break;
      default:
        get_template_part('partials/post-layouts/flex-post', null, array(
          'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xl-6 flex-item-xxl-4 margin-bottom-basic'
        ));  
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