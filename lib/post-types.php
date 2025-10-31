<?php
/**
 * Register the Contributor custom post type.
 *
 * @return void
 */
function nm_register_post_type_contributor() {
  $args = array(
    'label'               => 'Contributors',
    'labels'              => array(
      'menu_name'          => 'Contributors',
      'name_admin_bar'     => 'Contributor',
      'add_new'            => 'Add Contributor',
      'add_new_item'       => 'Add new Contributor',
      'new_item'           => 'New Contributor',
      'edit_item'          => 'Edit Contributor',
      'view_item'          => 'View Contributor',
      'update_item'        => 'Update Contributor',
      'all_items'          => 'All Contributors',
      'search_items'       => 'Search Contributors',
      'parent_item_colon'  => 'Parent Contributor',
      'not_found'          => 'No Contributors found',
      'not_found_in_trash' => 'No Contributors found in Trash',
      'name'               => 'Contributors',
      'singular_name'      => 'Contributor',
    ),
    'public'              => true,
    'exclude_from_search' => false,
    'menu_position'       => 10,
    'menu_icon'           => 'dashicons-universal-access-alt',
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'show_in_rest'        => true,
    'capability_type'     => 'post',
    'hierarchical'        => false,
    'has_archive'         => false,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite'             => true,
    'rewrite_no_front'    => false,
    'show_in_menu'        => true,
    'supports'            => array(
      'title',
      'editor',
      'thumbnail',
    ),
  );

  register_post_type( 'contributor', $args );
}
add_action( 'init', 'nm_register_post_type_contributor', 0 );

/**
 * Register the Newsletter custom post type.
 *
 * @return void
 */
function nm_register_post_type_newsletter() {
  $args = array(
    'label'               => 'Newsletters',
    'labels'              => array(
      'menu_name'          => 'Newsletters',
      'name_admin_bar'     => 'Newsletter',
      'add_new'            => 'Add Newsletter',
      'add_new_item'       => 'Add new Newsletter',
      'new_item'           => 'New Newsletter',
      'edit_item'          => 'Edit Newsletter',
      'view_item'          => 'View Newsletter',
      'update_item'        => 'Update Newsletter',
      'all_items'          => 'All Newsletters',
      'search_items'       => 'Search Newsletters',
      'parent_item_colon'  => 'Parent Newsletter:',
      'not_found'          => 'No Newsletters found',
      'not_found_in_trash' => 'No Newsletters found in Trash',
      'name'               => 'Newsletters',
      'singular_name'      => 'Newsletter',
    ),
    'public'              => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'show_in_rest'        => true,
    'menu_icon'           => 'dashicons-email',
    'capability_type'     => 'post',
    'hierarchical'        => false,
    'has_archive'         => true,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite'             => array(
      'slug'  => 'newsletters',
      'feeds' => false,
    ),
    'show_in_menu'        => true,
    'supports'            => array(
      'title',
      'editor',
      'thumbnail',
    ),
  );

  register_post_type( 'newsletter', $args );
}
add_action( 'init', 'nm_register_post_type_newsletter', 0 );

/**
 * Register the Event custom post type.
 *
 * @return void
 */
function event_post_type() {
  $labels = array(
    'name'                  => 'Events',
    'singular_name'         => 'Event',
    'menu_name'             => 'Events',
    'name_admin_bar'        => 'Event',
    'archives'              => 'Event Archives',
    'attributes'            => 'Event Attributes',
    'parent_item_colon'     => 'Parent Event:',
    'all_items'             => 'All Events',
    'add_new_item'          => 'Add New Event',
    'add_new'               => 'Add New',
    'new_item'              => 'New Event',
    'edit_item'             => 'Edit Event',
    'update_item'           => 'Update Event',
    'view_item'             => 'View Event',
    'view_items'            => 'View Events',
    'search_items'          => 'Search Event',
    'not_found'             => 'Not found',
    'not_found_in_trash'    => 'Not found in Trash',
    'featured_image'        => 'Featured Image',
    'set_featured_image'    => 'Set featured image',
    'remove_featured_image' => 'Remove featured image',
    'use_featured_image'    => 'Use as featured image',
    'insert_into_item'      => 'Insert into item',
    'uploaded_to_this_item' => 'Uploaded to this item',
    'items_list'            => 'Items list',
    'items_list_navigation' => 'Items list navigation',
    'filter_items_list'     => 'Filter items list',
  );
  $args = array(
    'label'               => 'Event',
    'description'         => 'IRL Events',
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor', 'thumbnail' ),
    'taxonomies'          => array( 'post_tag' ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_icon'           => 'dashicons-calendar',
    'menu_position'       => null,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'show_in_rest'        => true,
  );
  register_post_type( 'event', $args );
}
add_action( 'init', 'event_post_type', 0 );

/**
 * Register the Notice custom post type.
 *
 * @return void
 */
function notices_post_type() {
  $labels = array(
    'name'                  => 'Notices',
    'singular_name'         => 'Notice',
    'menu_name'             => 'Notices',
    'name_admin_bar'        => 'Notices',
    'archives'              => 'Item Archives',
    'parent_item_colon'     => 'Parent Notice:',
    'all_items'             => 'All Notices',
    'add_new_item'          => 'Add New Notice',
    'add_new'               => 'Add Notice',
    'new_item'              => 'New Notice',
    'edit_item'             => 'Edit Notice',
    'update_item'           => 'Update Notice',
    'view_item'             => 'View Notice',
    'search_items'          => 'Search Notice',
    'not_found'             => 'Not found',
    'not_found_in_trash'    => 'Not found in Trash',
    'featured_image'        => 'Featured Image',
    'set_featured_image'    => 'Set featured image',
    'remove_featured_image' => 'Remove featured image',
    'use_featured_image'    => 'Use as featured image',
    'insert_into_item'      => 'Insert into item',
    'uploaded_to_this_item' => 'Uploaded to this item',
    'items_list'            => 'Items list',
    'items_list_navigation' => 'Items list navigation',
    'filter_items_list'     => 'Filter items list',
  );
  $args = array(
    'label'               => 'Notice',
    'description'         => 'Simple notices with no archive',
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor', 'thumbnail' ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => null,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'show_in_rest'        => true,
  );
  register_post_type( 'notice', $args );
}
add_action( 'init', 'notices_post_type', 0 );

/**
 * Register the Job custom post type.
 *
 * @return void
 */
function nm_register_post_type_job() {
  $args = array(
    'label'               => 'Jobs',
    'labels'              => array(
      'menu_name'          => 'Jobs',
      'name_admin_bar'     => 'Job',
      'add_new'            => 'Add Job',
      'add_new_item'       => 'Add new Job',
      'new_item'           => 'New Job',
      'edit_item'          => 'Edit Job',
      'view_item'          => 'View Job',
      'update_item'        => 'View Job',
      'all_items'          => 'All Jobs',
      'search_items'       => 'Search Jobs',
      'parent_item_colon'  => 'Parent Job:',
      'not_found'          => 'No Jobs found',
      'not_found_in_trash' => 'No Jobs found in Trash',
      'name'               => 'Jobs',
      'singular_name'      => 'Job',
    ),
    'public'              => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'show_in_rest'        => true,
    'capability_type'     => 'post',
    'hierarchical'        => false,
    'has_archive'         => true,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite_no_front'    => false,
    'show_in_menu'        => true,
    'supports'            => array(
      'title',
      'editor',
      'thumbnail',
    ),

    'rewrite'             => true,
  );

  register_post_type( 'job', $args );
}
add_action( 'init', 'nm_register_post_type_job', 0 );
