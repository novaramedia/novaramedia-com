<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <title><?php wp_title('|',true,'right'); bloginfo('name'); ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
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
            <nav class="header-main__navigation col col4" role="navigation" aria-label="Main">
              <ul id="header-navs" class="u-inline-list u-inline-block">
                <li id="menu-toggle" class="u-pointer" role="button" tabindex="0" aria-controls="header-sub" aria-label="Sections Navigation" aria-haspopup="menu" aria-pressed="false"><i class="icon-menu icon-large"></i></li>
                <li id="search-toggle" class="u-pointer" role="button" tabindex="0" aria-controls="header-search" aria-label="Search" aria-haspopup="dialog" aria-pressed="false"><i class="icon-search icon-large"></i></li>
              </ul>
            </nav>
            
            <div class="header-main__middle col col16 text-align-center">
              <a href="<?php echo home_url(); ?>">
                <nav id="header-main__logotype" class="u-inline-block"><?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/logotype-2-white-line.svg'); ?></nav>

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
              </a>
            </div>
                        
            <a href="<?php echo home_url('support/'); ?>">
              <div class="header-main__logomark col col4 text-align-right">
                <nav id="menu-logo-nm10" class="u-inline-block"><?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/dist/img/nm10-logo.svg'); ?></nav>
              </div>
            </a>
          </div>
        </div>
      </div>

      <nav id="header-sub" class="background-gray" role="navigation" aria-label="Sections">
        <div class="container font-color-white padding-top-small padding-bottom-small">
          <div class="row">
            <div class="col col24">
              <ul class="header-menu u-inline-list text-align-center font-tracking-medium">
                <li><a href="<?php echo site_url(); ?>">Front Page</a></li>
                <li><a href="<?php echo get_category_link(get_category_by_slug('articles')); ?>">Articles</a></li>
                <li><a href="<?php echo get_category_link(get_category_by_slug('video')); ?>">Video</a></li>
                <li><a href="<?php echo get_category_link(get_category_by_slug('audio')); ?>">Audio</a></li>
                <li><a href="<?php echo site_url('newsletters/'); ?>">Newsletters</a></li>
                <li><a href="<?php echo site_url('support/'); ?>">Support Us</a></li>
                <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
                <li><a href="https://shop.novaramedia.com">Shop</a></li>
                <li><a href="https://donate.novaramedia.com/login">Log In</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <section id="header-search" role="dialog" aria-label="Search">
        <?php get_search_form(); ?>
      </section>

    </header>
