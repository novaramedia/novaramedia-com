<?php
/**
 * User Role Capabilities Management
 *
 * Handles custom capability assignments and restrictions for WordPress user roles.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Grant editor role the ability to edit menus.
 *
 * Adds 'edit_theme_options' capability to the editor role on theme activation
 * or when the capability doesn't exist. This allows editors to access the
 * Appearance > Menus page.
 *
 * @return void
 */
function nm_add_editor_menu_capability() {
  $role = get_role( 'editor' );

  if ( $role && ! $role->has_cap( 'edit_theme_options' ) ) {
    $role->add_cap( 'edit_theme_options' );
  }
}
add_action( 'after_switch_theme', 'nm_add_editor_menu_capability' );

/**
 * Restrict editors from accessing theme and appearance options except menus.
 *
 * Uses the map_meta_cap filter to prevent editors from accessing theme settings,
 * widgets, and customizer while still allowing menu editing. Administrators
 * retain full access to all appearance options.
 *
 * @param array  $caps    Required capabilities.
 * @param string $cap     Capability being checked.
 * @param int    $user_id User ID.
 * @return array Modified capabilities array.
 */
function nm_restrict_editor_theme_options( $caps, $cap, $user_id ) {
  // Only apply restrictions to users with editor role
  // Cache role check as this filter runs frequently
  static $editor_users = array();
  
  if ( ! isset( $editor_users[ $user_id ] ) ) {
    $user = get_userdata( $user_id );
    $editor_users[ $user_id ] = $user && in_array( 'editor', $user->roles, true );
  }
  
  if ( ! $editor_users[ $user_id ] ) {
    return $caps;
  }
  
  // Contextually restrict edit_theme_options for editors to menu pages only
  if ( $cap === 'edit_theme_options' ) {
    global $pagenow;
    $is_menu_page = $pagenow === 'nav-menus.php';

    // Check for menu-related AJAX actions
    // Note: WordPress handles nonce verification in the actual AJAX handlers
    $is_menu_ajax = false;
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) ) {
      $action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );

      $menu_actions = apply_filters(
        'nm_editor_allowed_menu_actions',
        array(
          'add-menu-item',
          'delete-menu-item',
          'update-menu-item',
          'menu-quick-search',
          'menu-locations-save',
          'menu-get-metabox',
          'add-menu',
        )
      );
      $is_menu_ajax = in_array( $action, $menu_actions, true );
    }

    if ( $is_menu_page || $is_menu_ajax ) {
      return $caps;
    }

    return array( 'do_not_allow' );
  }

  return $caps;
}
add_filter( 'map_meta_cap', 'nm_restrict_editor_theme_options', 10, 3 );

/**
 * Remove appearance menu items for editors except Menus.
 *
 * Hides theme, widget, and customizer menu items from editors while keeping
 * the Menus submenu item visible and accessible.
 *
 * @return void
 */
function nm_remove_editor_appearance_menus() {
  $user = wp_get_current_user();
  
  // Only apply to editors
  if ( ! in_array( 'editor', $user->roles, true ) ) {
    return;
  }
  
  // Remove specific appearance submenu items for editors
  remove_submenu_page( 'themes.php', 'themes.php' );        // Themes
  remove_submenu_page( 'themes.php', 'customize.php' );     // Customize
  remove_submenu_page( 'themes.php', 'widgets.php' );       // Widgets
  remove_submenu_page( 'themes.php', 'theme-editor.php' );  // Theme File Editor
  
  // Keep nav-menus.php accessible - no removal needed
}
add_action( 'admin_menu', 'nm_remove_editor_appearance_menus', 999 );
