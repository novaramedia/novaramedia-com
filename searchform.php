<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="pt-4 pb-5 font-size-11">
  <div class="container">
    <form role="search" method="get" id="search-form" class="site-header-search__form" action="<?php echo esc_url( site_url() ); ?>" autocomplete="off">
      <div class="grid-row">
        <div class="grid-item is-xxl-24 u-visuallyhidden">
          <label for="search-input">Search this site</label>
        </div>

        <div class="grid-item is-s-20 is-xxl-22">
          <input id="search-input" class="site-header-search__input ui-input" type="text" placeholder="Search" value="" name="s" required aria-required="true">
        </div>
        <div class="grid-item is-s-4 is-xxl-2">
          <button type="submit" id="search-submit" class="site-header-search__submit ui-button ui-button--fill-width" role="button" aria-label="Submit Search"><i class="icon-search"></i></button>
        </div>
      </div>
    </form>
  </div>
</div>
