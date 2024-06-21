<?php
  $section_slug = 'organised-labour';

  $posts_above_the_fold_ids = []; // Exclude posts from above the fold. needs this passed as a variable somehow

  // Featured posts from query
  $latest_featured_args = array(
    'post__not_in' => $posts_above_the_fold_ids,
    'posts_per_page' => 7,
    'fields' => 'ids',
    'category_name' => 'articles,video,audio',
    'meta_key' => '_cmb_featurable',
    'meta_value' => 'on',
    'tax_query' => array(
        array (
            'taxonomy' => 'section',
            'field' => 'slug',
            'terms' => $section_slug,
        )
    ),
  );

  $latest_featured_posts = new WP_Query($latest_featured_args);
  $latest_featured_posts_ids = $latest_featured_posts->posts;

  // Non-featured posts from query
  $latest_others_posts_to_show = 10;
  $latest_others_args = array(
    'post__not_in' => array_merge($posts_above_the_fold_ids, $latest_featured_posts_ids),
    'posts_per_page' => $latest_others_posts_to_show,
    'category_name' => 'articles,video,audio',
    // 'meta_key' => '_cmb_featurable',
    // 'meta_compare' => 'NOT LIKE',
    // 'meta_value' => 'on',
    'tax_query' => array(
        array (
            'taxonomy' => 'section',
            'field' => 'slug',
            'terms' => $section_slug,
        )
    ),
  );

  // pr($latest_featured_posts_ids);
?>
<section class="front-page-highlight-block pt-6 pb-6 pt-s-5 pb-s-5">
  <div class="container">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-5">
        <h3 class="fs-7 font-weight-regular layout-flex-no-shrink mr-4"><strong>General Election 2024</strong> This is the text talking about this highlight and explaining it's purpose.</h3>
      </div>
    </div>
  </div>
  <div class="container container--padded">
    <div class="highlight-block layout-grid">
      <div class="highlight-block__featured-1">
        <div class="grid-row grid--nested">
          <div class="grid-item is-l-24 is-xxl-16">
            <div class="ui-border-bottom pb-6 mb-4">
              <?php
                if (is_numeric($latest_featured_posts_ids[0])) {
                  get_template_part('partials/front-page/above-the-fold/featured-post-primary', null, array(
                    'post_id' => $latest_featured_posts_ids[0],
                    'show_related' => false,
                  ));
                }
              ?>
            </div>
            <div class="grid-row grid--nested">
              <div class="grid-item is-xxl-12">
                <?php
                  if (is_numeric($latest_featured_posts_ids[2])) {
                    get_template_part('partials/front-page/above-the-fold/latest-article--thumb-small', null, array(
                      'post_id' => $latest_featured_posts_ids[2],
                    ));
                  }
                ?>
              </div>
              <div class="grid-item is-xxl-12">
                <?php
                  if (is_numeric($latest_featured_posts_ids[3])) {
                    get_template_part('partials/front-page/above-the-fold/latest-article--thumb-small', null, array(
                      'post_id' => $latest_featured_posts_ids[3],
                    ));
                  }
                ?>
              </div>
            </div>
          </div>
          <div class=" grid-item is-l-24 is-xxl-8">
            <?php
              if (is_numeric($latest_featured_posts_ids[1])) {
                get_template_part('partials/front-page/above-the-fold/featured-post-secondary', null, array(
                  'post_id' => $latest_featured_posts_ids[1],
                  'container_classes' => 'mb-4',
                ));
              }

              if (is_numeric($latest_featured_posts_ids[4])) {
                get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
                  'post_id' => $latest_featured_posts_ids[4],
                  'container_classes' => 'mb-4',
                ));
              }

              if (is_numeric($latest_featured_posts_ids[5])) {
                get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
                  'post_id' => $latest_featured_posts_ids[5],
                  'container_classes' => 'mb-4',
                ));
              }

              if (is_numeric($latest_featured_posts_ids[6])) {
                get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
                  'post_id' => $latest_featured_posts_ids[6],
                  'container_classes' => 'mb-4',
                ));
              }
            ?>
          </div>
        </div>
      </div>
      <div class="highlight-block__latest-posts">
        <?php
          $latest_others = new WP_Query($latest_others_args);

            if ($latest_others->have_posts()) {
              $i = 1;
              while($latest_others->have_posts()) {
                $latest_others->the_post();
                $meta = get_post_meta($post->ID);
                $timestamp = get_post_time('c');
          ?>
            <div class="pb-3 mb-3 <?php if ($i < $latest_others_posts_to_show) {echo 'ui-border-bottom';} ?>">
              <div class="layout-split-level fs-2 mb-1">
                <?php render_post_ui_tags($post->ID, true, true); ?>
                <a href="<?php the_permalink(); ?>" class="ui-hover"><span><?php the_time('j F'); ?></span></a>
              </div>
              <a href="<?php the_permalink(); ?>" class="ui-hover">
                <h4 class="post__title fs-2 fs-s-4-sans font-bold">
                  <?php the_title(); ?>
                </h4>
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
</section>
