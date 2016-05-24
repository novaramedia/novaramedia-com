<?php

// for array_filter
function onlyTopLevelCategoryFilter($var) {
  if ($var->category_parent == 0) {
    return true;
  }
}

// obviously it gets related posts and returns a WP Query
function get_related_posts($excludedIdsArray = null) {
  // Check if we are on a single page, if not, return false
  if ( !is_single() )
    return false;

  // Get the current post id
  $post_id = get_queried_object_id();

  // Get all the tags of the current post
  $tags = wp_get_post_tags($post_id);

  // Exlude this post and possibly others if parsed as variable
  $posts_not_in[] = $post_id;
  if ($excludedIdsArray) {
    $posts_not_in = array_merge($posts_not_in, $excludedIdsArray);
  }

  if ($tags) {

    $tag_ids = array();

    //Get the id of every tag and add it to the array
    foreach( $tags as $individual_tag ) {
      $tag_ids[] = $individual_tag->term_id;
    }

    //args for the query which will get the related posts
    $args = array(
      'tag__in' => $tag_ids,
      'post__not_in' => $posts_not_in,
      'posts_per_page' => 4,
      'order' => 'DESC',
      'orderby' => 'post_date',
    );
  } else {
    $args = array (
      'posts_per_page' => 4,
      'post__not_in'   => $posts_not_in,
    );
  }
  return new WP_Query($args);
}