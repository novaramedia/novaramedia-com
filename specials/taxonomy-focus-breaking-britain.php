<?php
get_header();

$term = $wp_query->get_queried_object();

$quotes = get_term_meta($term->term_id, '_nm_focus_quotes', true);
$credits = get_term_meta($term->term_id, '_nm_focus_credits', true);
?>
<div class="breaking-britain__bars-container" style="position: absolute; width: 100%; height: 100vh; overflow: hidden; z-index: 10; pointer-events: none; ">
  <div class="breaking-britain__bar-1">
    <?php echo nm_get_file('/dist/img/specials/breaking-britain/focus-breakingbritain-line-1.svg'); ?>
  </div>
  <div class="breaking-britain__bar-2">
    <?php echo nm_get_file('/dist/img/specials/breaking-britain/focus-breakingbritain-line-2.svg'); ?>
  </div>
  <div class="breaking-britain__bar-3">
    <?php echo nm_get_file('/dist/img/specials/breaking-britain/focus-breakingbritain-line-3.svg'); ?>
  </div>
</div>
<style type="text/css">
  .breaking-britain__header {
    background-size: cover;
    background-position: bottom;
  }
  .breaking-britain__title {
    font-size: 159px;
    line-height: 0.84937238;
    letter-spacing: -0.02em;
  }
  .breaking-britain__header {
    background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/breaking-britain/focus-breakingbritain-background.png'; ?>);
  }
  .breaking-britain__bar-1 {
    position: absolute;
    top: 0;
    left: -400px;
    animation: breakingbars 13131ms ease-in-out 111ms infinite normal forwards;
  }
  .breaking-britain__bar-2 {
    position: absolute;
    top: -250px;
    left: 27vw;
    animation: breakingbars 22222ms ease-in-out 77ms infinite normal forwards;
  }
  .breaking-britain__bar-3 {
    position: absolute;
    top: -50px;
    right: -250px;
    animation: breakingbars 11111ms ease-in-out 42ms infinite normal forwards;
  }
  @media screen and (max-width: 1408px) {
    .breaking-britain__title {
      font-size: 106px;
      line-height: 0.8490566;
      letter-spacing: -0.025em;
    }
  }
  @media screen and (max-width: 1336px) {
    .breaking-britain__bars-container svg {
      transform: scale(.7)
    }
  }
  @media screen and (max-width: 1104px) {
    .breaking-britain__title {
      font-size: 106px;
      line-height: 0.8490566;
      letter-spacing: -0.025em;
    }
    .breaking-britain__bars-container svg {
      transform: scale(.5)
    }
  }
  @media screen and (max-width: 910px) {
    .breaking-britain__bars-container svg {
      transform: scale(.3)
    }
  }
  @media screen and (max-width: 759px) {
    .breaking-britain__title {
      font-size: 53px;
      line-height: 68px;
      letter-spacing: -0.02em;
    }
    .breaking-britain__bars-container svg {
      transform: scale(.2)
    }
    .breaking-britain__bar-1 {
      top: -100px;
    }
  }
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
<main id="main-content">
  <div class="breaking-britain__header">
    <div class="container">
      <div class="grid-row pt-4 mb-5" style="align-items: flex-end;">
        <div class="grid-item is-s-24 is-m-24 is-xl-12 is-xxl-14">
          <h4 class="mb-2 fs-3-sans font-uppercase font-bold">Focus</h4>
          <h1 class="breaking-britain__title mb-2 font-color-blue-neon" style="margin-bottom: 0;margin-left: -0.06em;">Breaking Britain.</h1>
        </div>
        <div class="grid-item is-s-24 is-m-18 is-xl-12 is-xxl-10">
          <div class="fs-5-sans font-bold font-color-blue-neon margin-top-small">
            <?php echo get_term_field( 'description', $term, null, $context = 'raw' ); // getting like this to avoid the filters that add <p> tags ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <section id="posts" class="container">
<?php
if( have_posts() ) {
  $i = 0;
  while( have_posts() ) {
    the_post();

    if ($i === 0) { ?>
    <div class="grid-row">
<?php }

    if ($i === 1) { ?>
      <div class="grid-item is-s-24 is-xxl-8 mb-4">
<?php }

    if ($i === 3) { ?>
      </div>
    </div>
    <div class="grid-row ">
<?php }

  if ($i === 4) { ?>
    <div class="grid-item is-s-24 is-xxl-8 mb-4 mt-s-5 mb-s-5 font-color-blue-neon">
      <?php
        if(isset($quotes[0])) {
          get_template_part('partials/components/quote', null, $quotes[0]);
        }
      ?>
    </div>
<?php }

  if ($i === 13) { ?>
    <div class="grid-item is-s-24 is-xxl-8 mb-4 mt-s-5 mb-s-5 font-color-blue-neon">
      <?php
        if(isset($quotes[1])) {
          get_template_part('partials/components/quote', null, $quotes[1]);
        }
      ?>
    </div>
<?php }

    if ($i === 5 || $i === 10) { ?>
    </div>
    <div class="grid-row margin-top-mid mb-5 mt-s-0 mb-s-0" style="justify-content: center;">
<?php }

    if ($i === 7|| $i === 12) { ?>
    </div>
    <div class="grid-row">
<?php }

    if ($i === 0) {
      $post_arguments = array(
        'grid-item-classes' => 'grid-item is-s-24 is-xxl-16 mb-4',
        'text-size' => 'large',
        'image-size' => 'col18-16to9',
      );
    } else if ($i === 1 || $i === 2) {
      $post_arguments = array(
        'grid-item-classes' => 'mb-4',
        'image-size' => 'col12-16to9',
      );
    } else if ($i === 5 || $i === 6) {
      $post_arguments = array(
        'grid-item-classes' => 'grid-item is-s-24 is-l-10 is-xxl-8 mb-4',
        'image-size' => 'col12-16to9',
      );
    } else {
      $post_arguments = array(
        'grid-item-classes' => 'grid-item is-s-24 is-xxl-8 mb-4',
        'image-size' => 'col12-16to9',
      );
    }

    get_template_part('partials/post-layouts/flex-post', null, $post_arguments);

    $i++;
  }
}
?>
    </div>

    <div class="grid-row mt-5 mb-5 font-smaller">
      <div class="grid-item is-s-24 flex-item-m-6 is-xxl-8">
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
