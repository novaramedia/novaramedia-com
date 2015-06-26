<?php
/*
Template Name: Data - Podcast Tool
*/

if (!empty($page)) {
	$offset = ($page-1)*10;
} else {
	$offset = 0;
	$page = 1;
}

$args = array(
	'posts_per_page' 	=> 1,
	'category_name' 	=> 'fm',
	'post_status'		=> 'draft'
);

$the_query = new WP_Query($args);

if ($the_query->post_count === 0) {
	$output = array(
		'site_url' => site_url(),
		'channel' => 'fm',
		'page' => $page,
		'error' => true,
		'posts' => 'no posts'
		);
  header('content-type: application/json; charset=utf-8');
  $json = json_encode($output);
  echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

} else {

  $posts = array();
  while ( $the_query->have_posts() ) :
  	$the_query->the_post();
  	$id = $the_query->post->ID;
  	$meta = get_post_meta($id);

    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'api-large' );
    $thumbmedium = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'api-medium' );

  	$tags = wp_get_post_tags( $id, array( 'fields' => 'names' ) );

  	array_push($posts, array(
  		'id' => $id,
  		'title' => $the_query->post->post_title,
  		'permalink' => get_permalink($id),
  		'short_desc' => $meta['_cmb_short-desc'][0],
  		'soundcloud_url' => $meta['_cmb_sc'][0],
      'thumb_large' => $thumb[0],
      'thumb_medium' => $thumbmedium[0],
  		'tags' => $tags
  		));
  endwhile;

  wp_reset_postdata();

  $output = array(
    'site_url' => site_url(),
    'channel' => 'fm',
    'page' => $page,
    'posts' => $posts
  );

  header('content-type: application/json; charset=utf-8');
  $json = json_encode($output);
  echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

}
?>