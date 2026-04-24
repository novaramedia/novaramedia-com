    <footer id="footer" role="contentinfo" aria-label="Footer" class="background-black font-color-white pt-5 pb-8 text-uppercase ui-hover-links-inside" data-testid="site-footer">
      <div class="container">
        <div class="grid-row">
          <div class="grid-item is-s-12 is-xxl-6 mb-5">
            <h6 class="mb-4">NM</h6>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-general',
                  'fallback_cb' => 'nm_footer_general_fallback',
                  'menu_class' => 'font-weight-bold mb-4'
                )
              );
            ?>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'manage-donation',
                  'fallback_cb' => 'nm_manage_donation_fallback',
                  'menu_class' => 'font-weight-bold mb-4'
                )
              );
            ?>
            <?php
              wp_nav_menu(
                array(
                  'theme_location' => 'footer-legal',
                  'fallback_cb' => 'nm_footer_legal_fallback',
                  'menu_class' => 'font-weight-bold'
                )
              );
            ?>
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
