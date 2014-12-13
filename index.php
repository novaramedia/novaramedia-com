<?php get_header(); ?>

	<!-- main content -->

  <section id="main-content" class="container">

    <!-- main posts loop -->
    <section id="posts" class="row masonry">

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
      $meta = get_post_meta($post->ID);
      $cats = get_the_category();
      $cat = array_shift($cats);
      $type = $cat->slug;
      $thumb = 'col12-' . $type;
    ?>

      <article <?php post_class('col col12 ' . $type); ?> id="post-<?php the_ID(); ?>">

        <a href="<?php the_permalink() ?>">
          <?php the_post_thumbnail($thumb); ?>
        </a>

        <a href="<?php the_permalink() ?>">
          <h2 class="post-title-index underline"><?php the_title(); ?></h2>
        </a>

        <?php
          if (!empty($meta['_cmb_short-desc'][0])) {
            echo wpautop($meta['_cmb_short-desc'][0]);
          } else {
            the_content();
          } ?>

      </article>

    <?php endwhile; else: ?>
      <p><?php _e('Sorry, no posts matched your criteria :{'); ?></p>
    <?php endif; ?>

    <!-- end posts -->
    </section>

    <?php if (get_next_posts_link() || get_previous_posts_link()) { ?>
    <!-- post pagination -->
    <nav id="pagination" class="row">
      <div class="col col24 u-align-center">
        <h3><?php
              $previous = get_previous_posts_link('Newer');
              $next = get_next_posts_link('Older');
              if ($previous) {
                echo '<span class="pagination-button">' . $previous . '</span>';
              }
              if ($previous && $next) {
                echo ' &mdash; ';
              }
              if ($next) {
                echo '<span class="pagination-button">' . $next . '</span>';
              }
        ?></h3>
      </div>
    </nav>
    <?php } ?>

  <!-- end main-content -->

  </section>

<?php get_footer(); ?>