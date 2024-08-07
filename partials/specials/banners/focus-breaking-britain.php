<?php
  $term = get_term_by('slug', 'breaking-britain', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="pt-6 pb-6 background-neon-blue font-color-white" style="overflow: hidden; position: relative">
  <div class="breaking-britain__bars-container" style="position: absolute; top: 0; width: 100%; height: 100vh; overflow: hidden; z-index: 10; pointer-events: none; ">
    <div class="breaking-britain__bar-1">
      <?php echo nm_get_file('/dist/img/specials/breaking-britain/focus-breakingbritain-line-1.svg'); ?>
    </div>
    <div class="breaking-britain__bar-2">
      <?php echo nm_get_file('/dist/img/specials/breaking-britain/focus-breakingbritain-line-2.svg'); ?>
    </div>
    <div class="breaking-britain__bar-3">
      <?php echo nm_get_file('/dist/img/specials/breaking-britain/focus-breakingbritain-line-3.svg'); ?>
    </div>
  </div>
  <style type="text/css">
    .breaking-britain__bar-1 {
      position: absolute;
      top: 40px;
      left: -400px;
      animation: breakingbars 13131ms ease-in-out 111ms infinite normal forwards;
    }
    .breaking-britain__bar-2 {
      position: absolute;
      top: -250px;
      left: 27vw;
      animation: breakingbars 22222ms ease-in-out 77ms infinite normal forwards;
    }
    .breaking-britain__bar-3 {
      position: absolute;
      top: -50px;
      right: -250px;
      animation: breakingbars 11111ms ease-in-out 42ms infinite normal forwards;
    }
    @media screen and (max-width: 1336px) {
      .breaking-britain__bars-container svg {
        transform: scale(.6)
      }
    }
    @media screen and (max-width: 1104px) {
      .breaking-britain__bars-container svg {
        transform: scale(.5)
      }
      .breaking-britain__bar-2 {
        top: -300px;
        left: 22vw;
      }
    }
    @media screen and (max-width: 910px) {
      .breaking-britain__bars-container svg {
        transform: scale(.3)
      }
    }
    @media screen and (max-width: 759px) {
      .breaking-britain__bars-container svg {
        transform: scale(.2)
      }
      .breaking-britain__bar-1 {
        top: -50px;
      }
      .breaking-britain__bar-2 {
        top: -250px;
      }
      .breaking-britain__bar-3 {
        top: 50px;
      }
    }
    @keyframes breakingbars {
    0% {
      transform: translate(0) rotate(0deg);
    }
    20% {
      transform: translate(-10px, 10px) rotate(-2deg);
    }
    40% {
      transform: translate(-10px, -10px);
    }
    60% {
      transform: translate(10px, 10px) rotate(2deg);
    }
    80% {
      transform: translate(10px, -10px);
    }
    100% {
      transform: translate(0) rotate(0deg);
    }
  }
  </style>
  <div class="container">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <a href="<?php echo $url; ?>"><h4 class="font-size-9 text-uppercase font-weight-bold">Focus</h4></a>
      </div>
    </div>
    <div class="grid-row">
      <div class="grid-item is-s-24 is-xxl-12 mb-s-3">
        <a href="<?php echo $url; ?>"><h3 class="font-size-15 font-weight-bold" style="margin-left: -.05em;">Breaking<br/>Britain.</h3></a>
      </div>
      <div class="grid-item is-s-24 is-xxl-12">
        <a href="<?php echo $url; ?>"><p class="font-size-11 mb-4">With growing rifts between its regions, how much longer can the UK survive? From the economic to the constitutional, this focus explores the divisions and contradictions within the union today.</p></a>
        <a href="<?php echo $url; ?>" class="ui-button ui-button--white">Read more</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
