    <footer id="footer" class="background-black font-color-white padding-top-basic padding-bottom-large font-uppercase font-bold font-leading-wider">
      <div class="container">
        <div class="row">
          <div class="col col6 mobile-margin-bottom-basic">
            <ul>
              <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
              <li><a href="<?php echo site_url('support/'); ?>">Support Us</a></li>
              <li><a href="https://payment.novaramedia.com/profile">Manage subscription</a></li>
              <li><a href="https://shop.novaramedia.com">Shop</a></li>
              <li><a href="<?php echo site_url('pitching/'); ?>">Pitching</a></li>
              <li><a href="<?php echo site_url('api/'); ?>">API</a></li>
              <li><a href="http://podcast.novaramedia.com">Podcast</a></li>
              <li><a href="itpc://feeds.feedburner.com/NovaraMediaPodcast">Subscribe in iTunes</a></li>
              <li><a href="<?php bloginfo('rss2_url'); ?>">RSS</a></li>
              <li><a href="<?php echo site_url('terms-and-conditions/'); ?>">Terms & Conditions</a></li>
              <li><a href="<?php echo site_url('privacy-policy/'); ?>">Privacy Policy</a></li>
            </ul>
          </div>
          <div class="col col6 mobile-margin-bottom-basic">
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
              <li><a href="<?php echo get_post_type_archive_link('event'); ?>">Events</a></li>
            </ul>
          </div>
          <div class="col col6">
            <ul>
              <li><a href="https://twitter.com/novaramedia" target="_blank">Twitter</a></li>
              <li><a href="https://www.facebook.com/novaramedia/" target="_blank">Facebook</a></li>
              <li><a href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank">YouTube</a></li>
              <li><a href="https://www.instagram.com/novaramedia/" target="_blank">Instagram</a></li>
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
    get_template_part('partials/gdpr');
    get_template_part('partials/scripts');
    get_template_part('partials/schema-org');
  ?>

  </body>
</html>