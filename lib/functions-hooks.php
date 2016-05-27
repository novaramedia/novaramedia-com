<?php

// Custom hooks (like excerpt length etc)

function save_reading_time_meta( $post_id, $post, $update ) {

  // [Thanks to https://wordpress.org/plugins/estimated-post-reading-time/]

  $words_per_minute = 265;
  $content = strip_tags($post->post_content);
  $content_words = str_word_count($content);
  $estimated_minutes = floor($content_words / $words_per_minute);

  update_post_meta($post_id, '_igv_reading_time', $estimated_minutes);

}
add_action('save_post', 'save_reading_time_meta', 10, 3);