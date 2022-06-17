<?php
/*
>>>TODO
- [ ] Full comment code
- [ ] Bring contrib description into bottom of single article
*/

$is_full_archive = get_query_var('is_full_archive');

function render_posts_section($query, $title, $is_full_archive) {
  $post_classes_default = 'flex-grid-item flex-item-m-6 flex-item-l-4 flex-item-xxl-3 margin-bottom-small';
  $post_classes_larger_display = 'flex-grid-item flex-item-s-12 flex-item-m-6 flex-item-xxl-4 margin-bottom-small';

  if( $query->have_posts() ) {
?>
  <div class="flex-grid-row margin-bottom-small">
    <div class="flex-grid-item flex-item-xxl-12 margin-bottom-tiny">
      <h4><?php echo $title; ?></h4>
    </div>
<?php
      while( $query->have_posts() ) {
        $query->the_post();

        if ( $is_full_archive ) {
          get_template_part('partials/post-layouts/list-post', null, array(
            'grid-item-classes' => 'flex-grid-item flex-item-xxl-12 margin-bottom-tiny',
          ));
        } else {
          get_template_part('partials/post-layouts/flex-post', null, array(
            'grid-item-classes' => $query->post_count < 6 ? $post_classes_larger_display : $post_classes_default,
            'image-size' => 'col12-16to9',
          ));
        }
      }

      if ( $query->max_num_pages > 1 ) {
        pr('full archive link');
      }
?>
  </div>
<?php
  }
}

get_header();
?>
<main id="main-content">
  <div id="contributor" class="container margin-top-small margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $link = get_post_meta($post->ID, '_nm_contributor_link', true);
?>
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12">
        <h4>Contributor</h4>
      </div>
    </div>
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-4 mobile-margin-bottom-micro">
        <h1 class="font-size-3 font-semibold">
          <?php the_title(); ?>
        </h1>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-5">
        <?php the_content(); ?>
        <?php
          if (!empty($link)) {
            echo '<a href="' . $link . '" target="_blank" ref="nofollow">' . $link . '</a>';
          }
        ?>
      </div>
    <?php
      if (has_post_thumbnail()) {
    ?>
      <div class="flex-grid-item flex-offset-l-0 flex-item-l-2 flex-offset-xxl-2 flex-item-xxl-1 only-desktop">
        <?php the_post_thumbnail('col4-square'); ?>
      </div>
    <?php
      }
    ?>
    </div>
    <?php
      $base_args = array(
        'post_type'    => 'post',
        'posts_per_page' => $is_full_archive ? -1 : 12,
        'meta_key'     => '_cmb_contributors',
        'meta_value'   => get_the_ID(),
        'meta_compare' => 'LIKE',
      );

      $articles = new WP_Query(wp_parse_args(array('category_name' => 'Articles'), $base_args));
      render_posts_section($articles, 'Articles', $is_full_archive);
      wp_reset_postdata();

      $audio = new WP_Query(wp_parse_args(array('category_name' => 'Audio'), $base_args));
      render_posts_section($audio, 'Audio', $is_full_archive);
      wp_reset_postdata();

      $downstream_cat_id = get_cat_ID('Downstream') ? get_cat_ID('Downstream') : false;

      if ( $downstream_cat_id ) {
        $downstream = new WP_Query(wp_parse_args(array('cat' => $downstream_cat_id), $base_args));
        render_posts_section($downstream, 'Downstream', $is_full_archive);
        wp_reset_postdata();
      }

      $tyksysour_cat_id = get_cat_ID('TyskySour') ? get_cat_ID('TyskySour') : false;

      if ( $tyksysour_cat_id ) {
        $tyskysour = new WP_Query(wp_parse_args(array('cat' => $tyksysour_cat_id), $base_args));
        render_posts_section($tyskysour, 'TyskySour', $is_full_archive);
        wp_reset_postdata();
      }

      $other_video = new WP_Query(wp_parse_args(array(
        'category_name' => 'Video',
        'category__not_in' => array($downstream_cat_id, $tyksysour_cat_id)
      ), $base_args));
      render_posts_section($other_video, 'Video', $is_full_archive);
      wp_reset_postdata();
    ?>
  </div>
  <?php
  }
}
    get_template_part('partials/support-section');
  ?>
</main>
<?php
get_footer();
?>
