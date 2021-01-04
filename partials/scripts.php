<section id="scripts">
  <?php
    wp_footer();

    if (is_single() && in_category('Articles')) {
  ?>
    <script type="text/javascript" src="https://d1xnn692s7u6t6.cloudfront.net/widget.js"></script>
    <script type="text/javascript">(function k(){window.$SendToKindle&&window.$SendToKindle.Widget?$SendToKindle.Widget.init({"content":"#single-articles-copy","title":"#single-articles-title","author":"#single-articles-author","published":"#single-articles-publication-date"}):setTimeout(k,500);})();</script>
  <?php
    }

  ?>
</section>