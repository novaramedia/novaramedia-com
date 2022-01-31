
<?php
  $term = get_term_by('slug', 'doing-it-right-sex-on-the-left', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="doing-it-right-sex-on-the-left__container padding-top-basic padding-bottom-basic margin-bottom-large">
  <style type="text/css">
    .doing-it-right-sex-on-the-left__container {
      overflow: hidden;
      position: relative;
      background-color: #EBB800;
      outline: 1px black solid;
    }

    .doing-it-right-sex-on-the-left__container .flex-grid-item {
      z-index: 20;
    }

    .doing-it-right-sex-on-the-left__typoblobs-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      overflow: hidden;
      z-index: 10;
      pointer-events: none;
    }

    .doing-it-right-sex-on-the-left__typoblob-1 {
      position: absolute;
      top: -80px;
      left: -150px;
    }

    .doing-it-right-sex-on-the-left__typoblob-2 {
      position: absolute;
      top: -100px;
      left: 30vw;
    }

    .doing-it-right-sex-on-the-left__typoblob-3 {
      position: absolute;
      top: -50px;
      right: -250px;
    }

    @media screen and (max-width: 1336px) {
      .doing-it-right-sex-on-the-left__typoblobs-container svg {
        transform: scale(.7)
      }

      .doing-it-right-sex-on-the-left__typoblob-1 {
        top: -80px;
        left: -150px;
      }
    }

    @media screen and (max-width: 1104px) {
      .doing-it-right-sex-on-the-left__typoblob-1 {
        left: -210px;
      }

      .doing-it-right-sex-on-the-left__typoblob-2 {
        display: none;
      }
    }

    @media screen and (max-width: 910px) {
      .doing-it-right-sex-on-the-left__typoblobs-container svg {
        transform: scale(.8)
      }

      .doing-it-right-sex-on-the-left__typoblob-3 {
        right: -250px;
      }
    }

    @media screen and (max-width: 759px) {

    }
  }
  </style>

  <div class="container">
    <div class="doing-it-right-sex-on-the-left__typoblobs-container">
      <div class="doing-it-right-sex-on-the-left__typoblob-1">
        <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-sex-typoblob-1.svg'); ?>
      </div>
      <div class="doing-it-right-sex-on-the-left__typoblob-2">
        <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-sex-typoblob-2.svg'); ?>
      </div>
      <div class="doing-it-right-sex-on-the-left__typoblob-3">
        <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-sex-typoblob-3.svg'); ?>
      </div>
    </div>

    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <a href="<?php echo $url; ?>"><h4>Focus</h4></a>
      </div>
    </div>
    <div class="flex-grid-row padding-bottom-small">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-5 mobile-margin-bottom-small">
        <a href="<?php echo $url; ?>"><h3 class="font-size-s-4 font-size-l-4 font-size-4" style="margin-left: -.05em;">
          Doing It Right:<br/>Sex On The Left</h3></a>
      </div>
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-xxl-1 flex-item-xxl-6">
        <a href="<?php echo $url; ?>"><p class="font-size-2 font-bold">Sex can be disastrous, delightful or extremely okay, but it is always slippery as f***. From bi erasure to ethical ghosting, male circumcision to family abolition, we ask: what does leftist sex look like? And can we ever do it right?</p></a>
        <a href="<?php echo $url; ?>" class="nm-button nm-button--white nm-button--inline font-color-black">Explore the Focus</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
