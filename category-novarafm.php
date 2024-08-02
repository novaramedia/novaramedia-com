<?php
get_header();

$category = get_category(get_query_var('cat'));

$podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);
$podcast_copy = !empty($podcast_copy_override) ? $podcast_copy_override : 'Subscribe to the podcast';
$podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : '';

// Mock copy here. Should we hardcode this or should it be in some options panel?
$credits = [
  array(
    'name' => 'Chal Ravens',
    'role' => 'Executive Producer',
    'bio' => 'The boss, la jefa, the big cheese. Chal is the executive producer of Novara FM and the rest of the Novara Media audio output.'
  ),
  array(
    'name' => 'Richard Hames',
    'role' => 'Producer & Host',
    'bio' => 'Doomplanner & internet shapecel Richard Hames is the producer of Novara FM and host of the monthly show Tech Against Tyranny (says GPT3).'
  ),
  array(
    'name' => 'James Butler',
    'role' => 'Host',
    'bio' => 'James is a writer, broadcaster and founder of Novara Media. From 2011 up until 2019 he hosted the majority of Novara FM episodes and therefore you will find him all throughout the archive.'
  ),
];

// renders the small cross in chosen color. This probably should become a combined function with the large cross renderer
function render_small_cross($color = 'black') {
  echo '<svg width="92" height="38" viewBox="0 0 92 38" fill="none" xmlns="http://www.w3.org/2000/svg" class="novara-fm-archive__cross novara-fm-archive__cross--' . $color . '">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M92 19L1.12875e-07 19L0 18L92 18L92 19Z" fill="#0E0E0E"/>
    <path fill-rule="evenodd" clip-rule="evenodd" d="M46 38L46 -1.80547e-08L47 0L47 38L46 38Z" fill="#0E0E0E"/>
    </svg>';
}

function render_large_cross($color = 'black') {
  echo '<svg width="92" height="483" viewBox="0 0 92 483" fill="none" xmlns="http://www.w3.org/2000/svg" class="novara-fm-archive__cross novara-fm-archive__cross--' . $color . '">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M92 241.79L8.89111e-09 241.79L0 240.79L92 240.79L92 241.79Z" fill="#5FCC00"/>
    <path fill-rule="evenodd" clip-rule="evenodd" d="M45.9999 483L46 0.579834L47 0.579834L46.9999 483L45.9999 483Z" fill="#5FCC00"/>
    </svg>';
}

$episode_block_posts_number = 9; // how many episodes to show in the sidescroll blocks

// renders a carousel of episodes
function render_episode_block($posts) {
?>
<div class="novara-fm-archive__archive-block background-white ui-rounded-box ux-carousel ux-carousel--autoplay">
  <div class="swiper">
    <div class="swiper-button-prev swiper-button-prev--disabled ui-rounded-box"><span class="only-desktop ui-chevron ui-chevron--left"></span></div>
    <div class="swiper-button-next ui-rounded-box"><span class="only-desktop ui-chevron ui-chevron--right"></span></div>
    <div class="swiper-wrapper">
      <?php
        foreach ($posts as $post) {
          $post_id = $post->ID;
      ?>
        <div class="swiper-slide">
          <a href="<?php echo get_permalink($post_id); ?>">
            <?php render_thumbnail($post_id, 'col12-16to9', array(
              'class' => 'ui-rounded-image'
            )); ?>
            <h2 class="fs-4-sans font-bold mt-1 mb-1"><?php echo get_the_title($post_id); ?></h2>
            <h3 class="fs-3-sans font-bold mb-1"><?php render_standfirst($post_id); ?></h3>
            <p class="fs-3-sans"><?php render_short_description($post_id); ?></p>
          </a>
        </div>
      <?php
        }
      ?>
    </div>
  </div>
</div>
<?php
}

// renders the follow links for the podcast. How hardcoded should these links be. Likely needs an option for button style classes
function render_podcast_links($podcast_url, $button_color = 'white') {
?>
  <ul class="u-inline-list fs-3-sans">
    <li class="mb-1"><a class="ui-button ui-button--<?php echo $button_color; ?> ui-button--small">Apple Podcasts</a></li>
    <li class="mb-1"><a class="ui-button ui-button--<?php echo $button_color; ?> ui-button--small">Spotify</a></li>
    <li class="mb-1"><a class="ui-button ui-button--<?php echo $button_color; ?> ui-button--small">Pocket Casts</a></li>
    <li><a class="ui-button ui-button--<?php echo $button_color; ?> ui-button--small" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow">Everywhere else</a></li>
  </ul>
<?php
}
?>
<main id="main-content" class="category-archive novara-fm-archive">
  <div class="novara-fm-archive__splash-video-container">
    <video class="novara-fm-archive__splash-video" autoplay loop muted>
      <source src="<?php echo get_template_directory_uri(); ?>/dist/video/novara-fm-splash.webm" type="video/webm" />
      <source src="<?php echo get_template_directory_uri(); ?>/dist/video/novara-fm-splash.mp4" type="video/mp4" />
    </video>
    <div class="container">
      <div class="novara-fm-archive__header pt-6 pb-7">
        <?php echo nm_get_file('/dist/img/products/novara-fm/novarafm-wordmark.svg'); ?>
      </div>
    </div>
  </div>

  <div class="background-green">
    <div class="container pt-5 pb-5 fs-5-sans font-bold">
      <div class="grid-row">
        <div class="is-xxl-2">
          <?php echo render_small_cross('black'); ?>
        </div>
        <div class="grid-item is-xxl-20 text-align-center">
          Novara FM is a podcast about the ideas, <br class="only-desktop" />people and movements that wield power in our lives.
        </div>
        <div class="is-xxl-2 text-align-right">
          <?php echo render_small_cross('black'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="background-black">
  <?php
    $lastest_fm = get_posts(array(
      'category' => $category->term_id,
      'posts_per_page' => ($episode_block_posts_number + 1), // 1 for latest, x for recent
    ));

    if ($lastest_fm) {
      $post_id = $lastest_fm[0]->ID;
      $meta = get_post_meta($post_id);
  ?>
    <section class="container pt-7 pb-8 font-color-white">
      <div class="grid-row">
        <div class="is-xxl-2">
          <?php echo render_large_cross('green'); ?>
        </div>
        <div class="grid-item offset-xxl-1 is-xxl-18">
          <div class="grid-row grid--nested">
            <div class="grid-item is-s-24 is-xxl-12 mb-s-5">
              <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
                <h4 class="fs-3-sans font-bold font-uppercase mb-2">Latest Episode</h4>
                <h2 class="fs-8 mb-3"><?php echo get_the_title($post_id); ?></h2>
              </a>
              <p class="fs-4-serif mb-4"><?php render_short_description($post_id); ?></p>

              <?php
                if (!empty($meta['_cmb_sc'][0])) {
              ?>
              <iframe width="100%" height="20" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>&color=%235fcc00&inverse=true&auto_play=false&show_user=true"></iframe>
              <?php
                if (!empty($meta['_cmb_is_resonance']) && $meta['_cmb_is_resonance'][0]) {
              ?>
                <div class="fs-1 mt-1">
                  <a target=_blank rel="nofollow" href="http://resonancefm.com/">powered by: Resonance FM</a>
                </div>
              <?php
                }
              }
              ?>
              <h4 class="fs-6 mt-4 mb-3">Listen now on:</h4>
              <?php render_podcast_links($podcast_url); ?>
            </div>
            <div class="grid-item is-s-24 is-xxl-12">
              <a href="<?php echo get_permalink($post_id); ?>" class="ui-hover">
                <?php render_thumbnail($post_id, '12-square', array(
                  'class' => 'mt-1'
                )); ?>
              </a>
            </div>
          </div>
        </div>
        <div class="offset-xxl-1 is-xxl-2 text-align-right">
          <?php echo render_large_cross('green'); ?>
        </div>
      </div>
    </section>
    <?php
      }
    ?>

    <section class="container">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <h4 class="fs-3-sans font-bold font-uppercase font-color-white mb-4">Recent Episodes Right Now</h4>
          <?php
            array_shift($lastest_fm); // Remove first episode as already shown above
            render_episode_block($lastest_fm); ?>

          <h4 class="fs-3-sans font-bold font-uppercase font-color-white mt-4 mb-4">Climate Breakdown Apocalypse</h4>
          <?php
            render_episode_block(get_posts(array(
              'category' => $category->term_id,
              'posts_per_page' => $episode_block_posts_number,
              'tax_query' => array(
                array(
                  'taxonomy' => 'section',
                  'field' => 'slug',
                  'terms' => 'climate'
                )
              )
            ))); ?>

          <h4 class="fs-3-sans font-bold font-uppercase font-color-white mt-4 mb-4">Police Freaking Policing</h4>
          <?php
            render_episode_block(get_posts(array(
              'category' => $category->term_id,
              'posts_per_page' => $episode_block_posts_number,
              'tax_query' => array(
                array(
                  'taxonomy' => 'section',
                  'field' => 'slug',
                  'terms' => 'policing'
                )
              )
            ))); ?>
        </div>

      </div>
    </section>

    <section class="container pt-9 pb-9">
      <div class="grid-row">
        <div class="grid-item is-xxl-2">
          <div class="layout-split-vertical">
            <?php
              echo render_small_cross('green');
              echo render_small_cross('green');
            ?>
          </div>
        </div>
        <div class="grid-item offset-s-0 is-s-20 offset-xxl-1 is-xxl-18">
          <svg class="novara-fm-archive__split-logotype" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" id="Layer_1" x="0" y="0" style="enable-background:new 0 0 485 57.4" version="1.1" viewBox="0 0 485 57.4"><style>.st0{fill:#5fcc00}</style><path d="M0 57.4V22.6h15.5v8.3h.3c2.1-3.4 4.8-5.9 8-7.5C27.2 21.8 30.6 21 34 21c4.4 0 7.9.6 10.7 1.8 2.8 1.1 5.1 2.8 6.7 4.9 1.6 2.1 2.7 4.6 3.3 7.7.7 3 1 6.3 1 10v11.9H39.4v-8.9c0-4.9-.8-8.5-2.3-10.9-1.5-2.5-4.3-3.7-8.2-3.7-4.4 0-7.7 1.3-9.7 4-2 2.6-3 6.9-3 13v6.5H0zM484.3 57.4V42.3c0-3.8-.5-7.1-1.6-9.8-1.1-2.8-2.6-5-4.5-6.7-1.9-1.7-4.2-2.9-6.9-3.7-2.7-.8-5.6-1.1-8.7-1.1-4.1 0-7.7 1-10.8 3-3 2-5.4 4.3-7.1 6.9-1.6-3.7-4-6.2-7.1-7.7-3.1-1.5-6.5-2.2-10.2-2.2-3.9 0-7.4.8-10.5 2.5-3 1.7-5.6 4.1-7.7 7.1h-.2v-8h-15.4v34.8h16.3v-9.8c0-2.9.4-5.2 1.3-7 .8-1.8 1.8-3.2 3-4.1 1.2-1 2.5-1.6 3.7-2 1.3-.4 2.3-.6 3.1-.6 2.6 0 4.6.5 5.9 1.4 1.4.8 2.3 2 2.9 3.4.6 1.5 1 3.1 1 4.8.1 1.7.1 3.4.1 5.2v8.7h16.3V49c0-1.8.1-3.6.3-5.4.3-1.8.8-3.4 1.6-4.8.8-1.5 2-2.6 3.3-3.4 1.5-.9 3.3-1.4 5.6-1.4s4.1.4 5.4 1.1c1.4.8 2.4 1.8 3.1 3.1.7 1.3 1.1 2.8 1.3 4.6.2 1.8.2 3.6.2 5.6v9h16.3zM381.6 57.4V33.6h11.3v-11h-11.3V19c0-2.5.5-4.2 1.4-5.2 1-1.1 2.6-1.6 4.8-1.6 2.1 0 4.1.1 6 .3V.5c-1.4-.1-2.8-.2-4.3-.2-1.5-.2-2.9-.3-4.4-.3-6.7 0-11.6 1.7-14.9 5.1-3.3 3.4-4.9 7.7-4.9 13v4.6h-9.8v10.9h9.8v23.8h16.3zM328.8 57.4V37.9c0-3.6-.8-6.5-2.4-8.6-1.6-2.2-3.7-3.9-6.2-5.2-2.5-1.2-5.3-2-8.4-2.4-3.1-.5-6.1-.7-9.1-.7-3.3 0-6.6.3-9.9 1-3.2.6-6.1 1.7-8.7 3.2-2.6 1.5-4.8 3.6-6.4 6.1-1.7 2.5-2.6 5.7-2.9 9.5h16.3c.3-3.2 1.4-5.5 3.2-6.9 1.8-1.4 4.4-2.1 7.6-2.1 1.5 0 2.8.1 4 .3 1.3.2 2.4.5 3.3 1.1 1 .5 1.8 1.3 2.3 2.4.6 1 .9 2.4.9 4.1.1 1.7-.4 3-1.5 3.9-1.1.8-2.5 1.5-4.4 2-1.8.5-3.9.8-6.3 1-2.4.2-4.8.5-7.2.9s-4.9.9-7.4 1.6c-2.4.6-4.5 1.6-6.4 2.9-1.8 1.3-3.3 3-4.5 5.1H295.1c1.2-.4 2.5-.7 3.7-.9l3.9-.6c1.3-.2 2.5-.3 3.7-.6 1.2-.2 2.3-.5 3.3-.8 1.1-.4 2-.9 2.6-1.5v4.4h16.5zM252.8 57.4v-2.1c0-2.7.3-5.2.8-7.5s1.4-4.3 2.6-6c1.3-1.8 3-3.1 5.1-4.1 2.1-1 4.6-1.5 7.6-1.5 1 0 2 .1 3.1.2s2 .2 2.8.3V21.6c-1.3-.4-2.5-.6-3.6-.6-2.1 0-4.1.3-6 .9-1.9.6-3.7 1.5-5.4 2.6-1.7 1.1-3.2 2.4-4.5 4-1.3 1.5-2.3 3.2-3.1 5.1h-.2v-11h-15.5v34.8h16.3zM230.8 57.4V37.9c0-3.6-.8-6.5-2.4-8.6-1.6-2.2-3.7-3.9-6.2-5.2-2.5-1.2-5.3-2-8.4-2.4-3.1-.5-6.1-.7-9.1-.7-3.3 0-6.6.3-9.9 1-3.2.6-6.1 1.7-8.7 3.2-2.6 1.5-4.8 3.6-6.4 6.1-1.7 2.5-2.6 5.7-2.9 9.5h16.3c.3-3.2 1.4-5.5 3.2-6.9 1.8-1.4 4.4-2.1 7.6-2.1 1.5 0 2.8.1 4 .3 1.3.2 2.4.5 3.3 1.1 1 .5 1.8 1.3 2.3 2.4.6 1 .9 2.4.9 4.1.1 1.7-.4 3-1.5 3.9-1.1.8-2.5 1.5-4.4 2-1.8.5-3.9.8-6.3 1-2.4.2-4.8.5-7.2.9-2.5.4-4.9.9-7.4 1.6-2.4.6-4.5 1.6-6.4 2.9-1.8 1.3-3.3 3-4.5 5.1H197.1c1.2-.4 2.5-.7 3.7-.9l3.9-.6c1.3-.2 2.5-.3 3.7-.6 1.2-.2 2.3-.5 3.3-.8 1.1-.4 2-.9 2.6-1.5v4.4h16.5zM165.8 57.4l11.8-34.8h-16.2l-10.7 34.8h15.1zM146.8 57.4l-10.7-34.8H119l11.9 34.8h15.9zM120.6 57.4c.2-1.6.3-3.3.3-5 0-4.8-.7-9-2.2-12.9-1.5-3.9-3.5-7.2-6.2-9.9-2.7-2.8-5.9-4.9-9.8-6.3-3.8-1.5-8-2.3-12.6-2.3-4.7 0-8.9.8-12.6 2.3-3.8 1.5-7 3.6-9.7 6.3-2.7 2.7-4.8 6-6.2 9.9-1.5 3.8-2.2 8.1-2.2 12.9 0 1.7.1 3.4.3 5h16.4c-.2-1.6-.3-3.3-.3-5 0-2.4.2-4.7.7-7 .5-2.3 1.2-4.3 2.3-6.1 1.1-1.8 2.6-3.2 4.5-4.3 1.8-1.1 4.1-1.7 6.9-1.7s5.1.6 6.9 1.7c1.9 1.1 3.4 2.5 4.5 4.3 1.1 1.8 2 3.8 2.4 6.1.5 2.3.7 4.6.7 7 0 1.7-.1 3.3-.3 5h16.2z" class="st0"/></svg>
          <div class="background-white ui-rounded-box pt-5 pb-5 pl-6 pr-6 pt-s-4 pb-s-4 pl-s-4 pr-s-4">
            <h2 class="fs-8 fs-s-7 mb-4">About the show</h2>
            <p class="fs-6 fs-s-5-sans mb-4">Novara Media’s flagship podcast is about the ideas that shape our past, present and future. With a desire to change the world—and ourselves along the way—Novara FM interrogates the people, ideologies and movements that wield power in our lives, from politics and culture to technology and the environment.</p>
            <h4 class="fs-6 fs-s-4-sans mb-3">Listen now on:</h4>
            <?php render_podcast_links($podcast_url, 'green'); ?>
          </div>
          <svg class="novara-fm-archive__split-logotype" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 485 39"><path fill="#5FCC00" fill-rule="evenodd" d="M306.437.397h10.668c-.014 4.408-.07 8.386-.166 11.932-.113 4.163-.17 8.723-.17 13.681 0 1.476.246 2.65.738 3.52.53.87 1.325 1.608 2.384 2.214.568.34 1.457.53 2.669.568 1.248.038 2.516.056 3.803.056v3.634c-1.779.643-3.349 1.154-4.712 1.533-1.324.416-2.838.624-4.541.624-2.952 0-5.318-.681-7.097-2.044-1.741-1.4-2.857-3.425-3.349-6.074h-.341c-2.46 2.725-5.109 4.806-7.947 6.245-2.801 1.438-6.188 2.157-10.162 2.157-4.201 0-7.664-1.287-10.389-3.86-2.687-2.574-4.031-5.942-4.031-10.105 0-2.158.303-4.088.909-5.791a13.831 13.831 0 0 1 2.724-4.598c.947-1.136 2.196-2.139 3.747-3.01 1.552-.908 3.009-1.627 4.372-2.156 1.703-.644 5.147-1.836 10.332-3.577 5.223-1.74 8.742-3.103 10.559-4.087V.397Zm-8.345 8.412c2.422-.908 5.241-1.836 8.458-2.782l-.34 19.586c-1.401 1.779-3.123 3.33-5.166 4.655-2.044 1.287-4.409 1.93-7.096 1.93-2.536 0-4.637-.738-6.302-2.214-1.627-1.476-2.441-3.785-2.441-6.926 0-2.422.549-4.503 1.646-6.244 1.098-1.78 2.593-3.312 4.485-4.599a34.26 34.26 0 0 1 6.756-3.406Z" clip-rule="evenodd"/><path fill="#5FCC00" d="M237.373.397h10.559v26.522c0 1.324.246 2.403.738 3.235.53.833 1.344 1.458 2.441 1.874.946.378 2.158.662 3.634.851 1.513.19 2.819.322 3.917.398v3.747h-29.521v-3.747c.871-.076 1.76-.152 2.669-.227.946-.076 1.759-.227 2.441-.454 1.059-.341 1.835-.928 2.327-1.76.53-.87.795-1.987.795-3.35V.396Z"/><path fill="#5FCC00" fill-rule="evenodd" d="M208.302.397h10.669a514.478 514.478 0 0 1-.166 11.932 502.652 502.652 0 0 0-.171 13.681c0 1.476.246 2.65.738 3.52.53.87 1.325 1.608 2.385 2.214.567.34 1.457.53 2.668.568 1.249.038 2.517.056 3.803.056v3.634c-1.778.643-3.349 1.154-4.711 1.533-1.325.416-2.839.624-4.542.624-2.952 0-5.317-.681-7.096-2.044-1.741-1.4-2.858-3.425-3.35-6.074h-.34c-2.46 2.725-5.11 4.806-7.948 6.245-2.801 1.438-6.188 2.157-10.162 2.157-4.201 0-7.664-1.287-10.389-3.86-2.687-2.574-4.03-5.942-4.03-10.105 0-2.158.302-4.088.908-5.791a13.834 13.834 0 0 1 2.725-4.598c.946-1.136 2.195-2.139 3.747-3.01 1.551-.908 3.008-1.627 4.371-2.156 1.703-.644 5.147-1.836 10.332-3.577 5.223-1.74 8.743-3.103 10.559-4.087V.397Zm-8.345 8.412c2.422-.908 5.242-1.836 8.459-2.782l-.341 19.586c-1.4 1.779-3.122 3.33-5.166 4.655-2.044 1.287-4.409 1.93-7.096 1.93-2.536 0-4.636-.738-6.302-2.214-1.627-1.476-2.441-3.785-2.441-6.926 0-2.422.549-4.503 1.647-6.244 1.097-1.78 2.592-3.312 4.484-4.599a34.26 34.26 0 0 1 6.756-3.406Z" clip-rule="evenodd"/><path fill="#5FCC00" d="M159.791.397h4.773a2493.18 2493.18 0 0 0-2.931 6.766 2390.075 2390.075 0 0 0-7.437 17.314c-.757 1.741-1.571 3.804-2.441 6.188a371.016 371.016 0 0 0-2.328 6.87h-4.428a3021.044 3021.044 0 0 0-9.934-24.298 2570.46 2570.46 0 0 0-5.338-12.84h11.309c.734 1.796 1.525 3.73 2.374 5.8a2233.237 2233.237 0 0 0 6.926 16.748c1.362-3.255 3.065-7.305 5.109-12.149a602.135 602.135 0 0 0 4.346-10.4ZM64.924.397c-.665 2.747-.997 5.702-.997 8.866 0 4.542.72 8.648 2.158 12.32 1.476 3.633 3.444 6.717 5.904 9.253 2.498 2.573 5.355 4.541 8.572 5.904 3.217 1.324 6.604 1.987 10.162 1.987 4.276 0 8.137-.738 11.581-2.214 3.444-1.476 6.472-3.728 9.083-6.756 2.309-2.65 4.087-5.866 5.336-9.65 1.249-3.823 1.874-7.703 1.874-11.639 0-2.88-.275-5.57-.825-8.071H106.13c.477 2.855.715 5.584.715 8.185 0 8.061-1.381 14.382-4.144 18.961-2.763 4.58-6.51 6.87-11.24 6.87-2.877 0-5.299-.682-7.267-2.044-1.93-1.4-3.538-3.312-4.825-5.734-1.287-2.422-2.233-5.166-2.839-8.232-.567-3.103-.851-6.472-.851-10.105 0-2.553.21-5.187.63-7.901H64.924ZM8.232.397v27.09c0 1.362-.265 2.478-.795 3.349-.492.832-1.268 1.419-2.328 1.76-.681.227-1.495.378-2.44.454-.91.075-1.799.151-2.669.227v3.747h27.42v-3.747a24.208 24.208 0 0 1-2.895-.398c-.909-.189-1.76-.473-2.555-.851-1.06-.454-1.854-1.098-2.384-1.93-.53-.833-.795-1.893-.795-3.18V.398H8.23ZM42.975.397h10.73v26.635c0 1.325.226 2.403.68 3.236.455.795 1.23 1.42 2.328 1.873.908.379 1.684.644 2.328.795.68.152 1.608.265 2.781.34v3.748h-27.42v-3.747c.871-.076 1.817-.152 2.839-.227 1.06-.076 1.93-.227 2.611-.454 1.06-.341 1.836-.928 2.328-1.76.53-.87.795-1.987.795-3.35V.396ZM363.323.397h10.559v26.522c0 1.324.246 2.403.738 3.235.53.833 1.344 1.458 2.441 1.874.909.34 2.12.624 3.634.851 1.514.19 2.819.322 3.917.398v3.747h-29.521v-3.747c.871-.076 1.76-.152 2.669-.227.946-.076 1.76-.227 2.441-.454 1.059-.341 1.835-.928 2.327-1.76.53-.87.795-1.987.795-3.35V.396ZM397.298.397v27.09c0 1.362-.265 2.478-.795 3.349-.492.832-1.268 1.419-2.328 1.76-.681.227-1.494.378-2.441.454-.908.075-1.797.151-2.668.227v3.747h27.193v-3.747a18.9 18.9 0 0 1-2.782-.398 11.71 11.71 0 0 1-2.441-.851c-1.06-.454-1.854-1.117-2.384-1.987-.53-.87-.795-1.95-.795-3.236V.397h-10.559ZM431.473.397v27.09c0 1.362-.265 2.478-.795 3.349-.492.832-1.267 1.419-2.327 1.76-.681.227-1.438.378-2.271.454-.795.075-1.627.151-2.498.227v3.747h26.739v-3.747c-1.136-.076-2.063-.19-2.782-.34a24.422 24.422 0 0 1-2.327-.796c-1.098-.454-1.874-1.097-2.328-1.93-.454-.87-.681-1.968-.681-3.292V.397h-10.73ZM465.762.397h10.73v26.522c0 1.324.227 2.422.681 3.292.454.833 1.23 1.476 2.328 1.93.87.341 1.722.606 2.554.795.871.152 1.836.265 2.896.34v3.748h-27.307v-3.747l2.612-.227c.908-.076 1.703-.227 2.384-.454 1.06-.341 1.835-.928 2.327-1.76.53-.87.795-1.987.795-3.35V.396Z"/></svg>
        </div>
        <div class="grid-item offset-s-0 offset-xxl-1 is-xxl-2 text-align-right">
          <div class="layout-split-vertical">
            <?php
              echo render_small_cross('green');
              echo render_small_cross('green');
            ?>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php
    get_template_part('partials/support-section');
  ?>

  <div class="background-green">
    <section class="container pt-6 pb-7">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <h3 class="fs-3-sans font-bold font-uppercase mb-4">Credits</h3>
        </div>
        <?php foreach($credits as $credit) {
      ?>
        <div class="grid-item is-s-12 is-xxl-6 mb-5">
          <h4 class="fs-5-sans mb-1"><?php echo $credit['name']; ?></h4>
          <h6 class="fs-2 font-bold font-uppercase mb-1"><?php echo $credit['role']; ?></h6>
          <p class="fs-3-sans"><?php echo $credit['bio']; ?></p>
        </div>
      <?php
        }
      ?>
      </div>
    </section>
  </div>
</main>
<?php
get_footer();
?>
