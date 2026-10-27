<?php
/**
 * Do Your Own Research archive grid card: the large archive-post card with a
 * season/episode label above the title and a larger title and standfirst.
 *
 * DYOR-only for now, so archive-post.php and the views that share it are
 * untouched. Takes the post ID rather than reading the global $post, so the
 * archive never has to swap it.
 *
 * @since 4.10.0
 *
 * $args['post-id']           Post ID.
 * $args['grid-item-classes'] Grid classes for the card.
 */

if ( empty( $args['post-id'] ) || empty( $args['grid-item-classes'] ) ) {
  return;
}

$this_post_id  = (int) $args['post-id'];
$permalink     = get_permalink( $this_post_id );
$is_article    = nm_is_article( $this_post_id );
$episode_label = nm_get_season_episode_label( $this_post_id );
?>
<article <?php post_class( $args['grid-item-classes'], $this_post_id ); ?> id="post-<?php echo esc_attr( $this_post_id ); ?>">
  <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover u-display-block">
    <?php
    render_thumbnail(
      $this_post_id,
      'col12-16to9',
      array(
        'class' => 'ui-rounded-box u-display-block',
      )
    );
    ?>
  </a>
  <a href="<?php echo esc_url( $permalink ); ?>" class="ui-hover u-display-block">
    <?php if ( $episode_label !== '' ) { ?>
    <h4 class="font-size-8 font-weight-bold mt-2" data-testid="episode-label"><?php echo esc_html( $episode_label ); ?></h4>
    <?php } ?>
    <h3 class="font-size-11 font-weight-bold mt-1 text-wrap-pretty"><?php echo esc_html( get_the_title( $this_post_id ) ); ?></h3>
    <h3 class="font-size-10 font-weight-bold mt-1 text-wrap-pretty">
      <?php
      if ( $is_article ) {
        render_bylines( $this_post_id );
      } else {
        render_standfirst( $this_post_id );
      }
      ?>
    </h3>
    <div class="mt-1">
      <?php
      if ( $is_article ) {
        render_standfirst( $this_post_id );
      } else {
        render_short_description( $this_post_id );
      }
      ?>
    </div>
  </a>
</article>
