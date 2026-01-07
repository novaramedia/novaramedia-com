<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

  <div class="container">
    <div class="row margin-top-large margin-bottom-basic">
      <div class="col col4"></div>
      <div class="col col16">
        <h2 class="margin-bottom-basic">404 !</h2>
        <h2 class="margin-bottom-basic">Sorry whatever you were looking for isn’t here</h2>
        <h3 class="margin-bottom-basic">Try a search above ↑</h3>
        <h3 class="margin-bottom-basic">Or view the latest posts below ↓</h3>
      </div>
    </div>
  </div>

  <!-- main posts loop -->
  <section id="posts" class="container">
    <div class="row margin-bottom-basic">
<?php
query_posts('posts_per_page=9');
if( have_posts() ) {
  $i = 0;
  while( have_posts() ) {
    the_post();
    $description = get_post_meta($post->ID, '_cmb_short_desc');

    if ($i % 3 === 0 && $i !== 0) {
      echo "</div>\n<div class=\"row margin-bottom-basic\">";
    }
?>

    <a href="<?php the_permalink() ?>">
      <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">

        <?php the_post_thumbnail('col8-16to9', array('class' => 'index-post-thumbnail')); ?>

        <h5 class="index-post-title margin-top-tiny margin-bottom-tiny text-wrap-pretty"><?php the_title(); ?></h5>

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