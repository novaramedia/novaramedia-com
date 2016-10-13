<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title><?php wp_title('|',true,'right'); bloginfo('name'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php get_template_part('partials/seo'); ?>

  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />

  <?php get_template_part('partials/favicon'); ?>

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
            <div class="col col18">
              <ul class="u-inline-list u-inline-block">
                <li id="menu-toggle" class="u-pointer"><i class="icon-menu icon-large"></i></li>
                <li id="search-toggle" class="u-pointer"><i class="icon-search icon-large"></i></li>
              </ul>
              <?php
                if (is_single()) {
                  $author = get_post_meta($post->ID, '_cmb_author', true);
              ?>
              <span id="header-page-title" class="text-overflow-ellipsis u-inline-block"><?php
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
              <div class="col col6 text-align-right">
                <nav id="menu-logo" class="u-inline-block"><?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/img/dist/NM-logomark-white.svg'); ?></nav>
                <nav id="header-logotype" class="u-inline-block"<a href="<?php echo home_url(); ?>"><?php echo url_get_contents(get_bloginfo('stylesheet_directory') . '/img/dist/nm-logomark-dev1.svg'); ?></div>
              </div>
            </a>
          </div>
        </div>
      </div>

      <div id="header-sub" class="background-gray">
        <div class="container font-color-white padding-top-small padding-bottom-small">
          <div class="row">
            <div class="col col6">
              <ul class="header-menu u-inline-list">
                <li><a href="<?php echo get_category_link(get_category_by_slug('articles')); ?>">Articles</a></li>
                <li><a href="<?php echo get_category_link(get_category_by_slug('video')); ?>">Video</a></li>
                <li><a href="<?php echo get_category_link(get_category_by_slug('audio')); ?>">Audio</a></li>
              </ul>
            </div>
            <div class="col col6">
              <ul class="header-menu u-inline-list">
                <li><a href="<?php echo site_url('about/'); ?>">About</a></li>
                <li><a href="<?php echo site_url('pitching/'); ?>">Pitching</a></li>
                <li><a href="http://support.novaramedia.com">Support Us</a></li>
                <li><a href="<?php echo site_url('api/'); ?>">API</a></li>
              </ul>
            </div>

            <div class="col col6">
              <ul class="header-menu u-inline-list">
                <li><a href="https://twitter.com/novaramedia" target="_blank">Twitter</a></li>
                <li><a href="https://www.facebook.com/novaramedia/" target="_blank">Facebook</a></li>
                <li><a href="https://www.youtube.com/channel/UCOzMAa6IhV6uwYQATYG_2kg" target="_blank">YouTube</a></li>
                <li><a href="http://novaramedia.tumblr.com/" target="_blank">Tumblr</a></li>
              </ul>
            </div>

            <div class="col col6">
              <ul class="header-menu u-inline-list">
                <li><a href="http://podcast.novaramedia.com">Podcast</a></li>
                <li><a href="itpc://feeds.feedburner.com/NovaraMediaPodcast">Subscribe in iTunes</a></li>
                <li><a href="<?php bloginfo('rss2_url'); ?>">RSS</a></li>
              </ul>
            </div>

          </div>
        </div>
      </div>

      <section id="header-search">
        <?php get_search_form(); ?>
      </section>

    </header>
