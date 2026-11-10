<?php
/**
 * Search form at the top of the search results page, pre-filled with the
 * current query. Separate from searchform.php (header overlay), whose classes
 * and ids are bound by Header.js and Search.js.
 *
 * @since 4.11.0
 */

?>
<form role="search" method="get" class="grid-row mb-4" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-testid="search-results-form">
  <div class="grid-item is-xxl-24 u-visuallyhidden">
    <label for="search-results-input">Search this site</label>
  </div>
  <div class="grid-item is-s-20 is-xxl-22">
    <input id="search-results-input" class="ui-input" type="text" placeholder="Search" value="<?php echo esc_attr( get_search_query( false ) ); ?>" name="s" required aria-required="true">
  </div>
  <div class="grid-item is-s-4 is-xxl-2">
    <button type="submit" class="ui-button ui-button--fill-width" aria-label="Submit Search"><i class="icon-search"></i></button>
  </div>
</form>
