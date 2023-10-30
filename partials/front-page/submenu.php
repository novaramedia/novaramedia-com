<section class="front-page__submenu container layout-grid mt-2 mb-2">
  <div class="submenu__date">
    <?php
      $m = new \Moment\Moment('now', 'Europe/London');
      echo $m->format('l j F Y');
    ?>
  </div>

  <div class="submenu__nav">
    <?php
      wp_nav_menu(
        array(
          'theme_location' => 'header-submenu',
          'fallback_cb' => false,
          'menu_class' => 'submenu__nav-list',
        )
      );
    ?>
  </div>

  <div class="submenu__message">
    Novara is Live
  </div>
</section>
