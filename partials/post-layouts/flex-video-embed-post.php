<?php
// @deprecated Replaced by archive-post.php with show-video-embed flag. No longer referenced. Safe to delete.
if ( empty( $args['grid-item-classes'] ) ) {
  return;
}

$this_post_id = get_the_ID();
$meta         = get_post_meta( $this_post_id );
$youtube_id   = ! empty( $meta['_cmb_utube'][0] ) ? $meta['_cmb_utube'][0] : false;
$is_show_tags = ! empty( $args['show-tags'] ) ? $args['show-tags'] : false;
?>
<article <?php post_class( $args['grid-item-classes'] ); ?> id="post-<?php the_ID(); ?>">
  <?php if ( $youtube_id ) { ?>
    <?php if ( $is_show_tags ) { ?>
      <div class="layout-thumbnail-frame">
        <div class="layout-thumbnail-frame__inner mt-1 ml-1">
          <?php render_post_ui_tags( $this_post_id, true, true, 'no-border' ); ?>
        </div>
        <div class="u-video-embed-container ui-rounded-image">
          <?php echo render_youtube_embed_iframe( $youtube_id, true ); ?>
        </div>
      </div>
    <?php } else { ?>
      <div class="u-video-embed-container ui-rounded-image">
        <?php echo render_youtube_embed_iframe( $youtube_id, true ); ?>
      </div>
    <?php } ?>
  <?php } else { ?>
    <?php if ( $is_show_tags ) { ?>
      <div class="layout-thumbnail-frame">
        <div class="layout-thumbnail-frame__inner mt-1 ml-1">
          <?php render_post_ui_tags( $this_post_id, true, true, 'no-border' ); ?>
        </div>
        <a href="<?php the_permalink(); ?>" class="ui-hover">
          <?php render_thumbnail( $this_post_id, 'col12-16to9', array( 'class' => 'ui-rounded-image' ) ); ?>
        </a>
      </div>
    <?php } else { ?>
      <a href="<?php the_permalink(); ?>" class="ui-hover">
        <?php render_thumbnail( $this_post_id, 'col12-16to9', array( 'class' => 'ui-rounded-image' ) ); ?>
      </a>
    <?php } ?>
  <?php } ?>
  <a href="<?php the_permalink(); ?>" class="ui-hover">
    <h5 class="index-post-title font-size-9 font-weight-bold mt-2 text-wrap-pretty"><?php the_title(); ?></h5>
    <div class="font-size-9 mt-1">
      <?php render_short_description( $this_post_id ); ?>
    </div>
  </a>
</article>
