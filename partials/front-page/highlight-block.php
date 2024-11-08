<?php
  $settings = NM_get_option('all', 'nm_front_page_highlight_section_options');

  if (!isset($settings['nm_front_page_highlight_section_options_is_displayed']) || $settings['nm_front_page_highlight_section_options_is_displayed'] !== 'on') {
    return;
  }

  $section_term = get_term($settings['nm_front_page_highlight_section_options_section']);

  if (is_wp_error($section_term) || empty($section_term)) {
    return;
  }

  $highlight_title = !empty($settings['nm_front_page_highlight_section_options_display_title']) ? $settings['nm_front_page_highlight_section_options_display_title'] : $section_term->name;
  $highlight_description = !empty($settings['nm_front_page_highlight_section_options_description']) ? $settings['nm_front_page_highlight_section_options_description'] : '';

  $posts_above_the_fold_ids = [];

  if (isset($args['excluded_posts_ids'])) {
    $posts_above_the_fold_ids = $args['excluded_posts_ids'];
  }

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
            'field' => 'id',
            'terms' => $section_term->term_id,
        )
    ),
  );

  $latest_featured_posts = new WP_Query($latest_featured_args);
  $latest_featured_posts_ids = $latest_featured_posts->posts;

  // Non-featured posts from query
  $latest_others_posts_to_show = 7;
  $latest_others_args = array(
    'post__not_in' => array_merge($posts_above_the_fold_ids, $latest_featured_posts_ids),
    'posts_per_page' => $latest_others_posts_to_show,
    'category_name' => 'articles,video,audio',
    'tax_query' => array(
        array (
            'taxonomy' => 'section',
            'field' => 'id',
            'terms' => $section_term->term_id,
        )
    ),
  );
?>
<section class="front-page-highlight-block pt-6 pb-6 pt-s-5 pb-s-5">
  <div class="container">
    <div class="grid-row">
      <div class="grid-item is-xxl-24 mb-5">
        <h3 class="font-size-13 layout-flex-no-shrink mr-4"><strong><?php echo $highlight_title; ?></strong> <?php echo $highlight_description; ?></h3>
      </div>
    </div>
  </div>
  <div class="container container--padded">
    <div class="highlight-block layout-grid">
      <div class="highlight-block__featured-1">
        <div class="grid-row grid--nested">
          <div class="grid-item is-s-24 is-xxl-16">
            <div class="ui-border-bottom pb-4 mb-4">
              <?php
                if (is_numeric($latest_featured_posts_ids[0])) {
                  get_template_part('partials/front-page/above-the-fold/featured-post-primary', null, array(
                    'post_id' => $latest_featured_posts_ids[0],
                    'show_related' => false,
                    'has_huge_headline' => false
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
          <div class=" grid-item is-s-24 is-xxl-8">
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
                  'show_descriptive_text' => false,
                ));
              }

              if (is_numeric($latest_featured_posts_ids[6])) {
                get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
                  'post_id' => $latest_featured_posts_ids[6],
                  'container_classes' => 'mb-4',
                  'show_descriptive_text' => false,
                ));
              }
            ?>
          </div>
        </div>
      </div>
      <div class="highlight-block__latest-posts">
          <div class="layout-split-level font-size-8 font-weight-bold mb-4">
            <a href="<?php echo get_term_link($section_term); ?>">
            <h5 class="font-weight-bold text-uppercase"><?php echo $section_term->name; ?></h5>
            </a>
            <a href="<?php echo get_term_link($section_term); ?>" class="ui-action-link ui-action-link--small">See All</a>
          </div>
        </a>
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
              <div class="mb-1">
                <?php render_post_ui_tags($post->ID, true, true); ?>
              </div>
              <a href="<?php the_permalink(); ?>" class="ui-hover">
                <h4 class="post__title font-size-9 font-weight-bold">
                  <?php the_title(); ?>
                </h4>
                <?php
                  if (nm_is_article($post->ID)) {
                ?>
                <h5 class="font-size-8 font-weight-bold text-uppercase mt-1">
                  <?php render_bylines($post->ID, false); ?>
                </h5>
                <?php
                  }
                ?>
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
