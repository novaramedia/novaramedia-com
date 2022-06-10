<?php
get_header();
?>
<main id="main-content">
  <article id="contributor" class="container margin-top-small margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $link = get_post_meta($post->ID, '_nm_contributor_link', true);
?>

    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12">
        <h4 class="margin-bottom-tiny">Contributor</h4>
        <h1 class="font-size-2 font-semibold">
          <?php the_title(); ?>
        </h1>
      </div>
    </div>

    <div class="flex-grid-row margin-bottom-basic mobile-margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-6">
        <?php the_content(); ?>
      </div>
      <div class="flex-grid-item flex-item-xxl-6">
        <?php
          if (!empty($link)) {
            echo '<a href="' . $link . '" target="_blank" ref="nofollow">' . $link . '</a>';
          }
        ?>
      </div>
    </div>

    <div class="flex-grid-row margin-bottom-basic mobile-margin-bottom-small">
      <?php
        $content = new WP_Query(array(
          'post_type'    => 'post',
          'posts_per_page' => 1,
          'meta_key'     => '_cmb_contributors',
          'meta_value'   => get_the_ID(),
          'meta_compare' => 'LIKE',
        ));

        if( $content->have_posts() ) {
          while( $content->have_posts() ) {
            $content->the_post();

            get_template_part('partials/post-layouts/flex-post', null, array(
              'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-m-6 flex-item-xxl-4 margin-bottom-basic',
              'image-size' => 'col12-16to9',
            ));
          }
        }
      ?>
    </div>

  </article>
  <?php
  }
}
    get_template_part('partials/support-section');
  ?>
</main>
<?php
get_footer();
?>
