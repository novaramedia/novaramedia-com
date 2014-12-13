<?php
/*
Template Name: Data - Single
*/

include('lib/stathat.php');
stathat_ez_count('patrickbest@patrickbest.com', 'novaramedia.com: api-data-single', 1);

if (!empty($_GET['id'])) {

  $id_raw = $_GET['id'];

	if (is_numeric($id_raw)) {
		$id = filter_var($id_raw, FILTER_SANITIZE_NUMBER_INT);
	} else {
		header("Content-type: application/json");
		die(json_encode(array('error' => 'badly formed request')));
	}

} else if (!empty($_GET['permalink'])) {

  $perma_raw = $_GET['permalink'];
	$id = url_to_postid($perma_raw);

} else {

	header("Content-type: application/json");
	die(json_encode(array('error' => 'badly formed request')));

}

if (empty($id)) {

	header("Content-type: application/json");
	die(json_encode(array('error' => 'badly formed request')));

}

$reqpost = get_post($id);

if ($reqpost) {
	$meta = get_post_meta($reqpost->ID);

  $cats = get_the_category($reqpost->ID);
  $cat = array_shift($cats);
  $type = $cat->slug;

  $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($reqpost->ID), 'api-large' );
  $thumbmedium = wp_get_attachment_image_src( get_post_thumbnail_id($reqpost->ID), 'api-medium' );

  $tags = wp_get_post_tags($reqpost->ID, array( 'fields' => 'names' ) );

  if($type === 'tv') {

  	$output = array(
  		'id' => $reqpost->ID,
  		'title' => $reqpost->post_title,
  		'permalink' => get_permalink($reqpost->ID),
  		'youtube_id' => $meta['_cmb_utube'][0],
      'thumb_large' => $thumb[0],
      'thumb_medium' => $thumbmedium[0],
      'short_desc' => $meta['_cmb_short-desc'][0],
      'tags' => $tags
  	);

  } else if($type === 'fm') {

  	$output = array(
  		'id' => $reqpost->ID,
  		'title' => $reqpost->post_title,
  		'permalink' => get_permalink($reqpost->ID),
  		'soundcloud_url' => $meta['_cmb_sc'][0],
      'thumb_large' => $thumb[0],
      'thumb_medium' => $thumbmedium[0],
      'short_desc' => $meta['_cmb_short-desc'][0],
      'tags' => $tags
  	);

  } else {

  	$output = array(
  		'id' => $reqpost->ID,
  		'title' => $reqpost->post_title,
  		'permalink' => get_permalink($reqpost->ID),
      'thumb_large' => $thumb[0],
      'thumb_medium' => $thumbmedium[0],
      'short_desc' => $meta['_cmb_short-desc'][0],
      'tags' => $tags
  	);

  }

	header('content-type: application/json; charset=utf-8');
	$json = json_encode($output);
	echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

} else {

	header("Content-type: application/json");
	die(json_encode(array('error' => 'post not found')));

}

?>