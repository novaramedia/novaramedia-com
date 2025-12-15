<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
/**
 * Miscellaneous functions for the theme.
 *
 * @package novara-media
 */

/**
 * Remove WP Emoji
 * Source: https://wordpress.org/support/article/emoji/
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Disable that freaking admin bar
 *
 * @return bool False to disable admin bar.
 */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Remove WordPress version from meta generator tag.
 *
 * @return string Empty string to remove generator.
 */
function nm_remove_wp_version() {
  return '';
}
add_filter( 'the_generator', 'nm_remove_wp_version' );

/**
 * Add thumbnail and post ID columns to post list in admin.
 *
 * @param array $cols Existing columns.
 * @return array Modified columns with thumbnail and post ID.
 */
function nm_add_post_thumbnail_column( $cols ) {
  $cols['nm_post_thumb'] = __( 'Thumbnail' );
  $cols['nm_post_id'] = __( 'Post ID' );
  return $cols;
}
add_filter( 'manage_posts_columns', 'nm_add_post_thumbnail_column' );

/**
 * Display content in custom admin columns.
 *
 * @param string $col Column name.
 * @param int    $id  Post ID.
 */
function nm_display_post_thumbnail_column( $col, $id ) {
  switch ( $col ) {
    case 'nm_post_thumb':
      if ( function_exists( 'the_post_thumbnail' ) ) {
        the_post_thumbnail(
          'col4-square',
          array(
            'style' => 'max-width:100%;height:auto;',
          )
        );
      } else {
        echo esc_html( 'Not supported in theme' );
      }
        break;
    case 'nm_post_id':
      echo '<span class="nm-post-id-copy" onclick="nmCopyPostId(this, ' . esc_attr( $id ) . ')" style="cursor:pointer;padding:2px 6px;border-radius:3px;background:#f0f0f1;border:1px solid #c3c4c7;" title="Click to copy ID">' . esc_html( $id ) . '</span>';
      break;
  }
}
add_action( 'manage_posts_custom_column', 'nm_display_post_thumbnail_column', 5, 2 );

/**
 * Add copy-to-clipboard JavaScript function to admin footer.
 *
 * Only loads on admin pages where the post ID column might be displayed.
 */
function nm_add_copy_post_id_script() {
  $screen = get_current_screen();
  if ( $screen && $screen->base === 'edit' ) {
    ?>
    <script>
      function nmCopyPostId(element, id) {
        navigator.clipboard.writeText(id).then(function() {
          element.style.background = '#00a32a';
          element.style.color = 'white';
          element.style.transform = 'scale(1.05)';
          setTimeout(function() {
            element.style.background = '#f0f0f1';
            element.style.color = '';
            element.style.transform = '';
          }, 1000);
        }).catch(function() {
          // Fallback for older browsers
          var textArea = document.createElement('textarea');
          textArea.value = id;
          document.body.appendChild(textArea);
          textArea.select();
          document.execCommand('copy');
          document.body.removeChild(textArea);
          element.style.background = '#00a32a';
          element.style.color = 'white';
          setTimeout(function() {
            element.style.background = '#f0f0f1';
            element.style.color = '';
          }, 300);
        });
      }
    </script>
    <?php
  }
}
add_action( 'admin_footer', 'nm_add_copy_post_id_script' );

/**
 * Remove automatic links from images in blog posts.
 *
 * Sets the default image link type to 'none' to prevent automatic linking.
 */
function nm_disable_image_links() {
  $image_set = get_option( 'image_default_link_type' );
  if ( $image_set !== 'none' ) {
    update_option( 'image_default_link_type', 'none' );
  }
}
add_action( 'admin_init', 'nm_disable_image_links', 10 );
