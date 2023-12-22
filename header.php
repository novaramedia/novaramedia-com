<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <title><?php wp_title('|',true,'right'); bloginfo('name'); ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="dns-prefetch" href="https://googletagmanager.com"/>

  <link rel="preconnect" href="https://use.typekit.net" crossorigin />
  <link rel="preconnect" href="https://p.typekit.net" crossorigin />
  <link rel="preload" as="style" href="https://use.typekit.net/aki7elm.css" />
  <link rel="stylesheet" href="https://use.typekit.net/aki7elm.css">

  <?php
    get_template_part('partials/header/google-tag-manager');
    get_template_part('partials/header/seo');
    get_template_part('partials/header/favicon');
    get_template_part('partials/header/feature-detect');
  ?>
  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
  <?php if (is_singular() && pings_open(get_queried_object())) { ?>
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
  <?php } ?>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <section id="main-container">
    <header class="site-header margin-bottom-basic">
      <div class="site-header__wrapper background-black font-color-white fs-4-sans fs-s-2">
        <div class="site-header__main container pt-2 pb-2">
          <div class="grid-row">
            <nav class="grid-item is-xxl-6" role="navigation" aria-label="Main">
              <ul class="site-header__navigation u-inline-list u-inline-block">
                <li id="menu-toggle" class="u-pointer" role="button" tabindex="0" aria-controls="header-sub" aria-label="Site Navigation" aria-haspopup="menu" aria-pressed="false"><i class="icon-menu icon-large"></i></li>
                <li id="search-toggle" class="u-pointer" role="button" tabindex="0" aria-controls="header-search" aria-label="Search" aria-haspopup="dialog" aria-pressed="false"><i class="icon-search icon-large"></i></li>
              </ul>
            </nav>

            <div class="header-main__middle grid-item is-xxl-12 text-align-center">
              <a href="<?php echo home_url(); ?>">
                <nav class="site-header__logomark" class="u-inline-block"><?php echo nm_get_file('/dist/img/logomark-white.svg'); ?></nav>

                <div class="site-header__scroll-reveal">
                  <span class="site-header__scroll-reveal-text text-overflow-ellipsis"><?php
                  if (is_single()) {
                    $author = get_post_meta($post->ID, '_cmb_author', true);

                    the_title();
                    // if (!empty($author)) { // WOW FIX THIS!!!
                    //   echo ' by ' . $author;
                    // }
                  } else {
                    echo 'Novara Media';
                  }
                ?></span>
                </div>
              </a>
            </div>

            <div class="grid-item is-xxl-6 text-align-right font-weight-bold">
              <a href="<?php echo home_url('support'); ?>" class="only-desktop">Support Us</a>
            </div>
          </div>
        </div>
      </div>

      <nav id="header-sub" class="background-black" role="navigation" aria-label="Sections">
        <div class="container font-color-white pt-3 pb-3">
          <div class="flex-grid-row">
            <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
              <ul class="font-weight-bold mb-3">
                <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
                <li><a href="<?php echo site_url('support/'); ?>">Support Us</a></li>
                <li><a href="<?php echo site_url('about/how-were-funded/'); ?>">How We're Funded</a></li>
                <li><a href="https://shop.novaramedia.com">Merch Shop</a></li>
              </ul>
              <ul class="font-weight-bold mb-3">
                <li><a href="https://donate.novaramedia.com/profile">&#10142; Manage Donation</a></li>
              </ul>
            </div>
            <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
              <h6 class="font-weight-regular fs-3-sans font-uppercase mb-3">Shows</h6>
              <?php
                wp_nav_menu(
                  array(
                    'theme_location' => 'header-shows',
                    'fallback_cb' => false,
                    'menu_class' => 'font-weight-bold mb-3'
                  )
                );
              ?>
            </div>
            <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
              <h6 class="font-weight-regular fs-3-sans font-uppercase mb-3">Series</h6>
              <?php
                wp_nav_menu(
                  array(
                    'theme_location' => 'header-series',
                    'fallback_cb' => false,
                    'menu_class' => 'font-weight-bold'
                  )
                );
              ?>
            </div>
            <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-basic">
              <h6 class="font-weight-regular fs-3-sans font-uppercase mb-3">Articles</h6>
              <?php
                wp_nav_menu(
                  array(
                    'theme_location' => 'footer-articles',
                    'fallback_cb' => false,
                    'menu_class' => 'font-weight-bold mb-3'
                  )
                );
              ?>
            </div>
          </div>
        </div>
      </nav>

      <section id="header-search" role="dialog" aria-label="Search">
        <?php get_search_form(); ?>
      </section>

    </header>
