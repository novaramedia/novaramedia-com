<?php
  if (wp_get_nav_menu_object('header-submenu')) {
?>
<section class="front-page__submenu container container--padded mt-3 mb-4 mb-s-3 font-size-9 font-weight-bold">
  <div class="layout-grid ui-border-bottom pb-3">
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
      <div class="submenu__message__live" style="display: none">
        <a href="<?php echo site_url('novara-live'); ?>"><span class="ui-dot ui-dot--green"></span>Novara is Live</a>
      </div>
      <a class="submenu__message__offline" style="display: none" target="_blank" rel="nofollow"></a>
    </div>
  </div>
</section>
<?php
  }
?>
