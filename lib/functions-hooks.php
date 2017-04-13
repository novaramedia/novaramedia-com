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


// Custom editor button to find replace all links in the_content and make target _blank

if ( is_admin() ) {
  add_action( 'init', 'extlink_setup_tinymce_plugin' );
}

function extlink_setup_tinymce_plugin() {

  // Check if the logged in WordPress User can edit Posts or Pages
  // If not, don't register our TinyMCE plugin
  if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
    return;
  }

  // Check if the logged in WordPress User has the Visual Editor enabled
  // If not, don't register our TinyMCE plugin
  if ( get_user_option( 'rich_editing' ) !== 'true' ) {
    return;
  }

  // Setup some filters
  add_filter( 'mce_external_plugins', 'extlink_add_tinymce_plugin' );
  add_filter( 'mce_buttons', 'extlink_add_tinymce_toolbar_button' );

}

function extlink_add_tinymce_plugin( $plugin_array ) {

  $plugin_array['extlinks'] = get_template_directory_uri() . '/lib/tinyMCE/extlink-tinymce.js';
  return $plugin_array;

}

function extlink_add_tinymce_toolbar_button( $buttons ) {

  array_push( $buttons, 'extlinks' );
  return $buttons;

}

function order_events_by_meta( $query ) {

  if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive()) {
    if ($query->query['post_type'] === 'event') {
      $query->set('orderby', 'meta_value');
      $query->set('meta_key', '_cmb_time');
    }
  }
}

add_action( 'pre_get_posts', 'order_events_by_meta' );
