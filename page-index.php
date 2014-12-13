<?php get_header(); ?>

	<!-- main content -->

  <section id="main-content" class="container">

    <!-- main posts loop -->
    <section id="single-post">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
  $meta = get_post_meta($post->ID);
?>

      <article <?php post_class('row'); ?> id="post-<?php the_ID(); ?>">

        <div class="col col8">

					<h2>FM</h2>
				<?php
					$fm_posts = get_posts('category_name=fm&posts_per_page=-1');
					echo '<ul>';
					foreach ($fm_posts as $fm) {
						echo '<li><a href="' . get_permalink($fm->ID) .'">' . $fm->post_title . '</a></li>';
					};
					echo '</ul>';
				?>

        </div>

        <div class="col col8">

					<h2>TV</h2>
				<?php
					$txt_posts = get_posts('category_name=tv&posts_per_page=-1');
					echo '<ul>';
					foreach ($txt_posts as $txt) {
						echo '<li><a href="' . get_permalink($txt->ID) .'">' . $txt->post_title . '</a></li>';
					};
					echo '</ul>';
				?>

        </div>

        <div class="col col8">

					<h2>TXT</h2>
				<?php
					$txt_posts = get_posts('category_name=txt&posts_per_page=-1');
					echo '<ul>';
					foreach ($txt_posts as $txt) {
						echo '<li><a href="' . get_permalink($txt->ID) .'">' . $txt->post_title . '</a></li>';
					};
					echo '</ul>';
				?>

        </div>

      </article>

    <?php endwhile; else: ?>
      <p><?php _e('Sorry, no posts matched your criteria :{'); ?></p>
    <?php endif; ?>

    </section>

  <!-- end main-content -->

  </section>

<?php get_footer(); ?>