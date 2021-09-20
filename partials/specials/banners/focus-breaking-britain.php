<?php
  $term = get_term_by('slug', 'breaking-britain', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="padding-top-mid padding-bottom-mid margin-bottom-large background-neon-blue font-color-white" style="overflow: hidden; position: relative">
  <div style="position: absolute; top: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: 10; pointer-events: none; ">
    <div style="position: absolute; top: 0; left: -400px; animation: breakingbars 13131ms ease-in-out 111ms infinite normal forwards;">
      <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-1.svg'); ?>
    </div>
    <div style="position: absolute; top: -250px; left: 27vw; animation: breakingbars 22222ms ease-in-out 707ms infinite normal forwards;">
      <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-2.svg'); ?>
    </div>
    <div style="position: absolute; top: -50px; right: -250px; animation: breakingbars 11111ms ease-in-out 420ms infinite normal forwards;">
      <?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-breakingbritain-line-3.svg'); ?>
    </div>
  </div>
  
  <style type="text/css">
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
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">  
        <a href="<?php echo $url; ?>"><h3 class="font-size-neg1">Breaking<br/>Britain.</h3></a>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">  
        <a href="<?php echo $url; ?>"><p class="font-size-3">Will Britain as we know it survive the 21st Century? Novara ventures in the possibilities of a fragmented future in the new focus featuring __, ____ and ____.</p></a>
        <a href="<?php echo $url; ?>" class="nm-button nm-button--white nm-button--inline font-color-black">Read the Focus</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>