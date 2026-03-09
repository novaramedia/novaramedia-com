    <footer id="footer" role="contentinfo" aria-label="Footer" class="background-black font-color-white pt-5 pb-8 text-uppercase ui-hover-links-inside" data-testid="site-footer">
      <div class="container">
        <div class="grid-row">
          <div class="grid-item is-s-12 is-xxl-6 mb-5">
            <h6 class="mb-4">NM</h6>
            <ul class="font-weight-bold mb-4">
              <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
              <li><a href="<?php echo site_url('support/'); ?>">Support Us</a></li>
              <li><a href="<?php echo site_url('about/how-we-are-funded/'); ?>">How We Are Funded</a></li>
              <li><a href="https://shop.novaramedia.com">Merch Shop</a></li>
              <li><a href="<?php echo site_url('pitching/'); ?>">Pitching</a></li>
              <li><a href="<?php echo site_url('jobs/'); ?>">Jobs</a></li>
            </ul>
            <ul class="font-weight-bold mb-4">
              <li><a href="https://donate.novaramedia.com/profile">&#10142; Manage Donation</a></li>
            </ul>
            <ul class="font-weight-bold">
              <li><a href="<?php echo site_url('terms-and-conditions/'); ?>">Terms & Conditions</a></li>
              <li><a href="<?php echo site_url('privacy-policy/'); ?>">Privacy Policy</a></li>
            </ul>
          </div>
          <div class="grid-item is-s-12 is-xxl-6 mb-5">
            <h6 class="mb-4">Podcasts</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-podcasts',
                  'fallback_cb' => false,
                  'menu_class' => 'font-weight-bold mb-4'
                )
              );
            ?>
            <h6 class="mb-4">Focuses</h6>
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
          <div class="grid-item is-s-12 is-xxl-6 mb-5">
            <h6 class="mb-4">Articles</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-articles',
                  'fallback_cb' => false,
                  'menu_class' => 'font-weight-bold mb-4'
                )
              );
            ?>
            <h6 class="mb-4">Shows</h6>
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
          <div class="grid-item is-s-12 is-xxl-6 mb-5">
            <h6 class="mb-4">Social Media</h6>
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
