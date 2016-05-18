    <footer id="footer" class="background-black font-color-white margin-top-basic padding-top-basic padding-bottom-large font-uppercase font-bold font-leading-wider">
      <div class="container">
        <div class="row">
          <div class="col col6">
            <ul>
              <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
              <li><a href="http://support.novaramedia.com">Support Us</a></li>
              <li><a href="<?php echo site_url('api/'); ?>">API</a></li>
              <li><a href="http://podcast.novaramedia.com">Podcast</a></li>
              <li><a href="itpc://feeds.feedburner.com/NovaraMediaPodcast">Subscribe in iTunes</a></li>
              <li><a href="<?php bloginfo('rss2_url'); ?>">RSS</a></li>
            </ul>
          </div>
          <div class="col col6">
            <ul>
            <?php
              // Outputs only the top level categories
              $categories = get_categories(array(
                'orderby' => 'name',
                'parent'  => 0
              ));
              foreach ($categories as $category) {
                echo '<li><a href="' . get_category_link($category) . '">' . $category->name . '</a></li>';
              }
            ?>
            </ul>
          </div>
          <div class="col col6">
            <ul>
              <li><a href="https://twitter.com/novaramedia" target="_blank">Twitter</a></li>
              <li><a href="https://www.facebook.com/novaramedia/" target="_blank">Facebook</a></li>
              <li><a href="https://www.youtube.com/channel/UCOzMAa6IhV6uwYQATYG_2kg" target="_blank">YouTube</a></li>
              <li><a href="http://novaramedia.tumblr.com/" target="_blank">Tumblr</a></li>
            </ul>
          </div>
          <div class="col col6">
            <ul>
            <?php
              // Outputs only the child categories of all the top level categories
              foreach ($categories as $category) {
                $children = get_categories(array(
                  'orderby' => 'name',
                  'parent'  => $category->term_id
                ));
                if ($children) {
                  foreach($children as $child) {
                    echo '<li><a href="' . get_category_link($child) . '">' . $child->name . '</a></li>';
                  }
                }
              }
            ?>
            </ul>
          </div>
        </div>
      </div>
    </footer>

  </section>

  <?php
    get_template_part('partials/scripts');
    get_template_part('partials/schema-org');
  ?>

  </body>
</html>