<?php get_header(); ?>

	<!-- main content -->

  <section id="main-content" class="container">

    <!-- main posts loop -->
    <section id="single-post">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
  $meta = get_post_meta($post->ID);
?>

      <article <?php post_class('row'); ?> id="post-<?php the_ID(); ?>">

        <div class="col col6">

  				<ul>
<?php
if (97 == $post->post_parent) {
  wp_list_pages("child_of=97&sort_column=menu_order&title_li=");
  echo '<li class="page_item"><a href="' . home_url('/api') . '">Developers</a></li>';
} else {
  wp_list_pages("child_of=".$post->ID."&sort_column=menu_order&title_li=");
  if (97 == $post->ID) {
    echo '<li class="page_item"><a href="' . home_url('/api') . '">Developers</a></li>';
	}
}
?>
  				</ul>

        </div>

        <div class="col col12">

          <h1 class="single-post-title underline"><?php the_title(); ?></h1>

          <?php the_content(); ?>

        </div>

      </article>

    <?php endwhile; else: ?>
      <p><?php _e('Sorry, no posts matched your criteria :{'); ?></p>
    <?php endif; ?>

    </section>

  <!-- end main-content -->

  </section>

<?php get_footer(); ?>