<?php

/*
Sections:
- Articles
- Audio
- Downstream
- Tyksy
- Video
*/

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
        'posts_per_page' => 12,
        'meta_key'     => '_cmb_contributors',
        'meta_value'   => get_the_ID(),
        'meta_compare' => 'LIKE',
      );

      $post_classes_default = 'flex-grid-item flex-item-m-6 flex-item-l-4 flex-item-xxl-3 margin-bottom-small';
      $post_classes_larger_display = 'flex-grid-item flex-item-s-12 flex-item-m-6 flex-item-xxl-4 margin-bottom-small';

      $articles = new WP_Query(wp_parse_args(array('category_name' => 'Articles'), $base_args));

      if( $articles->have_posts() ) {
?>
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12 margin-bottom-tiny">
        <h4>Articles</h4>
      </div>
<?php
        while( $articles->have_posts() ) {
          $articles->the_post();

          get_template_part('partials/post-layouts/flex-post', null, array(
            'grid-item-classes' => $articles->post_count < 6 ? $post_classes_larger_display : $post_classes_default,
            'image-size' => 'col12-16to9',
          ));
        }
?>
    </div>
<?php
      }

      wp_reset_postdata();

      $audio = new WP_Query(wp_parse_args(array('category_name' => 'Audio'), $base_args));

      if( $audio->have_posts() ) {
?>
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12 margin-bottom-tiny">
        <h4>Audio</h4>
      </div>
<?php
        while( $audio->have_posts() ) {
          $audio->the_post();

          get_template_part('partials/post-layouts/flex-post', null, array(
            'grid-item-classes' => $audio->post_count < 6 ? $post_classes_larger_display : $post_classes_default,
            'image-size' => 'col12-16to9',
          ));
        }
?>
    </div>
<?php
      }

      wp_reset_postdata();

      $downstream_cat_id = get_cat_ID('Downstream') ? get_cat_ID('Downstream') : false;

      if ( $downstream_cat_id ) {
        $downstream = new WP_Query(wp_parse_args(array('cat' => $downstream_cat_id), $base_args));

        if( $downstream->have_posts() ) {
  ?>
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12 margin-bottom-tiny">
        <h4>Downstream</h4>
      </div>
<?php
        while( $downstream->have_posts() ) {
          $downstream->the_post();

          get_template_part('partials/post-layouts/flex-post', null, array(
            'grid-item-classes' => $downstream->post_count < 6 ? $post_classes_larger_display : $post_classes_default,
            'image-size' => 'col12-16to9',
          ));
        }
?>
    </div>
  <?php
        }

        wp_reset_postdata();

      }

      $tyksysour_cat_id = get_cat_ID('TyskySour') ? get_cat_ID('TyskySour') : false;

      if ( $tyksysour_cat_id ) {
        $tyskysour = new WP_Query(wp_parse_args(array('cat' => $tyksysour_cat_id), $base_args));

        if( $tyskysour->have_posts() ) {
?>
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12 margin-bottom-tiny">
        <h4>TyskySour</h4>
      </div>
<?php
        while( $tyskysour->have_posts() ) {
          $tyskysour->the_post();

          get_template_part('partials/post-layouts/flex-post', null, array(
            'grid-item-classes' => $tyskysour->post_count < 6 ? $post_classes_larger_display : $post_classes_default,
            'image-size' => 'col12-16to9',
          ));
        }
?>
    </div>
<?php
        }

        wp_reset_postdata();

      }

      $other_video = new WP_Query(wp_parse_args(array(
        'category_name' => 'Video',
        'category__not_in' => array($downstream_cat_id, $tyksysour_cat_id)
      ), $base_args));

      if( $other_video->have_posts() ) {
?>
    <div class="flex-grid-row margin-bottom-basic mobile-margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12 margin-bottom-tiny">
        <h4>Video</h4>
      </div>
<?php
        while( $other_video->have_posts() ) {
          $other_video->the_post();

          get_template_part('partials/post-layouts/flex-post', null, array(
            'grid-item-classes' => $other_video->post_count < 6 ? $post_classes_larger_display : $post_classes_default,
            'image-size' => 'col12-16to9',
          ));
        }
?>
    </div>
<?php
      }

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
