<?php
/**
 * Search suggestions: category archives, newsletters and site sections
 * matching the current search, shown as cards above the post results.
 *
 * @since 4.11.0
 */

$destinations = nm_get_search_destination_matches( get_search_query( false ) );

if ( empty( $destinations ) ) {
  return;
}
?>
<div data-testid="search-destinations">
  <div class="grid-row mb-3">
    <div class="grid-item is-xxl-24">
      <h4 class="font-size-10 font-weight-bold">Suggested</h4>
    </div>
  </div>
  <div class="grid-row mb-4">
    <?php
    foreach ( $destinations as $destination ) {
      get_template_part(
        'partials/post-layouts/archive-destination',
        null,
        array(
          'destination'       => $destination,
          'grid-item-classes' => 'grid-item is-s-24 is-l-12 is-xxl-8 mb-4',
        )
      );
    }
    ?>
  </div>
</div>
