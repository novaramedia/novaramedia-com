<?php
if( function_exists( 'add_image_size' ) ) {
  add_image_size( 'opengraph', 1200, 630, true );

  add_image_size( 'mobile-16to9', 460, 259, true );

  add_image_size( 'col4', 204, 9999, false );
  add_image_size( 'col4-square', 204, 204, true );
  add_image_size( 'col4-16to9', 204, 114, true );

  add_image_size( 'col6-16to9', 314, 176, true );
  add_image_size( 'col6-1to4', 314, 78, true );

  add_image_size( 'col8-16to9', 424, 238, true );

  add_image_size( 'col12', 664, 9999, false );
  add_image_size( 'col12-16to9', 664, 373, true );

  add_image_size( 'gallery-mid', 664, 373, false );

  add_image_size( 'col18-16to9', 974, 548, true );

  add_image_size( 'col24-16to9', 1304, 733, true );

  add_image_size( 'gallery', 1304, 733, false );

  // V4.0.0

  add_image_size( '12-square', 685, 685, true );
}
