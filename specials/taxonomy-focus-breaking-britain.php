<?php
get_header();

$term = $wp_query->get_queried_object();

$quotes = get_term_meta($term->term_id, '_nm_focus_quotes', true);
$credits = get_term_meta($term->term_id, '_nm_focus_credits', true);
?>

<div style="position: absolute; width: 100%; height: 100vh; overflow: hidden; z-index: 10; pointer-events: none; ">
  <div style="position: absolute; top: 0; left: -400px; animation: breakingbars 13131ms ease-in-out 111ms infinite normal forwards;">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-1.svg'); ?>
  </div>
  <div style="position: absolute; top: -250px; left: 27vw; animation: breakingbars 22222ms ease-in-out 77ms infinite normal forwards;">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-2.svg'); ?>
  </div>
  <div style="position: absolute; top: -50px; right: -250px; animation: breakingbars 11111ms ease-in-out 42ms infinite normal forwards;">
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
    <div class="flex-grid-row margin-top-small margin-bottom-mid" style="align-items: flex-end;">
      <div class="flex-grid-item flex-item-s-12 flex-item-m-12 flex-item-xxl-7">
        <h4 class="margin-bottom-micro">Focus</h4>
        <h1 class="margin-bottom-micro font-color-blue-neon" style="font-size: 239px;font-weight: 700;line-height: 203px;letter-spacing: -0.04em;margin-bottom: 0;margin-left: -0.06em;">Breaking Britain.</h1>
      </div>        
      <div class="flex-grid-item flex-item-s-12 flex-item-m-9 flex-item-xxl-5">
        <div class="font-size-h2 font-color-blue-neon">
          <?php echo get_term_field( 'description', $term, null, $context = 'raw' ); // getting like this to avoid the filters that add <p> tags ?>
        </div>
      </div>
    </div>
 
<?php
if( have_posts() ) {
  $i = 0;
  while( have_posts() ) {
    the_post();
    
    if ($i === 0) { ?>
    <div class="flex-grid-row margin-bottom-basic">
<?php }

    if ($i === 1) { ?>
      <div class="flex-grid-item flex-item-s-12 flex-item-xl-4 flex-item-xxl-4 margin-bottom-basic">
<?php }
    
    if ($i === 3) { ?>
      </div>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
<?php }
  
  if ($i === 4) { ?>
    <div class="flex-grid-item flex-item-s-12 flex-item-xl-6 flex-item-xxl-4 margin-bottom-basic">
      <?php
        get_template_part('partials/components/quote', null, $quotes[0]);
      ?>
    </div>
<?php }   

    if ($i === 5 ) { ?>
    </div>
    <div class="flex-grid-row margin-top-mid margin-bottom-mid" style="justify-content: center;">
<?php }

    if ($i === 7) { ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
<?php }
    
    if ($i === 0) {
      $post_arguments = array(
        'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xl-8 flex-item-xxl-8 margin-bottom-basic',
        'text-size' => 'large'
      );
    } else if ($i === 1 || $i === 2) {
      $post_arguments = array(
        'grid-item-classes' => 'margin-bottom-basic'
      );
    } else {
      $post_arguments = array(
        'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xl-6 flex-item-xxl-4 margin-bottom-basic'
      );
    }
    
    get_template_part('partials/post-layouts/flex-post', null, $post_arguments);
    
    $i++;
  }
}
?>   
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