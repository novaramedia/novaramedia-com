<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <div class="container">
    <div class="row mt-8 mb-5">
      <div class="col col4"></div>
      <div class="col col16">
        <h2 class="mb-5">404 !</h2>
        <h2 class="mb-5">Sorry whatever you were looking for isn’t here</h2>
        <h3 class="mb-5">Try a search above ↑</h3>
        <h3 class="mb-5">Or view the latest posts below ↓</h3>
      </div>
    </div>
  </div>

  <!-- main posts loop -->
  <section id="posts" class="container">
    <div class="row mb-5">
<?php
query_posts('posts_per_page=9');
if( have_posts() ) {
  $i = 0;
  while( have_posts() ) {
    the_post();
    $description = get_post_meta($post->ID, '_cmb_short_desc');

    if ($i % 3 === 0 && $i !== 0) {
      echo "</div>\n<div class=\"row mb-5\">";
    }
?>

    <a href="<?php the_permalink() ?>">
      <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">

        <?php the_post_thumbnail('col8-16to9', array('class' => 'index-post-thumbnail')); ?>

        <h5 class="index-post-title mt-2 mb-2 text-wrap-pretty"><?php the_title(); ?></h5>

        <div class="index-post-description">
          <?php
            if (!empty($description)) {
              echo $description[0];
            } else {
              the_excerpt();
            }
          ?>
        </div>

      </article>
    </a>

<?php
    $i++;
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>

  <!-- end posts -->
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>