<?php
/**
 * Pure-text archive post layout.
 *
 * Date (house NM_DATE_FORMAT_LONG), headline, then excerpt — no image, avatar or byline.
 * For archive grids whose design is type only, e.g. the If I Speak category archive.
 * Sibling of archive-post.php (image-led) and archive-post-opinion.php (avatar-led).
 * Colour is left to the page: the rule is .ui-border-top (set --ui-border-color) and the
 * headline is .index-post-title.
 *
 * Args:
 *   grid-item-classes  (string, required) Classes for the wrapping article. Returns early if empty.
 */

if ( empty( $args['grid-item-classes'] ) ) { // if no classes set for grid item don't render
  return;
}

$this_post_id = get_the_ID();
?>
<article <?php post_class( $args['grid-item-classes'] ); ?> id="post-<?php the_ID(); ?>" data-testid="archive-post-pure-text">
  <a href="<?php the_permalink(); ?>" class="ui-hover ui-border-top u-display-block pt-4">
    <time class="font-size-9 u-display-block" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( NM_DATE_FORMAT_LONG ) ); ?></time>
    <h5 class="index-post-title font-size-12 font-weight-bold mt-3 text-wrap-pretty"><?php the_title(); ?></h5>
    <div class="font-size-10 mt-4">
      <?php
      if ( nm_is_article( $this_post_id ) ) {
        render_standfirst( $this_post_id );
      } else {
        render_short_description( $this_post_id );
      }
      ?>
    </div>
  </a>
</article>
