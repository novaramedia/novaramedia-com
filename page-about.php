<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
?>
  <!-- main posts loop -->
  <section id="page" class="container margin-bottom-large">
    <div class="row margin-bottom-basic">
      <div class="col col24">
        <h4><?php the_title(); ?></h4>
      </div>
    </div>
    <div class="row margin-bottom-mid">
      <div class="col col10">
        <?php the_content(); ?>
      </div>
      <div class="col col2"></div>
      <div class="col col10">
        <?php if (!empty($meta['_cmb_page_extra'])) {
          echo apply_filters('the_content', $meta['_cmb_page_extra'][0]);
        } ?>
      </div>
    </div>
    <div class="row margin-bottom-basic">
      <div class="col col24">
        <h5>Editorial Team</h5>
      </div>
    </div>
    <div class="row margin-bottom-mid">
    <?php
    $people = new WP_Query(array(
      'post_type' => 'person',
      'posts_per_page' => -1,
      'order' => 'ASC'
    ));
    if ( $people->have_posts() ) {
      $i = 0;
      while( $people->have_posts() ) {
        $people->the_post();

        if ($i % 2 === 0 && $i !== 0) {
          echo "</div>\n<div class=\"row margin-bottom-mid\">";
        }
        $meta = get_post_meta($post->ID);
?>
      <div class="col col4">
        <?php the_post_thumbnail('col4'); ?>
      </div>
      <div class="col col8">
        <div class="margin-bottom-small">
        <h6><span class="font-bold"><?php the_title(); ?></span>
          <?php if (!empty($meta['_cmb_title'][0])) {echo $meta['_cmb_title'][0];} ?>
          </h6>
<?php
        if (!empty($meta['_cmb_twitter'][0])) {
?>
          <h6><a target="_blank" href="https://twitter.com/<?php echo $meta['_cmb_twitter'][0]; ?>"><?php echo $meta['_cmb_twitter'][0]; ?></a></h6>
<?php
        }

        if (!empty($meta['_cmb_email'][0])) {
?>
          <h6><a target="_blank" href="mailto:<?php echo $meta['_cmb_email'][0]; ?>"><?php echo $meta['_cmb_email'][0]; ?></a></h6>
<?php
        }
?>
        </div>
        <?php the_content(); ?>
      </div>
<?php
      $i++;
      }
    }
?>
    </div>
  <!-- end post -->
  </section>
<?php
  }
  wp_reset_postdata();
} else {
?>
  <div class="container">
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </div>
<?php
} ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>
