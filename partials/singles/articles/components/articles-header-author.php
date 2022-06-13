<?php
  $author = get_post_meta($post->ID, '_cmb_author', true);
  $twitter = get_post_meta($post->ID, '_cmb_author_twitter', true);

  $contributors = get_post_meta($post->ID, '_cmb_contributors', true);

/*
  Check if there are contributors associated and create an array of valid contributor posts.
  Set meta variable false if no valid posts are found.
*/

  if ($contributors) {
    $contributors_posts_array = [];

    foreach(explode(',', $contributors) as $contributor_id) {
      $post = get_post($contributor_id);

      if ($post) {
        array_push($contributors_posts_array, $post);
      }
    }

    if (count($contributors_posts_array) === 0) {
      $contributors = false;
    }
  }


  $twitter_url = false;

  if ($twitter &&(!is_array($twitter) || count($twitter) === 1)) { // if twitter is set and it either isn't an array (old support) or it only has 1 value then we can display it
    if (is_array($twitter)) {
      $twitter_url = $twitter[0];
    } else {
      $twitter_url = $twitter;
    }
  }
?>
<h3>by <?php
  if ($contributors) {
    $number_of_contributors = count($contributors_posts_array);

    foreach($contributors_posts_array as $index => $contributor) {
      if ($number_of_contributors > 1) {
        if ($number_of_contributors === $index + 1) {
          echo ' & ';
        } else if ($index !== 0) {
          echo ', ';
        }
      }
      echo '<a href="' . get_the_permalink($contributor->ID) . '">' . $contributor->post_title . '</a>';
    }

  } else if (!empty($author)) {
    if ($twitter_url) {
      echo '<a href="https://twitter.com/' . $twitter_url . '" target="_blank" rel="nofollow">' . $author . '</a>';
    } else {
      echo $author;
    }
  } else {
    echo 'Novara Reporters';
  }
?></h3>
