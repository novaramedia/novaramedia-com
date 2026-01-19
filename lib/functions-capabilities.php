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
add_action( 'admin_init', 'nm_add_editor_menu_capability' );

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
  $user = get_userdata( $user_id );
  if ( ! $user || ! in_array( 'editor', $user->roles, true ) ) {
    return $caps;
  }
  
  // Block access to theme/widget/customizer capabilities for editors
  $restricted_caps = array(
    'switch_themes',        // Prevents theme switching
    'edit_themes',          // Prevents theme file editing
    'edit_theme_options',   // We'll handle this contextually below
  );
  
  if ( in_array( $cap, $restricted_caps, true ) ) {
    // Allow edit_theme_options only for nav menus
    if ( $cap === 'edit_theme_options' ) {
      // Check if we're on the nav-menus page
      global $pagenow;
      $is_menu_page = $pagenow === 'nav-menus.php';
      
      // Check for menu-related AJAX actions
      $is_menu_ajax = false;
      if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) ) {
        $action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );
        $menu_actions = array(
          'add-menu-item',
          'delete-menu-item',
          'update-menu-item',
          'menu-quick-search',
          'menu-locations-save',
          'menu-get-metabox',
          'add-menu',
        );
        $is_menu_ajax = in_array( $action, $menu_actions, true );
      }
      
      if ( $is_menu_page || $is_menu_ajax ) {
        // Allow access to menus
        return $caps;
      }
    }
    
    // Block all other theme options
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
