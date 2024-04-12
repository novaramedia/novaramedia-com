<section id="front-page__mega-block" class="container mt-7 mb-5">
  <?php
    $sections_to_render = array(
      'UK',
      'World',
      'Labour Party',
      'Organised Labour',
      'Conservative Party',
      'Politics',
      'Economics',
      'Environment',
      'Policing',
      'Housing',
      'Society',
      'History',
      'TV & Film',
      'Culture',
    );
  ?>
  <div class="grid-row">
    <?php
      $excluded_ids = array();

      foreach($sections_to_render as $section) {
        $args = array(
          'posts_per_page' => 3,
          'tax_query' => array(array(
            'taxonomy' => 'section',
            'field' => 'name',
            'terms' => $section,
            ),
          ),
          'post__not_in' => $excluded_ids,
        );

        $posts = get_posts($args);

        if ($posts) {
    ?>
      <div class="grid-item is-s-12 is-m-8 is-xl-6 is-xxl-4 mb-6 fs-3-sans font-weight-bold">
        <h4 class="font-uppercase mb-2"><?php echo $section; ?></h4>
    <?php
          $excluded_ids = array_merge($excluded_ids, wp_list_pluck($posts, 'ID'));

          foreach ($posts as $index => $post) {
        ?>
        <a href="<?php echo get_permalink($post->ID); ?>">
          <?php
            if ($index === 0) {
              render_thumbnail($post->ID, 'col24-16to9', array(
                'class' => 'ui-rounded-image',
              ));
            }
          ?>
          <h5 class="<?php echo $index === 2 ? "mb-2" : "pb-2 mb-2 ui-border-bottom";?>"><?php echo $post->post_title; ?></h5>
        </a>
        <?php
          }
    ?>
      </div>
    <?php
        }
      }
    ?>
  </div>
</section>