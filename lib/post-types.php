<?php
// Register Custom Post Type
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
		'supports'              => array( ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'notice', $args );

}
add_action( 'init', 'notices_post_type', 0 );
?>