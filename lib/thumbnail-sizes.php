<?php

if( function_exists( 'add_theme_support' ) ) {
  add_theme_support( 'post-thumbnails' );
}

if( function_exists( 'add_image_size' ) ) {
  add_image_size( 'admin-thumb', 150, 150, false );
  add_image_size( 'opengraph', 1200, 630, true );

  add_image_size( 'col8-16to9', 432, 243, true );

  add_image_size( 'col24-wire-crop', 1328, 400, true );

  add_image_size( 'blank', 432, 243, true );

}