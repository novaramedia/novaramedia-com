<?php
get_header();

$fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
?>

<!-- main content -->
<main id="main-content">
  <?php
    get_template_part('partials/front-page/front-page-signups');

    // **************
    // ABOVE THE FOLD
    // **************

    $featured_main_1 = NM_get_option('nm_front_page_main_featured_article_1');
    $featured_main_2 = NM_get_option('nm_front_page_main_featured_article_2');
    $featured_sub = NM_get_option('nm_front_page_sub_featured_article');

    // *******************
    // get the 2 selected featured posts. fallback to most recent articles if not selected
    // note: some variable names refer to articles in the context of these posts. this was built first for articles but changed to all posts to support occational special video embeds on the homepage

    // setup basic args
    $featured_args = array(
      'posts_per_page' => 2,
      'fields' => 'ids',
    );

    // get fallback most recent article posts
    $featured_fallback = new WP_Query(array_merge($featured_args, array('category_name' => 'articles')));

    // set fallback ids in array
    $featured_posts_ids = $featured_fallback->posts;

    // get set featured post values
    if ($featured_main_1 || $featured_main_2) {
      $featured_main_ids = array_merge(explode(', ', $featured_main_1), explode(', ', $featured_main_2));

      // if set parse ids and query for post ids
      $featured_args['post__in'] = $featured_main_ids;
      $featured_args['orderby'] = 'post__in';
      $featured_posts = new WP_Query($featured_args);

      // if featured post exist put them in front of the fallback ids
      if ($featured_posts->have_posts()) {
        $featured_posts_ids = array_merge($featured_posts->posts, $featured_posts_ids);
      }
    }

    // trim array to leave only 2 ids left and get full posts
    $featured_posts_ids = array_slice($featured_posts_ids, 0, 2);

    $featured_display = new WP_Query(array(
      'post__in' => $featured_posts_ids,
      'orderby'=> 'post__in'
    ));

    // *******************
    // get 5 most recent articles excluding the 2 featured

    $exluded_articles_ids = $featured_posts_ids;

    if ($featured_sub) {
      $exluded_articles_ids = array_merge($featured_posts_ids, array($featured_sub));
    }

    $recent_articles = new WP_Query(array(
      'category_name' => 'articles',
      'posts_per_page' => 5,
      'post__not_in' => $exluded_articles_ids
    ));

    // map the query return to get the IDs for other queries
    $recent_articles_ids = array_map('nm_filter_query_ids', $recent_articles->posts);

    // *******************
    // get featured audio shows
    $featured_audio_category_1 = NM_get_option('nm_front_page_featured_audio_1');
    $featured_audio_category_2 = NM_get_option('nm_front_page_featured_audio_2');

    // *******************
    // most recent top audio show

    $recent_audio_category_1 = new WP_Query(array(
      'category_name' => $featured_audio_category_1,
      'posts_per_page' => 1,
    ));

    // *******************
    // most recent second audio show

    $recent_audio_category_2 = new WP_Query(array(
      'category_name' => $featured_audio_category_2,
      'posts_per_page' => 1,
    ));

    // *******************
    // selected sub featured article. if none most recent analysis article excluding existing 7 requested articles

    $sub_featured_args = array(
      'posts_per_page' => 1
    );

    if ($featured_sub) {
      // if sub featured is set get it
      $sub_featured_args['post__in'] = explode(', ', $featured_sub);

      $sub_featured = new WP_Query($sub_featured_args);
    } else {
      // get fallback most recent analysis post that isn't already shown
      $sub_featured_args['category_name'] = 'analysis';
      $sub_featured_args['post__not_in'] = array_merge($featured_posts_ids, $recent_articles_ids);

      $sub_featured = new WP_Query($sub_featured_args);
    }

    // *******************
    // most recent tysky

    $recent_tysky = new WP_Query(array(
      'category_name' => 'tyskysour-video',
      'posts_per_page' => 1,
    ));
  ?>

  <section id="front-page-above-the-fold" class="container margin-bottom-mid mobile-margin-bottom-basic">
    <div class="row">
      <div class="front-page-above-the-fold__column col only-mobile">
        <?php
          // render 2 featured articles
          if ($featured_display->have_posts()) {
            while ($featured_display->have_posts()) {
              $featured_display->the_post();
              get_template_part('partials/front-page/front-page-featured-main');
            }
          }
        ?>
      </div>
      <div class="front-page-above-the-fold__column col col6">
        <?php
          // render 5 recent articles
          if ($recent_articles->have_posts()) {
            $i = 0;
            while ($recent_articles->have_posts()) {
              $i++;
              $recent_articles->the_post();
              if ($i <= 3) {
                get_template_part('partials/front-page/front-page-article-default');
              } else {
                get_template_part('partials/front-page/front-page-article-slim');
              }
            }
          }
        ?>
      </div>
      <div class="front-page-above-the-fold__column col col12 only-desktop">
        <?php
          // render 2 featured articles
          if ($featured_display->have_posts()) {
            while ($featured_display->have_posts()) {
              $featured_display->the_post();
              get_template_part('partials/front-page/front-page-featured-main');
            }
          }
        ?>
      </div>
      <div class="front-page-above-the-fold__column col col6">
        <?php
          // render recent top audio
          if ($recent_audio_category_1->have_posts()) {
            while ($recent_audio_category_1->have_posts()) {
              $recent_audio_category_1->the_post();
              get_template_part('partials/front-page/front-page-audio-slim');
            }
          }
        ?>
        <?php
          // render recent second audio
          if ($recent_audio_category_2->have_posts()) {
            while ($recent_audio_category_2->have_posts()) {
              $recent_audio_category_2->the_post();
              get_template_part('partials/front-page/front-page-audio-slim');
            }
          }
        ?>
        <?php
          // render sub featured or analysis article
          if ($sub_featured->have_posts()) {
            while ($sub_featured->have_posts()) {
              $sub_featured->the_post();
              get_template_part('partials/front-page/front-page-featured-sub');
            }
          }
        ?>
        <?php
          // render recent tysky sour
          if ($recent_tysky->have_posts()) {
            while ($recent_tysky->have_posts()) {
              $recent_tysky->the_post();
              get_template_part('partials/front-page/front-page-tysky');
            }
          }
        ?>
      </div>
    </div>
  </section>

  <?php
    get_template_part('partials/support-section');
  ?>

  <section id="front-page-audio-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $audio_category = get_term_by('slug', 'audio', 'category');
      $burner_category = get_term_by('slug', 'the-burner', 'category');

      $category_link = get_category_link( $audio_category->term_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Audio</a></h4>
      </div>
    </div>

    <div class="row">
      <?php
        $latest_audio = new WP_Query(array(
          'posts_per_page' => 8,
          'cat' => $audio_category->term_id,
          'category__not_in' => array($burner_category->term_id)
        ));

        if ($latest_audio->have_posts()) {
          $i = 0;
          while ($latest_audio->have_posts()) {
            $latest_audio->the_post();
            get_template_part('partials/front-page/front-page-audio-default');

            if ($i === 3) {
              echo '</div><div class="row margin-bottom-small mobile-margin-bottom-none">';
            }

            $i++;
          }
        }
      ?>
    </div>
  </section>

<!-- Tyksy Sour video block -->

  <section id="front-page-tysky-sour-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $video_category_slug = 'tyskysour-video';

      render_front_page_video_block($video_category_slug);
    ?>
  </section>

  <section id="front-page-articles-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      // *** EXCLUDE ALREADY SHOWN ABOVE

      $category_id = get_cat_ID('Articles');
      $category_link = get_category_link( $category_id );
    ?>

    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $category_link; ?>">Articles</a></h4>
      </div>
    </div>

    <div class="row margin-bottom-small mobile-margin-bottom-none">
      <?php
        $latest_articles = new WP_Query(array(
          'posts_per_page' => 8,
          'category_name' => 'Articles',
          'post__not_in' => array_merge($featured_posts_ids, $recent_articles_ids)
        ));

        if ($latest_articles->have_posts()) {
          $i = 0;
          while ($latest_articles->have_posts()) {
            $latest_articles->the_post();

            get_template_part('partials/front-page/front-page-article-default');

            if ($i === 3) {
              echo '</div><div class="row margin-bottom-small mobile-margin-bottom-none">';
            }

            $i++;
          }
        }
      ?>
    </div>
  </section>

  <section id="front-page-video-posts" class="container margin-top-mid margin-bottom-large mobile-margin-bottom-basic">
    <?php
      $video_category_slug = 'video';
      $excluded_category_slug = 'tyskysour-video';

      render_front_page_video_block($video_category_slug, $excluded_category_slug);
    ?>
  </section>

  <?php
    get_template_part('partials/support-section');
  ?>

<!-- end main-content -->
</main>

<?php
get_footer();
?>