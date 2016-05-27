<?php
function render_tv_query($query) {
  if ($query->have_posts()) {
  ?>
  <div class="col col20">
    <?php
      $meta = get_post_meta($query->posts[0]->ID);
      if (!empty($meta['_cmb_utube'])) {
    ?>
      <div class="u-video-embed-container">
        <iframe class="youtube-player" type="text/html" src="http://www.youtube.com/embed/<?php echo $meta['_cmb_utube'][0]; ?>?autohide=2&amp;modestbranding=1&amp;origin=http://novaramedia.com&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
      </div>
    <?php
      } else {
        echo 'Someone messed up :/';
      }
    ?>
  </div>
  <div class="col col4">
    <?php
        global $post;
        for ($i = 1; $i < count($query->posts); $i++) {
          $post = $query->posts[$i];
          setup_postdata($post);
    ?>
    <a href="<?php the_permalink(); ?>">
     <div class="single-tv-related-tv margin-bottom-small">
       <?php the_post_thumbnail('col4-16to9'); ?>
       <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
     </div>
   </a>
    <?php
        }
        wp_reset_postdata();
    ?>
  </div>
  <?php
  }
}