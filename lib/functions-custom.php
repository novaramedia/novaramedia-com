<?php

// for array_filter s
function only_top_level_category_filter($var) {
  if ($var->category_parent == 0) {
    return true;
  }
}

// obviously it gets related posts and returns a WP Query
function get_related_posts($excluded_ids_array = null, $category_name = null, $number_of_posts = 4) {

  // Check if we are on a single page, if not, return false
  if (!is_single())  {
    return false;
  }

  // Get the current post id
  $post_id = get_queried_object_id();

  // Get all the tags of the current post
  $tags = wp_get_post_tags($post_id);

  // Exlude this post and possibly others if parsed as variable
  $posts_not_in[] = $post_id;

  if ($excluded_ids_array) {
    $posts_not_in = array_merge($posts_not_in, $excluded_ids_array);
  }

  $args = array(
    'post__not_in' => $posts_not_in,
    'posts_per_page' => $number_of_posts,
  );

  if ($category_name) {
    $args['category_name'] = $category_name;
  }

  if ($tags) {

    $tag_ids = array();

    //Get the id of every tag and add it to the array
    foreach( $tags as $individual_tag ) {
      $tag_ids[] = $individual_tag->term_id;
    }

    //args for the query which will get the related posts
    $args = array_merge($args, array(
      'tag__in' => $tag_ids,
      'order' => 'DESC',
      'orderby' => 'post_date',
    ));

  }

  return new WP_Query($args);
}

// for site options. Returns array for select
function home_focus_list() {

  $focuses = array(
    null => 'No Focus Shown',
  );

  $focus_terms = get_terms(array(
    'taxonomy' => 'focus',
  ));

  if ($focus_terms) {
    foreach($focus_terms as $term) {
      $focuses[$term->term_id] = $term->name;
    }

  }

  return $focuses;

}

function menu_tags_list() {

  $tags = array();

  $tags_all = get_terms(array(
    'taxonomy' => 'post_tag',
  ));

  if ($tags_all) {
    foreach($tags_all as $tag) {
      $tags[$tag->term_id] = $tag->name;
    }

  }

  return $tags;

}