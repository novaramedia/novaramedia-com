<?php
/**
 * Search suggestion card: a category, newsletter or site section styled as a
 * peer of the archive post card (partials/post-layouts/archive-post.php).
 *
 * $args['destination']       Match from nm_get_search_destination_matches().
 * $args['grid-item-classes'] Grid classes for the card.
 *
 * @since 4.11.0
 */

if ( empty( $args['destination'] ) || empty( $args['grid-item-classes'] ) ) {
  return;
}

$destination = $args['destination'];
$image       = nm_get_search_destination_image( $destination );
?>
<article class="<?php echo esc_attr( $args['grid-item-classes'] ); ?>" data-testid="search-destination" data-destination-type="<?php echo esc_attr( $destination['type'] ); ?>">
  <a href="<?php echo esc_url( $destination['url'] ); ?>" class="ui-hover u-display-block">
    <div class="layout-thumbnail-frame">
      <div class="layout-thumbnail-frame__inner mt-1 ml-1">
        <span class="ui-tag-block ui-tag-block--no-border"><span class="ui-tag"><?php echo esc_html( nm_get_search_destination_tag( $destination ) ); ?></span></span>
      </div>
      <?php if ( $image['kind'] === 'logo' ) { ?>
        <div class="search-destination-tile search-destination-tile--logo ui-rounded-box" data-testid="search-destination-image">
          <?php echo wp_get_attachment_image( $image['attachment_id'], 'col12', false, array( 'class' => 'search-destination-tile__logo' ) ); ?>
        </div>
      <?php } elseif ( $image['kind'] === 'image' ) { ?>
        <?php
        echo wp_get_attachment_image(
          $image['attachment_id'],
          'col12-16to9',
          false,
          array(
            'class'       => 'ui-rounded-box u-display-block',
            'data-testid' => 'search-destination-image',
          )
        );
        ?>
      <?php } else { ?>
        <div class="search-destination-tile search-destination-tile--brand ui-rounded-box" data-testid="search-destination-image">
          <span class="search-destination-tile__name font-weight-bold"><?php echo esc_html( $destination['label'] ); ?></span>
        </div>
      <?php } ?>
    </div>
    <h5 class="index-post-title font-size-9 font-weight-bold mt-2 text-wrap-pretty"><?php echo esc_html( $destination['label'] ); ?></h5>
    <?php if ( ! empty( $destination['description'] ) ) { ?>
      <div class="font-size-9 mt-1"><?php echo esc_html( wp_trim_words( $destination['description'], 24 ) ); ?></div>
    <?php } ?>
  </a>
</article>
