<section id="scripts">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php bloginfo('stylesheet_directory'); ?>/js/vendor/jquery-3.4.1.min.js"><\/script>')</script>
  <?php
    wp_footer();

    if (is_single() && in_category('Articles')) {
  ?>
    <script type="text/javascript" src="https://d1xnn692s7u6t6.cloudfront.net/widget.js"></script>
    <script type="text/javascript">(function k(){window.$SendToKindle&&window.$SendToKindle.Widget?$SendToKindle.Widget.init({"content":"#single-articles-copy","title":"#single-articles-title","author":"#single-articles-author","published":"#single-articles-publication-date"}):setTimeout(k,500);})();</script>
  <?php
    }

  ?>

  <script>
    if (Cookies.get('gdpr-approval')) {
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-37044100-1', 'auto');
      ga('send', 'pageview');
    }
  </script>


</section>
