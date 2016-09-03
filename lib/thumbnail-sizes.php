<?php

if( function_exists( 'add_theme_support' ) ) {
  add_theme_support( 'post-thumbnails' );
}

if( function_exists( 'add_image_size' ) ) {
  add_image_size( 'admin-thumb', 150, 150, false );
  add_image_size( 'opengraph', 1200, 630, true );

  add_image_size( 'mobile-16to9', 460, 259, true );
  add_image_size( 'mobile-21to9', 460, 197, true );

  add_image_size( 'col4', 204, 9999, false );
  add_image_size( 'col4-16to9', 204, 114, true );

  add_image_size( 'col6-16to9', 314, 176, true );

  add_image_size( 'col8-16to9', 424, 238, true );

  add_image_size( 'col12-1to2point3', 664, 288, true );
  add_image_size( 'col12-16to9', 664, 373, true );

  add_image_size( 'col24-16to9', 1304, 733, true );
  add_image_size( 'col24-widescreen-crop', 1304, 400, true );

  add_image_size( 'col24-featured-crop', 1304, 550, true );
  add_image_size( 'col24-mobile-featured-crop', 1304, 550, true );
}