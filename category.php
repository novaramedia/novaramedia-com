<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

<?php
  $video = get_category_by_slug('video');
  $category = get_category(get_query_var('cat'));
?>

  <!-- main posts loop -->
  <section id="posts" class="container">

    <div class="row margin-bottom-basic">
      <div class="col col24">
        <h4 class="margin-bottom-tiny"><a href="<?php echo get_category_link($category->term_id); ?>"><?php echo $category->name; ?></a></h4>
        <?php echo category_description(); ?>
      </div>
    </div>

<?php
  if ($video->term_id === $category->term_id || $video->term_id === $category->category_parent) {

    if (have_posts()) {
      $i = 0;
      the_post();
    ?>
    <div class="row margin-bottom-large only-desktop">
      <div class="col col16">
        <?php
        $meta = get_post_meta($post->ID);
        if (!empty($meta['_cmb_utube'])) {
        ?>
        <div class="u-video-embed-container">
          <iframe class="youtube-player" type="text/html" src="http://www.youtube.com/embed/<?php echo $meta['_cmb_utube'][0]; ?>?autohide=2&amp;modestbranding=1&amp;origin=http://novaramedia.com&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
        </div>
        <a href="<?php the_permalink(); ?>">
          <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
        </a>
        <?php
        } else {
          echo 'Someone messed up :/';
        }
        ?>
      </div>
      <div class="col col4">
        <?php
        if (have_posts()) {
          while(have_posts() && $i < 6) {
            the_post();
        ?>
        <a href="<?php the_permalink(); ?>">
         <div class="single-tv-related-tv margin-bottom-small">
           <?php the_post_thumbnail('col4-16to9'); ?>
           <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
         </div>
       </a>
        <?php
          if ($i === 2) {
            echo '</div><div class="col col4">';
          }

          $i++;
          }
        }
        ?>
      </div>
    </div>
  <?php
    }

    // reset pointer for have_posts
    global $wp_query;
    $wp_query->current_post = -1;
  }
?>

    <div class="row margin-bottom-basic">
<?php
if( have_posts() ) {
  $i = 0;
  while( have_posts() ) {
    the_post();

    if ($i % 3 === 0 && $i !== 0) {
      echo "</div>\n<div class=\"row margin-bottom-basic\">";
    }

    get_template_part('partials/post-layouts/post-col8');

    $i++;
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="row">
      <div class="col col24">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>

  <!-- end posts -->
  </section>

<!-- end main-content -->

</main>

<?php
get_footer();
?>