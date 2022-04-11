<?php
get_header();

$term = $wp_query->get_queried_object();

$quotes = get_term_meta($term->term_id, '_nm_focus_quotes', true);
$credits = get_term_meta($term->term_id, '_nm_focus_credits', true);
?>

<div class="breaking-britain__bars-container" style="position: absolute; width: 100%; height: 100vh; overflow: hidden; z-index: 10; pointer-events: none; ">
  <div class="breaking-britain__bar-1">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-1.svg'); ?>
  </div>
  <div class="breaking-britain__bar-2">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-2.svg'); ?>
  </div>
  <div class="breaking-britain__bar-3">
    <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-3.svg'); ?>
  </div>
</div>

<style type="text/css">
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

  @media screen and (max-width: 1336px) {
    .breaking-britain__bars-container svg {
      transform: scale(.7)
    }
  }

  @media screen and (max-width: 1104px) {
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

<!-- main content -->

<main id="main-content">

  <div style="background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-background.png'; ?>); background-size: cover; background-position: bottom;">
    <div class="container">
      <div class="flex-grid-row padding-top-small margin-bottom-mid" style="align-items: flex-end;">
        <div class="flex-grid-item flex-item-s-12 flex-item-m-12 flex-item-xl-6 flex-item-xxl-7">
          <h4 class="margin-bottom-micro">Focus</h4>
          <h1 class="margin-bottom-micro font-size-s-7 font-size-l-6 font-size-xl-7 font-size-8 font-color-blue-neon" style="margin-bottom: 0;margin-left: -0.06em;">Breaking Britain.</h1>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-m-9 flex-item-xl-6 flex-item-xxl-5">
          <div class="font-size-2 font-size-s-1 font-bold font-color-blue-neon margin-top-small">
            <?php echo get_term_field('description', $term, null, $context = 'raw'); // getting like this to avoid the filters that add <p> tags?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- main posts loop -->
  <section id="posts" class="container">
<?php
if (have_posts()) {
    $i = 0;
    while (have_posts()) {
        the_post();

        if ($i === 0) { ?>
    <div class="flex-grid-row">
<?php }

        if ($i === 1) { ?>
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-4 margin-bottom-basic">
<?php }

        if ($i === 3) { ?>
      </div>
    </div>
    <div class="flex-grid-row ">
<?php }

        if ($i === 4) { ?>
    <div class="flex-grid-item flex-item-s-12 flex-item-xxl-4 margin-bottom-basic mobile-margin-top-mid mobile-margin-bottom-mid font-color-blue-neon">
      <?php
        if (isset($quotes[0])) {
            get_template_part('partials/components/quote', null, $quotes[0]);
        }
      ?>
    </div>
<?php }

        if ($i === 13) { ?>
    <div class="flex-grid-item flex-item-s-12 flex-item-xxl-4 margin-bottom-basic mobile-margin-top-mid mobile-margin-bottom-mid font-color-blue-neon">
      <?php
        if (isset($quotes[1])) {
            get_template_part('partials/components/quote', null, $quotes[1]);
        }
      ?>
    </div>
<?php }

        if ($i === 5 || $i === 10) { ?>
    </div>
    <div class="flex-grid-row margin-top-mid margin-bottom-mid mobile-margin-top-none mobile-margin-bottom-none" style="justify-content: center;">
<?php }

        if ($i === 7|| $i === 12) { ?>
    </div>
    <div class="flex-grid-row">
<?php }

        if ($i === 0) {
            $post_arguments = array(
        'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xxl-8 margin-bottom-basic',
        'text-size' => 'large',
        'image-size' => 'col18-16to9',
      );
        } elseif ($i === 1 || $i === 2) {
            $post_arguments = array(
        'grid-item-classes' => 'margin-bottom-basic',
        'image-size' => 'col12-16to9',
      );
        } elseif ($i === 5 || $i === 6) {
            $post_arguments = array(
        'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-l-5 flex-item-xxl-4 margin-bottom-basic',
        'image-size' => 'col12-16to9',
      );
        } else {
            $post_arguments = array(
        'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-xxl-4 margin-bottom-basic',
        'image-size' => 'col12-16to9',
      );
        }

        get_template_part('partials/post-layouts/flex-post', null, $post_arguments);

        $i++;
    }
}
?>
    </div>

    <div class="flex-grid-row margin-top-mid margin-bottom-mid">
      <div class="flex-grid-item flex-item-s-12 flex-item-m-6 flex-item-xxl-4">
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
