<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$is_full_archive = get_query_var('is_full_archive'); // get query var to see if in archive mode

/**
 * Renders a section of posts (on this page only). Depending on number of posts will display differently.
 *
 * @param Object  $query WP Query of posts to render
 * @param string  $title Title of the section
 * @param Boolean $is_full_archive If displaying in full archive style
 */
function render_posts_section($query, $title, $is_full_archive) {
  $post_classes_default = 'grid-item is-m-12 is-l-8 is-xxl-6 mb-4';
  $post_classes_larger_display = 'grid-item is-s-24 is-m-12 is-xxl-8 mb-4';

  if( $query->have_posts() ) {
?>
  <div class="grid-row mb-4">
    <div class="grid-item is-xxl-24 mb-2">
      <h4 class="font-size-9 text-uppercase font-weight-bold"><?php
 echo $title; ?></h4>
    </div>
<?php

      while( $query->have_posts() ) {
        $query->the_post();

        if ( $is_full_archive ) {
          get_template_part('partials/post-layouts/list-post', null, array(
            'grid-item-classes' => 'grid-item is-xxl-24 mb-2'
          ));
        } else {
          get_template_part('partials/post-layouts/archive-post', null, array(
            'grid-item-classes' => $query->post_count < 6 ? $post_classes_larger_display : $post_classes_default,
            'image-size' => 'col12-16to9',
            'show-tags' => true
          ));
        }
      }

      if ( $query->max_num_pages > 1 ) {
?>
    <div class="grid-item is-xxl-24 mt-5 mb-5">
      <a href="?is_full_archive=true" class="ui-action-link">View full archive</a>
    </div>
<?php

      }
?>
  </div>
<?php

  }
}

get_header();
?>
<main id="main-content">
  <div id="contributor" class="container mt-4 mb-4">
<?php

if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $link = get_post_meta($post->ID, '_nm_contributor_link', true);
?>
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold">Contributor</h4>
      </div>
    </div>
    <div class="grid-row mb-4">
      <div class="grid-item is-s-24 is-xxl-8 mobile-margin-bottom-micro">
        <h1 class="font-size-12 font-weight-semibold">
          <?php
 the_title(); ?>
        </h1>
      </div>
      <div class="grid-item is-s-24 is-l-12 is-xxl-10">
        <?php
 the_content(); ?>
        <?php

          if (!empty($link)) {
            echo '<div class="text-links-underlined mt-2"><a href="' . $link . '" target="_blank" ref="nofollow">' . $link . '</a></div>';
          }
        ?>
      </div>
    <?php

      if (has_post_thumbnail()) {
    ?>
      <div class="grid-item offset-l-0 is-l-4 offset-xxl-4 is-xxl-2 only-desktop">
        <?php
 the_post_thumbnail('grid-item4-square'); ?>
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

      $novaralive_cat_id = get_cat_ID('Novara Live') ? get_cat_ID('Novara Live') : false;

      if ( $novaralive_cat_id ) {
        $novaralive = new WP_Query(wp_parse_args(array('cat' => $novaralive_cat_id), $base_args));
        render_posts_section($novaralive, 'Novara Live', $is_full_archive);
        wp_reset_postdata();
      }

      $other_video = new WP_Query(wp_parse_args(array(
        'category_name' => 'Video',
        'category__not_in' => array($downstream_cat_id, $novaralive_cat_id)
      ), $base_args));
      render_posts_section($other_video, 'Video', $is_full_archive);
      wp_reset_postdata();
    ?>
  </div>
  <?php

  }
}
    get_template_part( 'partials/support-section', null, array( 'container_classes' => 'mb-4' ) );
  ?>
</main>
<?php

get_footer();
?>
