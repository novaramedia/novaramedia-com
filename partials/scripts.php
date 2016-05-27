<section id="scripts">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php bloginfo('stylesheet_directory'); ?>/js/vendor/jquery-2.1.1.min.js"><\/script>')</script>
  <?php
    wp_footer();

    if (is_single() && in_category('Wire')) {
  ?>
    <script type="text/javascript" src="https://d1xnn692s7u6t6.cloudfront.net/widget.js"></script>
    <script type="text/javascript">(function k(){window.$SendToKindle&&window.$SendToKindle.Widget?$SendToKindle.Widget.init({"content":"#single-wire-copy","title":"#single-wire-title","author":"#single-wire-author","published":"#single-wire-publication-date"}):setTimeout(k,500);})();</script>
  <?php
    }

  ?>
</section>
