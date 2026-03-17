<?php
// @deprecated Not referenced anywhere in the theme. Uses legacy col grid. Safe to delete.
  $description = get_post_meta( $post->ID, '_cmb_short_desc' );
?>
<a href="<?php the_permalink(); ?>">
  <article <?php post_class( 'col col6' ); ?> id="post-<?php the_ID(); ?>">
    <?php the_post_thumbnail( 'col6-16to9', array( 'class' => 'related-post-thumbnail only-desktop' ) ); ?>
    <?php the_post_thumbnail( 'mobile-16to9', array( 'class' => 'related-post-thumbnail only-mobile' ) ); ?>
    <h5 class="mt-2 mb-2 js-fix-widows"><?php render_post_title( $post->ID ); ?></h5>
    <div class="post-description">
      <?php
      if ( ! empty( $description ) ) {
        echo $description[0];
      } else {
        the_excerpt();
      }
      ?>
    </div>
  </article>
</a>
