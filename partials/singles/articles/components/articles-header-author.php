<?php
  $author = get_post_meta($post->ID, '_cmb_author', true);
  $twitter = get_post_meta($post->ID, '_cmb_author_twitter', true);
    
  $twitter_url = false;
  
  if (!is_array($twitter) || count($twitter) === 1) {
    if (is_array($twitter)) {
      $twitter_url = $twitter[0];
    } else {
      $twitter_url = $twitter;      
    }
  }
?>
<h3>by <?php
  if (!empty($author)) {
    if ($twitter_url) {
      echo '<a href="https://twitter.com/' . $twitter_url . '" target="_blank" rel="nofollow">';
    }
    
    echo $author;
    
    if ($twitter_url) {
      echo '</a>';
    }
  } else {
    echo 'Novara Reporters';
  }
?></h3>