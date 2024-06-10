
<?php
  $term = get_term_by('slug', 'doing-it-right-sex-on-the-left', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="doing-it-right-sex-on-the-left__container pt-6 pb-6">
  <style type="text/css">
    .doing-it-right-sex-on-the-left__container {
      overflow: hidden;
      position: relative;
      background-color: #FF7EF6;
      outline: 1px black solid;
    }
    .doing-it-right-sex-on-the-left__container .grid-item {
      z-index: 20;
    }
    .doing-it-right-sex-on-the-left__blobs-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      overflow: hidden;
      z-index: 10;
      pointer-events: none;
    }
    .doing-it-right-sex-on-the-left__blob-1 {
      position: absolute;
      top: -150px;
      left: -150px;
    }
    .doing-it-right-sex-on-the-left__blob-2 {
      position: absolute;
      top: -150px;
      left: 33vw;
    }
    .doing-it-right-sex-on-the-left__blob-3 {
      position: absolute;
      top: -120px;
      right: -150px;
    }
    @media screen and (max-width: 1336px) {
      .doing-it-right-sex-on-the-left__blobs-container svg {
        transform: scale(.9)
      }
      .doing-it-right-sex-on-the-left__blob-1 {
        top: -110px;
        left: -150px;
      }
      .doing-it-right-sex-on-the-left__blob-2 {
        left: 30vw;
      }
    }
    @media screen and (max-width: 1104px) {
      .doing-it-right-sex-on-the-left__blob-1 {
        left: -180px;
      }
    }
    @media screen and (max-width: 910px) {
      .doing-it-right-sex-on-the-left__blobs-container svg {
        transform: scale(.9)
      }
      .doing-it-right-sex-on-the-left__blob-2 {
        display: none;
      }
      .doing-it-right-sex-on-the-left__blob-3 {
        right: -150px;
      }
    }
  }
  </style>
  <div class="container">
    <div class="doing-it-right-sex-on-the-left__blobs-container">
      <div class="doing-it-right-sex-on-the-left__blob-1">
        <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-sex-blob-1.svg'); ?>
      </div>
      <div class="doing-it-right-sex-on-the-left__blob-2">
        <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-sex-blob-2.svg'); ?>
      </div>
      <div class="doing-it-right-sex-on-the-left__blob-3">
        <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-sex-blob-3.svg'); ?>
      </div>
    </div>
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <a href="<?php echo $url; ?>"><h4 class="fs-3-sans font-uppercase font-bold">Focus</h4></a>
      </div>
    </div>
    <div class="grid-row pb-4">
      <div class="grid-item is-s-24 is-xxl-10 mb-s-3">
        <a href="<?php echo $url; ?>"><h3 class="fs-8 fs-s-7" style="margin-left: -.05em;">
          Doing It Right:<br/>Sex On The Left</h3></a>
      </div>
      <div class="grid-item offset-s-0 is-s-24 offset-xxl-2 is-xxl-12">
        <a href="<?php echo $url; ?>"><p class="fs-6 fs-s-5-sans mb-4">Sex can be disastrous, delightful or extremely okay, but it is always slippery as fuck. From bi erasure to ethical ghosting, male circumcision to family abolition, we ask: what does leftist sex look like? And can we ever do it right?</p></a>
        <a href="<?php echo $url; ?>" class="ui-button ui-button--white">Explore the Focus</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
