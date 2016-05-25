<?php

// Custom filters (like pre_get_posts etc)

add_filter('embed_oembed_html', 'my_embed_oembed_html', 99, 4);
function my_embed_oembed_html($html, $url, $attr, $post_id) {

  return '<div class="margin-top-basic margin-bottom-basic"><div class="u-video-embed-container">' . $html . '</div></div>';

}