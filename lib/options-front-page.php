<?php

/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function nm_register_front_page_options_metabox() {
  $prefix = 'nm_';

  /**
   * Registers main options page menu item and form.
   */
  $main_options = new_cmb2_box( array(
    'id'           => 'nm_front_page_options',
    'title'        => 'Front Page',
    'object_types' => array( 'options-page' ),

    /*
     * The following parameters are specific to the options-page box
     * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
     */

    'option_key'      => 'nm_front_page_options', // The option key and admin menu page slug.
    'icon_url'        => 'dashicons-layout', // Menu icon. Only applicable if 'parent_slug' is left empty.
    // 'menu_title'      => esc_html__( 'Options', 'cmb2' ), // Falls back to 'title' (above).
    // 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
    'capability'      => 'edit_posts', // Cap required to view options-page.
    // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
    // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
    // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
    // 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
    // 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
    // 'message_cb'      => 'nm_options_page_message_callback',
  ) );

  /**
   * Options fields ids only need
   * to be unique within this box.
   * Prefix is not needed.
   */
  $main_options->add_field( array(
    'name'    => 'Front Page Settings',
    'desc'    => 'This is where the various settings for the Front Page can be found and set. There are some subpages to these settings for specific features',
    'id'      => $prefix . 'front_page_settings_title',
    'type'    => 'title',
  ) );

  $main_options->add_field( array(
    'name' => 'Show radio player when FM show is live',
    'desc' => 'According to the programmed schedule choose if to show the radio player when #NovaraFM is live on ResonanceFM',
    'id'   => $prefix . 'front_page_home_radio_boolean',
    'type' => 'checkbox',
  ) );

  $main_options->add_field( array(
    'name'    => 'Articles Curation',
    'desc'    => 'This is where articles can be curated to be displayed out of chronology',
    'id'      => $prefix . 'front_page_settings_articles_title',
    'type'    => 'title',
  ) );

  $main_options->add_field( array(
    'name'    => 'Main featured articles',
    'desc'    => 'Select the articles here to be shown as the main featured articles on the homepage. Only Articles category posts will be shown and only the chronological first 2 of the selected posts will be shown.',
    'id'      => $prefix . 'front_page_main_featured_articles',
    'type'    => 'post_search_text',
    'post_type'   => 'post',
    // Default is 'checkbox', used in the modal view to select the post type
    // 'select_type' => 'radio',
    // Will replace any selection with selection from modal. Default is 'add'
    'select_behavior' => 'replace',
  ) );

  $main_options->add_field( array(
    'name'    => 'Sub featured article',
    'desc'    => 'Select the article shown in the sub feature section on the homepage. If not selected the most recent Analysis type post will be shown.',
    'id'      => $prefix . 'front_page_sub_featured_article',
    'type'    => 'post_search_text',
    'post_type'   => 'post',
    'select_type' => 'radio',
    'select_behavior' => 'replace',
  ) );

  /**
   * Registers secondary options page, and set main item as parent.
   */
  $secondary_options = new_cmb2_box( array(
    'id'           => 'nm_secondary_options_page',
    'title'        => 'Links Bar',
    'object_types' => array( 'options-page' ),
    'option_key'   => 'nm_front_page_links_bar_options',
    'parent_slug'  => 'nm_front_page_options',
  ) );

  $secondary_options->add_field( array(
    'name' => 'Front Page Links and Signups for',
    'desc' => 'Up to 4 signup blocks. Displayed in the top bar of the desktop site',
    'id'   => $prefix . 'home_signups_title',
    'type' => 'title',
  ) );

  $group_field_id = $secondary_options->add_field( array(
    'id'          => $prefix . 'front_page_links_bar',
    'type'        => 'group',
    'description' => 'Signup sections on the Front Page',
    'options'     => array(
      'group_title'       => __( 'Block {#}', 'nm' ),
      'add_button'        => __( 'Add Another Block', 'nm' ),
      'remove_button'     => __( 'Remove Block', 'nm' ),
      'sortable'          => true,
    ),
  ) );

  $secondary_options->add_group_field( $group_field_id, array(
    'name' => 'Title',
    'id'   => 'title',
    'type' => 'text',
  ) );

  $secondary_options->add_group_field( $group_field_id, array(
    'name' => 'Link',
    'id'   => 'link',
    'type' => 'text_url',
  ) );

  $secondary_options->add_group_field( $group_field_id, array(
    'name' => 'Copy',
    'description' => 'Short copy. Needs to fit on 2 lines in the small box. This will likely be a short snappy sentence that is a call to action',
    'id'   => 'description',
    'type' => 'textarea_small',
  ) );

  $secondary_options->add_group_field( $group_field_id, array(
    'name' => 'Image',
    'id'   => 'image',
    'type' => 'file',
    'preview_size' => 'thumbnail',
  ) );

  /**
   * Registers tertiary options page, and set main item as parent.
   */
/*
  $tertiary_options = new_cmb2_box( array(
    'id'           => 'nm_tertiary_options_page',
    'title'        => esc_html__( 'Tertiary Options', 'nm' ),
    'object_types' => array( 'options-page' ),
    'option_key'   => 'nm_tertiary_options',
    'parent_slug'  => 'nm_main_options',
  ) );

  $tertiary_options->add_field( array(
    'name' => esc_html__( 'Test Text Area for Code', 'nm' ),
    'desc' => esc_html__( 'field description (optional)', 'nm' ),
    'id'   => 'textarea_code',
    'type' => 'textarea_code',
  ) );
*/

}
add_action( 'cmb2_admin_init', 'nm_register_front_page_options_metabox' );

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function NM_get_option( $key = '', $key_group = 'nm_front_page_options', $default = false ) {
  if ( function_exists( 'cmb2_get_option' ) ) {
    // Use cmb2_get_option as it passes through some key filters.
    return cmb2_get_option( $key_group, $key, $default );
  }

  // Fallback to get_option if CMB2 is not loaded yet.
  $opts = get_option( $key_group, $default );

  $val = $default;

  if ( 'all' == $key ) {
    $val = $opts;
  } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
    $val = $opts[ $key ];
  }

  return $val;
}