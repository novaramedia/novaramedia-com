<?php
function nm_register_post_type_contributor() {
  $args = [
    'label'  => esc_html__( 'Contributors', 'text-domain' ),
    'labels' => [
      'menu_name'          => esc_html__( 'Contributors', '' ),
      'name_admin_bar'     => esc_html__( 'Contributor', '' ),
      'add_new'            => esc_html__( 'Add Contributor', '' ),
      'add_new_item'       => esc_html__( 'Add new Contributor', '' ),
      'new_item'           => esc_html__( 'New Contributor', '' ),
      'edit_item'          => esc_html__( 'Edit Contributor', '' ),
      'view_item'          => esc_html__( 'View Contributor', '' ),
      'update_item'        => esc_html__( 'View Contributor', '' ),
      'all_items'          => esc_html__( 'All Contributors', '' ),
      'search_items'       => esc_html__( 'Search Contributors', '' ),
      'parent_item_colon'  => esc_html__( 'Parent Contributor', '' ),
      'not_found'          => esc_html__( 'No Contributors found', '' ),
      'not_found_in_trash' => esc_html__( 'No Contributors found in Trash', '' ),
      'name'               => esc_html__( 'Contributors', '' ),
      'singular_name'      => esc_html__( 'Contributor', '' ),
    ],
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
    'rewrite_no_front'    => false,
    'show_in_menu'        => true,
    'supports' => [
      'title',
      'editor',
      'thumbnail',
    ],

    'rewrite' => true
  ];

  register_post_type( 'contributor', $args );
}
add_action( 'init', 'nm_register_post_type_contributor', 0 );

function nm_register_post_type_newsletter() {
  $args = [
    'label'  => esc_html__( 'Newsletters', 'text-domain' ),
    'labels' => [
      'menu_name'          => esc_html__( 'Newsletters', '' ),
      'name_admin_bar'     => esc_html__( 'Newsletter', '' ),
      'add_new'            => esc_html__( 'Add Newsletter', '' ),
      'add_new_item'       => esc_html__( 'Add new Newsletter', '' ),
      'new_item'           => esc_html__( 'New Newsletter', '' ),
      'edit_item'          => esc_html__( 'Edit Newsletter', '' ),
      'view_item'          => esc_html__( 'View Newsletter', '' ),
      'update_item'        => esc_html__( 'View Newsletter', '' ),
      'all_items'          => esc_html__( 'All Newsletters', '' ),
      'search_items'       => esc_html__( 'Search Newsletters', '' ),
      'parent_item_colon'  => esc_html__( 'Parent Newsletter', '' ),
      'not_found'          => esc_html__( 'No Newsletters found', '' ),
      'not_found_in_trash' => esc_html__( 'No Newsletters found in Trash', '' ),
      'name'               => esc_html__( 'Newsletters', '' ),
      'singular_name'      => esc_html__( 'Newsletter', '' ),
    ],
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
    'rewrite_no_front'    => false,
    'show_in_menu'        => true,
    'supports' => [
      'title',
      'editor',
      'thumbnail',
    ],

    'rewrite' => true
  ];

  register_post_type( 'newsletter', $args );
}
add_action( 'init', 'nm_register_post_type_newsletter', 0 );

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
    'label'                 => 'Event',
    'description'           => 'IRL Events',
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor', 'thumbnail', ),
    'taxonomies'            => array( 'post_tag' ),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_icon'             => 'dashicons-calendar',
    'menu_position'         => null,
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'capability_type'       => 'page',
    'show_in_rest'          => true,
  );
  register_post_type( 'event', $args );

}
add_action( 'init', 'event_post_type', 0 );

// Register Notices
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
    'label'                 => 'Notice',
    'description'           => 'Simple notices with no archive',
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor', 'thumbnail'),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => null,
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => false,
    'exclude_from_search'   => true,
    'publicly_queryable'    => true,
    'capability_type'       => 'page',
    'show_in_rest'          => true,
  );
  register_post_type( 'notice', $args );

}
add_action( 'init', 'notices_post_type', 0 );

function nm_register_post_type_job() {
  $args = [
    'label'  => esc_html__( 'Jobs', 'text-domain' ),
    'labels' => [
      'menu_name'          => esc_html__( 'Jobs', '' ),
      'name_admin_bar'     => esc_html__( 'Job', '' ),
      'add_new'            => esc_html__( 'Add Job', '' ),
      'add_new_item'       => esc_html__( 'Add new Job', '' ),
      'new_item'           => esc_html__( 'New Job', '' ),
      'edit_item'          => esc_html__( 'Edit Job', '' ),
      'view_item'          => esc_html__( 'View Job', '' ),
      'update_item'        => esc_html__( 'View Job', '' ),
      'all_items'          => esc_html__( 'All Jobs', '' ),
      'search_items'       => esc_html__( 'Search Jobs', '' ),
      'parent_item_colon'  => esc_html__( 'Parent Job', '' ),
      'not_found'          => esc_html__( 'No Jobs found', '' ),
      'not_found_in_trash' => esc_html__( 'No Jobs found in Trash', '' ),
      'name'               => esc_html__( 'Jobs', '' ),
      'singular_name'      => esc_html__( 'Job', '' ),
    ],
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
    'supports' => [
      'title',
      'editor',
      'thumbnail',
    ],

    'rewrite' => true
  ];

  register_post_type( 'job', $args );
}
add_action( 'init', 'nm_register_post_type_job', 0 );
?>
