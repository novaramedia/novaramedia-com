<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <title><?php wp_title('|',true,'right'); bloginfo('name'); ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com" />
  <link rel="dns-prefetch" href="https://googletagmanager.com"/>

  <?php get_template_part('partials/header/google-tag-manager'); ?>
  <?php get_template_part('partials/header/seo'); ?>

  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />

  <?php get_template_part('partials/header/favicon'); ?>

  <?php if (is_singular() && pings_open(get_queried_object())) { ?>
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
  <?php } ?>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<!--[if lt IE 9]><p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p><![endif]-->

  <section id="main-container">

    <header id="header" class="margin-bottom-basic">

      <div id="header-main-wrapper">
        <div id="header-main" class="container font-color-white padding-top-small padding-bottom-small">
          <div class="row">
            <div class="header-main__navigation col col4">
              <ul id="header-navs" class="u-inline-list u-inline-block">
                <li id="menu-toggle" class="u-pointer"><i class="icon-menu icon-large"></i></li>
                <li id="search-toggle" class="u-pointer"><i class="icon-search icon-large"></i></li>
              </ul>
            </div>
            
            <div class="header-main__middle col col16 text-align-center">

              <a href="<?php echo home_url(); ?>">
                <nav id="header-main__logotype" class="u-inline-block"><?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/logotype-2-white-line.svg'); ?></nav>
              </a>

              
              <?php
                if (is_single()) {
                  $author = get_post_meta($post->ID, '_cmb_author', true);
              ?>
              <span id="header-main__page-title" class="text-overflow-ellipsis u-inline-block"><?php
                the_title();
                if (!empty($author)) {
                  echo ' by ' . $author;
                }
              ?></span>
              <?php
                }
              ?>
            </div>
                        
            <a href="<?php echo home_url(); ?>">
              <div class="header-main__logomark col col4 text-align-right">
                <nav id="menu-logo" class="u-inline-block"><?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/logomark-white.svg'); ?></nav>
              </div>
            </a>
          </div>
        </div>
      </div>

      <div id="header-sub" class="background-gray">
        <div class="container font-color-white padding-top-small padding-bottom-small">
          <div class="row">
            <div class="col col24">
              <ul class="header-menu u-inline-list text-align-center font-tracking-medium">
                <li><a href="<?php echo get_category_link(get_category_by_slug('articles')); ?>">Articles</a></li>
                <li><a href="<?php echo get_category_link(get_category_by_slug('video')); ?>">Video</a></li>
                <li><a href="<?php echo get_category_link(get_category_by_slug('audio')); ?>">Audio</a></li>
                <li><a href="<?php echo site_url('support/'); ?>">Support Us</a></li>
                <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
                <li><a href="https://shop.novaramedia.com">Shop</a></li>
                <li><a href="https://payment.novaramedia.com/login">Log In</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <section id="header-search">
        <?php get_search_form(); ?>
      </section>

    </header>
