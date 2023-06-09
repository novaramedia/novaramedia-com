    <footer id="footer" role="contentinfo" aria-label="Footer" class="background-black font-color-white padding-top-basic padding-bottom-large font-uppercase">
      <div class="container">
        <div class="flex-grid-row">
          <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
            <h6 class="font-weight-regular margin-bottom-small">NM</h6>
            <ul class="font-weight-bold margin-bottom-small">
              <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
              <li><a href="<?php echo site_url('support/'); ?>">Support Us</a></li>
              <li><a href="https://shop.novaramedia.com">Merch Shop</a></li>
              <li><a href="<?php echo site_url('pitching/'); ?>">Pitching</a></li>
              <li><a href="<?php echo site_url('jobs/'); ?>">Jobs</a></li>
            </ul>
            <ul class="font-weight-bold margin-bottom-small">
              <li><a href="https://donate.novaramedia.com/profile">&#10142; Manage Donation</a></li>
            </ul>
            <ul class="font-weight-bold">
              <li><a href="<?php echo site_url('terms-and-conditions/'); ?>">Terms & Conditions</a></li>
              <li><a href="<?php echo site_url('privacy-policy/'); ?>">Privacy Policy</a></li>
            </ul>
          </div>
          <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
            <h6 class="font-weight-regular margin-bottom-small">Podcasts</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-podcasts',
                  'fallback_cb' => false,
                  'menu_class' => 'font-weight-bold margin-bottom-small'
                )
              );
            ?>
            <h6 class="font-weight-regular margin-bottom-small">Focuses</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-focuses',
                  'fallback_cb' => false,
                  'menu_class' => 'font-weight-bold'
                )
              );
            ?>
          </div>
          <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
            <h6 class="font-weight-regular margin-bottom-small">Articles</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-articles',
                  'fallback_cb' => false,
                  'menu_class' => 'font-weight-bold margin-bottom-small'
                )
              );
            ?>
            <h6 class="font-weight-regular margin-bottom-small">Shows</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-shows',
                  'fallback_cb' => false,
                  'menu_class' => 'font-weight-bold'
                )
              );
            ?>
          </div>
          <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
            <h6 class="font-weight-regular margin-bottom-small">Social Media</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-social-media',
                  'fallback_cb' => false,
                  'menu_class' => 'font-weight-bold'
                )
              );
            ?>
          </div>
        </div>
      </div>
    </footer>

  </section>

  <?php
    get_template_part('partials/bottom-bar/bottom-bar');
    get_template_part('partials/scripts');
    get_template_part('partials/schema-org');
  ?>

  </body>
</html>
