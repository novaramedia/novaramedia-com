<?php
function topic_taxonomy() {

	$labels = array(
		'name'                       => 'Topics',
		'singular_name'              => 'Topic',
		'menu_name'                  => 'Topics',
		'all_items'                  => 'All Topics',
		'parent_item'                => 'Parent Topic',
		'parent_item_colon'          => 'Parent Topic:',
		'new_item_name'              => 'New Topic Name',
		'add_new_item'               => 'Add New Topic',
		'edit_item'                  => 'Edit Topic',
		'update_item'                => 'Update Topic',
		'view_item'                  => 'View Topic',
		'separate_items_with_commas' => 'Separate items with commas',
		'add_or_remove_items'        => 'Add or remove items',
		'choose_from_most_used'      => 'Choose from the most used',
		'popular_items'              => 'Popular Items',
		'search_items'               => 'Search Items',
		'not_found'                  => 'Not Found',
		'no_terms'                   => 'No items',
		'items_list'                 => 'Items list',
		'items_list_navigation'      => 'Items list navigation',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'rewrite' => array( 'hierarchical' => true ),
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'topic', array( 'post' ), $args );

}
add_action( 'init', 'topic_taxonomy', 0 );

function focus_taxonomy() {

	$labels = array(
		'name'                       => 'Focus',
		'singular_name'              => 'Focus',
		'menu_name'                  => 'Focus',
		'all_items'                  => 'All Focus',
		'parent_item'                => 'Parent Focus',
		'parent_item_colon'          => 'Parent Focus:',
		'new_item_name'              => 'New Focus Name',
		'add_new_item'               => 'Add New Focus',
		'edit_item'                  => 'Edit Focus',
		'update_item'                => 'Update Focus',
		'view_item'                  => 'View Focus',
		'separate_items_with_commas' => 'Separate items with commas',
		'add_or_remove_items'        => 'Add or remove items',
		'choose_from_most_used'      => 'Choose from the most used',
		'popular_items'              => 'Popular Items',
		'search_items'               => 'Search Items',
		'not_found'                  => 'Not Found',
		'no_terms'                   => 'No items',
		'items_list'                 => 'Items list',
		'items_list_navigation'      => 'Items list navigation',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'focus', array( 'post' ), $args );

}
add_action( 'init', 'focus_taxonomy', 0 );
?>
