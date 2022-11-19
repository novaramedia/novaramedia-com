
<?php
  $term = get_term_by('slug', 'focus-slug-goes-here', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="**banner**name**__container padding-top-basic padding-bottom-basic">
  <style type="text/css">
    .**banner**name**__container {
      overflow: hidden;
      position: relative;
      background-color: #EBB800;
      background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/**banner**name**-banner-background.svg'; ?>);
      background-size: cover;
      background-position: bottom;
    }

    @media screen and (max-width: 1336px) {

    }

    @media screen and (max-width: 1104px) {

    }

    @media screen and (max-width: 910px) {

    }

    @media screen and (max-width: 759px) {

    }
  }
  </style>

  <div class="container">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <a href="<?php echo $url; ?>"><h4>Focus</h4></a>
      </div>
    </div>
    <div class="flex-grid-row padding-bottom-small">
      <div class="flex-grid-item flex-item-s-12 flex-item-m-4 flex-item-xxl-5 mobile-margin-bottom-small">
        <a href="<?php echo $url; ?>">
          <h3 class="font-size-3">Title Text Here or SVG asset</h3>
        </a>
      </div>
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-m-1 flex-item-m-7 flex-offset-xxl-1 flex-item-xxl-6">
        <a href="<?php echo $url; ?>"><p class="font-size-2 font-bold">Strapline here</p></a>
        <a href="<?php echo $url; ?>" class="nm-button nm-button--white nm-button--inline font-color-black">CTA Button</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
