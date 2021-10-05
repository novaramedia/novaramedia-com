<?php
  $term = get_term_by('slug', 'breaking-britain', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="padding-top-mid padding-bottom-mid margin-bottom-large background-neon-blue font-color-white" style="overflow: hidden; position: relative">
  <div class="breaking-britain__bars-container" style="position: absolute; top: 0; width: 100%; height: 100vh; overflow: hidden; z-index: 10; pointer-events: none; ">
    <div class="breaking-britain__bar-1">
      <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-1.svg'); ?>
    </div>
    <div class="breaking-britain__bar-2">
      <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-2.svg'); ?>
    </div>
    <div class="breaking-britain__bar-3">
      <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-3.svg'); ?>
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
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">  
        <a href="<?php echo $url; ?>"><h4>Focus</h4></a>
      </div>
    </div>
    <div class="flex-grid-row">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6 mobile-margin-bottom-small">  
        <a href="<?php echo $url; ?>"><h3 class="font-size-s-7 font-size-m-5 font-size-6" style="margin-left: -.05em;">Breaking<br/>Britain.</h3></a>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">  
        <a href="<?php echo $url; ?>"><p class="font-size-2">With growing rifts between its regions, how much longer can the UK survive? From the economic to the constitutional, this focus explores the divisions and contradictions within the union today.</p></a>
        <a href="<?php echo $url; ?>" class="nm-button nm-button--white nm-button--inline font-color-black">Read more</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>