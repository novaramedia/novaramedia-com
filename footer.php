<footer id="footer" class="font-white background-black">
  <div class="container">
    <div class="row">
      <div class="col col18">
        <ul class="u-inline-list font-smaller">
          <li><a href="<?php echo home_url('category/tv/'); ?>">Novara TV</a></li>
          <li><a href="<?php echo home_url('category/fm/'); ?>">Novara FM</a></li>
          <li><a href="http://wire.novaramedia.com">Novara Wire</a></li>
          <li><a href="<?php echo home_url('index/'); ?>">Index</a></li>
          <li><a href="https://twitter.com/novaramedia">Twitter</a></li>
          <li><a href="https://www.facebook.com/pages/NovaraMedia/404716342902872">Facebook</a></li>
          <li><a href="https://www.youtube.com/channel/UCOzMAa6IhV6uwYQATYG_2kg">Youtube</a></li>
          <li><a href="http://novaramedia.tumblr.com/">Tumblr</a></li>
          <li><a href="http://podcast.novaramedia.com">Podcast</a></li>
          <li><a href="itpc://feeds.feedburner.com/NovaraMediaPodcast">Subscribe in iTunes</a></li>
          <li><a href="http://fm.novaramedia.com">ArchiveFM</a></li>
          <li><a href="<?php bloginfo('rss2_url'); ?>">RSS</a></li>
       </ul>
      </div>
    </div>
  </div>
</footer>

<section id="scripts">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-2.1.1.min.js"><\/script>')</script>
<?php
wp_footer();
if(is_single()) {
?>
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=305272962896290";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>

  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<?php
}
?>
</section>
</body>
</html>