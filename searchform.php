<div class="background-black pt-4 pb-5 fs-5-sans">
  <div class="container">
    <form role="search" method="get" id="search-form" class="site-header-search__form" action="<?php echo site_url(); ?>" autocomplete="off">
      <div class="grid-row">
        <div class="grid-item is-xxl-24 u-visuallyhidden">
          <label for="search-input">Search this site</label>
        </div>

        <div class="grid-item is-xxl-22">
          <input id="search-input" class="site-header-search__input font-color-white" type="text" placeholder="Search" value="" name="s" required aria-required="true">
        </div>
        <div class="grid-item is-xxl-2">
          <button type="submit" id="search-submit" class="site-header-search__submit nm-button" role="button" aria-label="Submit Search"><i class="icon-search"></i></button>
        </div>
      </div>
    </form>
  </div>
</div>
