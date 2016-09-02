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
    header('content-type: application/json; charset=utf-8');
    $json = json_encode(array('error' => 'badly formed request'));
    echo isset($_GET['callback'])
      ? "{$_GET['callback']}($json)"
      : $json;
  }

} else if (!empty($_GET['permalink'])) {

  $perma_raw = $_GET['permalink'];
  $id = url_to_postid($perma_raw);

} else {

  header('content-type: application/json; charset=utf-8');
  $json = json_encode(array('error' => 'badly formed request'));
  echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

}

if (empty($id)) {

  header('content-type: application/json; charset=utf-8');
  $json = json_encode(array('error' => 'badly formed request'));
  echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

}

$reqpost = get_post($id);

if ($reqpost) {
  $meta = get_post_meta($reqpost->ID);

  $cats = get_the_category($reqpost->ID);
  $cat = array_shift($cats);
  $type = $cat->slug;

  $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($reqpost->ID), 'col12-16to9' );
  $thumbmedium = wp_get_attachment_image_src( get_post_thumbnail_id($reqpost->ID), 'col6-16to9' );

  $path = $thumb[0];
  $type = pathinfo($path, PATHINFO_EXTENSION);
  $data = file_get_contents($path);
  $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

  $tags = wp_get_post_tags($reqpost->ID, array( 'fields' => 'names' ) );

  if (!empty($meta['_cmb_short_desc'][0])) {
    $description = $meta['_cmb_short_desc'][0];
  } else {
    $description = '';
  }

  if($type === 'video') {

    if (!empty($meta['_cmb_utube'][0])) {
      $utube = $meta['_cmb_utube'][0];
    } else {
      $utube = '';
    }


    $output = array(
      'id' => $reqpost->ID,
      'title' => $reqpost->post_title,
      'permalink' => get_permalink($reqpost->ID),
      'youtube_id' => $utube,
      'thumb_large' => $thumb[0],
      'thumb_base64' => $base64,
      'thumb_medium' => $thumbmedium[0],
      'short_desc' => $description,
      'tags' => $tags
    );

  } else if($type === 'audio') {

    if (!empty($meta['_cmb_sc'][0])) {
      $soundcloud = $meta['_cmb_sc'][0];
    } else {
      $soundcloud = '';
    }

    $output = array(
      'id' => $reqpost->ID,
      'title' => $reqpost->post_title,
      'permalink' => get_permalink($reqpost->ID),
      'soundcloud_url' => $soundcloud,
      'thumb_large' => $thumb[0],
      'thumb_base64' => $base64,
      'thumb_medium' => $thumbmedium[0],
      'short_desc' => $description,
      'tags' => $tags
    );

  } else {

    $output = array(
      'id' => $reqpost->ID,
      'title' => $reqpost->post_title,
      'permalink' => get_permalink($reqpost->ID),
      'thumb_large' => $thumb[0],
      'thumb_base64' => $base64,
      'thumb_medium' => $thumbmedium[0],
      'short_desc' => $description,
      'tags' => $tags
    );

  }

  header('content-type: application/json; charset=utf-8');
  $json = json_encode($output);
  echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

} else {

  header('content-type: application/json; charset=utf-8');
  $json = json_encode(array('error' => 'post not found'));
  echo isset($_GET['callback'])
    ? "{$_GET['callback']}($json)"
    : $json;

}

?>