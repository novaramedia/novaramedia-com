<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

<?php
if (have_posts()) {
    while (have_posts()) {
        the_post();
        $meta = get_post_meta($post->ID); ?>
  <article id="page">
    <div class="container margin-top-small margin-bottom-basic">
      <div class="flex-grid-row margin-bottom-basic">
        <div class="flex-grid-item flex-item-xxl-12">
          <h4><?php the_title(); ?></h4>
        </div>
      </div>
      <div class="flex-grid-row margin-bottom-basic">
        <div class="flex-grid-item flex-item-xxl-12 page-copy">
          <?php the_content(); ?>
        </div>
      </div>
    </div>

    <?php
      get_template_part('partials/email-signup', null, array(
        'newsletter' => 'The Cortado'
      )); ?>

  <!-- end post -->
  </article>
<?php
    }
} ?>
<!-- end main-content -->

<?php
  get_template_part('partials/support-section');
?>

</main>

<?php
get_footer();
?>
