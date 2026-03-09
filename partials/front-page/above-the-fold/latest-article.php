<?php
if ( ! isset( $args['post_id'] ) || ! is_numeric( $args['post_id'] ) ) {
  return;
}

if ( ! isset( $args['has_bottom_border'] ) ) {
  $args['has_bottom_border'] = false;
}

if ( ! isset( $args['show_image'] ) ) {
  $args['show_image'] = false;
}

$article_post_id = $args['post_id'];
$has_bottom_border = $args['has_bottom_border'];
$show_image = $args['show_image'];

$timestamp = get_post_time( 'c', false, $article_post_id );

$is_article = nm_is_article( $article_post_id );
$is_news = has_category( 'news', $article_post_id );

$permalink = esc_url( get_permalink( $article_post_id ) );
$timestamp_attr = esc_attr( $timestamp );
$title = esc_html( get_the_title( $article_post_id ) );
?>
<div class="mb-4 pb-4 <?php if ($has_bottom_border) {echo 'ui-border-bottom';} ?>">
<?php if ( $show_image === 'small' ) : ?>
  <div class="layout-split-level font-size-8 font-weight-bold mb-1">
    <?php render_post_ui_tags( $article_post_id ); ?>
    <a href="<?php echo $permalink; ?>" class="ui-hover">
      <span class="js-time-since" data-timestamp="<?php echo $timestamp_attr; ?>"></span>
    </a>
  </div>
  <div class="grid-row grid--nested">
    <div class="grid-item is-xxl-16">
      <a href="<?php echo $permalink; ?>" class="ui-hover">
      <h4 class="post__title font-size-11 font-size-s-12 font-weight-bold font-condensed text-wrap-pretty" data-testid="post-title"><?php echo $title; ?></h4>

      <h5 class="font-size-8 font-weight-bold text-uppercase mt-1">
        <?php
        if ( $is_news ) {
          // No byline for news posts
        } elseif ( $is_article ) {
          render_bylines( $article_post_id );
        } else {
          render_standfirst( $article_post_id );
        }
        ?>
      </h5>
      </a>
    </div>
    <div class="grid-item is-xxl-8">
      <a href="<?php echo $permalink; ?>" class="ui-hover">
      <?php
      render_thumbnail(
        $article_post_id,
        'col4-square',
        array(
          'class'             => 'ui-rounded-image u-display-block',
          'data-no-lazysizes' => true,
          'loading'           => 'eager',
        )
      );
      ?>
      </a>
    </div>
  </div>
<?php else : ?>
  <div class="layout-split-level font-size-8 font-weight-bold mb-1">
    <?php render_post_ui_tags( $article_post_id ); ?>
    <a href="<?php echo $permalink; ?>" class="ui-hover">
      <span class="js-time-since" data-timestamp="<?php echo $timestamp_attr; ?>"></span>
    </a>
  </div>
  <a href="<?php echo $permalink; ?>" class="ui-hover">
    <h4 class="post__title font-size-11 font-size-s-12 font-condensed text-wrap-pretty" data-testid="post-title"><?php echo $title; ?></h4>
<?php if ( $show_image === 'large' ) : ?>
    <div class="mt-2 mb-2">
      <?php
      render_thumbnail(
        $article_post_id,
        'col12-16to9',
        array(
          'class'             => 'ui-rounded-image u-display-block',
          'data-no-lazysizes' => true,
          'loading'           => 'eager',
        )
      );
      ?>
    </div>
<?php endif; ?>
    <h5 class="font-size-8 font-weight-bold text-uppercase mt-1">
      <?php
      if ( $is_news ) {
        // No byline for news posts
      } elseif ( $is_article ) {
        render_bylines( $article_post_id );
      } else {
        render_standfirst( $article_post_id );
      }
      ?>
    </h5>
  </a>
<?php endif; ?>
</div>
