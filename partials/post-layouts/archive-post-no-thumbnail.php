<?php
/**
 * Thumbnail-less archive post layout.
 *
 * Author avatar, headline, byline with date, then standfirst. For archive grids whose
 * design carries no post image — first used on The Cortado category archive. Sibling of
 * archive-post.php, minus the image-size and text-size args.
 *
 * Args:
 *   grid-item-classes  (string, required) Classes for the wrapping article. Returns early if empty.
 *   hide-excerpt       (bool, optional) Omit the standfirst / short description. The front-page
 *                      Cortado block shows title and byline only.
 */

if ( empty( $args['grid-item-classes'] ) ) { // if no classes set for grid item don't render
  return;
}

$this_post_id = get_the_ID();

$hide_excerpt = ! empty( $args['hide-excerpt'] );

// The avatar is the first contributor's featured image. Posts without one — legacy
// _cmb_author posts, or a contributor with no thumbnail — render text only.
$contributors   = get_contributors_array( $this_post_id );
$avatar_post_id = ( ! empty( $contributors ) && has_post_thumbnail( $contributors[0]->ID ) ) ? $contributors[0]->ID : false;
?>
<article <?php post_class( $args['grid-item-classes'] ); ?> id="post-<?php the_ID(); ?>" data-testid="archive-post-no-thumbnail">
  <a href="<?php the_permalink(); ?>" class="layout-flex ui-hover ui-border-top pt-4">
    <?php if ( $avatar_post_id ) { ?>
    <div class="layout-flex-no-shrink mr-3">
      <?php
      render_thumbnail(
        $avatar_post_id,
        'col4-square',
        array(
          'class'   => 'ui-circle-image ui-border',
          'loading' => 'lazy',
        )
      );
      ?>
    </div>
    <?php } ?>
    <div class="layout-flex-grow">
      <h5 class="index-post-title font-size-11 font-weight-bold text-wrap-pretty"><?php the_title(); ?></h5>
      <h6 class="font-size-8 font-weight-bold text-uppercase mt-2">
        <?php render_bylines( $this_post_id ); ?>
        <span class="ml-3"><?php echo esc_html( get_the_date( NM_DATE_FORMAT_LONG ) ); ?></span>
      </h6>
      <?php
      if ( ! $hide_excerpt ) {
        // nm_is_article() matches the `articles` term or a direct child of it, so a post filed
        // only under a grandchild category would fall to render_short_description() instead of
        // its standfirst. Cortado posts also carry `articles` directly, so they classify
        // correctly whichever parent the category ends up under — but the two branches are only
        // equivalent while that stays true. Resolved here rather than above so the term lookups
        // are skipped entirely when the excerpt is hidden.
        ?>
      <div class="font-size-10 mt-2">
        <?php
        if ( nm_is_article( $this_post_id ) ) {
          render_standfirst( $this_post_id );
        } else {
          render_short_description( $this_post_id );
        }
        ?>
      </div>
        <?php
      }
      ?>
    </div>
  </a>
</article>
