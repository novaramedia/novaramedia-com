<?php
/**
* Get the category at the show/brand AKA child level. Meaning get the first child of the top level category.
*
* @return Object/Boolean WP Term object or false if doesn't exist
*/
function get_child_level_child_category($post_id)
{
    $categories = get_the_category($post_id);
    $child_categories = array_filter($categories, 'only_child_category_filter');
    $child_categories = array_values($child_categories);

    if (isset($child_categories[0])) {
        return $child_categories[0];
    } else {
        return false;
    }
}

/**
* Answer the question is this a single post in the articles category?
*
* @return Boolean
*/
function nm_is_single_article()
{
    if (!is_single()) { // if not single return straight away
        return false;
    }

    global $post;

    $categories = get_the_terms($post->ID, 'category'); // get the categories for the post

    if (!$categories) {
        return false;
    }

    $found_in_categories = array_filter(
        $categories,
        function ($category) {
        return $category->slug === 'articles';
    }
    ); // check to see if any of the categories returned match the articles slug

    if (count($found_in_categories) > 0) {
        return true; // if articles slug was found return true
    }

    return false;
}

/**
* Get the top level category of a post. This will either be Audio, Article, Video and will only be 1
*
* @return Object
*/
function get_the_top_category($postId)
{
    $categories = get_the_category($postId);

    $top_categories = array_filter($categories, 'only_top_level_category_filter');
    $top_categories = array_values($top_categories);

    if (isset($top_categories[0])) {
        return $top_categories[0];
    } else {
        return false;
    }
}

// get and return the first sub category assigned to the post
function get_the_sub_category($postId, $object = false)
{
    $categories = get_the_category($postId);

    $child_categories = array_filter($categories, 'only_child_category_filter');
    $child_categories = array_values($child_categories);

    if (isset($child_categories[0])) {
        if ($object) {
            return $child_categories[0];
        } else {
            return $child_categories[0]->name;
        }
    } else {
        return false;
    }
}

// for array_filters

// returns the id from a post object from a WP query
function nm_filter_query_ids($post)
{
    return $post->ID;
}

// filters an array of post categories for just top level categories
function only_top_level_category_filter($var)
{
    if ($var->category_parent == 0) {
        return true;
    }
}

// filters an array of post categories for just child categories
function only_child_category_filter($var)
{
    if ($var->category_parent !== 0) {
        return true;
    }
}

// create youtube embed url with consistent parameters
function generate_youtube_embed_url($id, $autoplay = false)
{
    $url = 'https://www.youtube.com/embed/' . $id . '?modestbranding=1&rel=0';

    if ($autoplay) {
        $url .= '&autoplay=1';
    }

    return $url;
}

// obviously it gets related posts and returns a WP Query
function get_related_posts($excluded_ids_array = null, $category_name = null, $number_of_posts = 4)
{

  // Check if we are on a single page, if not, return false
    if (!is_single()) {
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
        foreach ($tags as $individual_tag) {
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
function home_focus_list()
{
    $focuses = array(
    null => 'No Focus Shown',
  );

    $focus_terms = get_terms(array(
    'taxonomy' => 'focus',
  ));

    if ($focus_terms) {
        foreach ($focus_terms as $term) {
            $focuses[$term->term_id] = $term->name;
        }
    }

    return $focuses;
}

function menu_tags_list()
{
    $tags = array();

    $tags_all = get_terms(array(
    'taxonomy' => 'post_tag',
  ));

    if ($tags_all) {
        foreach ($tags_all as $tag) {
            $tags[$tag->term_id] = $tag->name;
        }
    }

    return $tags;
}
