    <footer id="footer" role="contentinfo" aria-label="Footer" class="background-black font-color-white padding-top-basic padding-bottom-large font-uppercase font-bold font-leading-wider">
      <div class="container">
        <div class="row">
          <div class="col col6 mobile-margin-bottom-basic">
            <ul>
              <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
              <li><a href="<?php echo site_url('support/'); ?>">Support Us</a></li>
              <li><a href="https://donate.novaramedia.com/profile">Manage Donation</a></li>
              <li><a href="https://shop.novaramedia.com">Merch Shop</a></li>
              <li><a href="<?php echo site_url('pitching/'); ?>">Pitching</a></li>
              <li><a href="<?php echo site_url('jobs/'); ?>">Jobs</a></li>
              <li><a href="http://podcast.novaramedia.com">Podcast</a></li>
              <li><a href="<?php bloginfo('rss2_url'); ?>">RSS</a></li>
              <li><a href="<?php echo site_url('terms-and-conditions/'); ?>">Terms & Conditions</a></li>
              <li><a href="<?php echo site_url('privacy-policy/'); ?>">Privacy Policy</a></li>
            </ul>
          </div>
          <div class="col col6 mobile-margin-bottom-basic">
            <?php
              wp_nav_menu(
    array(
                  'theme_location' => 'footer-2',
                  'fallback_cb' => false,
                )
);
            ?>
          </div>
          <div class="col col6">
            <?php
              function footerMenuFallback()
              {
                  ?><ul>
                <li><a href="https://twitter.com/novaramedia" target="_blank" rel="noopener">Twitter</a></li>
                <li><a href="https://www.facebook.com/novaramedia/" target="_blank" rel="noopener">Facebook</a></li>
                <li><a href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="noopener">YouTube</a></li>
                <li><a href="https://www.instagram.com/novaramedia/" target="_blank" rel="noopener">Instagram</a></li>
                <li><a href="https://t.me/novaramedia" target="_blank" rel="noopener">Telegram</a></li>
              </ul>
            <?php
              }

              wp_nav_menu(
                  array(
                  'theme_location' => 'footer-3',
                  'fallback_cb' => 'footerMenuFallback',
                )
              );
            ?>
          </div>
          <div class="col col6">
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-4',
                  'fallback_cb' => false,
                )
            );
            ?>
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
