
<?php
  $term = get_term_by('slug', 'focus-slug-goes-here', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="**banner**name**__container pt-4 pb-4">
  <style type="text/css">
    .**banner**name**__container {
      overflow: hidden;
      position: relative;
      background-color: #EBB800;
      background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/**banner**name**-banner-background.svg'; ?>); // should use webp and avif and fallback
      background-size: cover;
      background-position: bottom;
    }

    @media screen and (max-width: 1336px) { // maybe this should be a css variable?

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
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <a href="<?php echo $url; ?>"><h4>Focus</h4></a>
      </div>
    </div>
    <div class="grid-row pb-4">
      <div class="grid-item is-xxl-24 is-m-8 is-xxl-10 mb-s-3">
        <a href="<?php echo $url; ?>">
          <h3 class="fs-6">Title Text Here or SVG asset</h3>
        </a>
      </div>
      <div class="grid-item is-xxl-24 is-m-8 is-xxl-10">
        <a href="<?php echo $url; ?>"><p class="fs-5-sans font-bold">Strapline here</p></a>
        <a href="<?php echo $url; ?>" class="ui-button ui-button--white">CTA Button</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
