<?php

/**
 * Returns a slug indexed array of all the Audio sub categories for use in select metaboxes
 */
function get_audio_categories_metabox_array() {
  $audio_category = get_category_by_slug( 'audio' );

  if ( ! $audio_category ) {
    return;
  }

    $terms = get_terms(
        array(
            'taxonomy' => 'category',
            'parent'   => $audio_category->term_id, // Use 'parent' instead of 'child_of'
        )
    );

  $return = array();
  $return['none'] = 'None';

  foreach ( $terms as $term ) {
    $return[ $term->slug ] = $term->name; // created slug indexed array of categories names
  }

  return $return;
}

/**
 * Returns a slug indexed array of all the Sections taxonomy options for use in select metaboxes
 */
function get_all_theme_sections_array() {
    $terms = get_terms(
        array(
            'taxonomy' => 'section',
        )
    );

  $return = array();
  $return['none'] = 'None';

  foreach ( $terms as $term ) {
    $return[ $term->term_id ] = $term->name;
  }

  return $return;
}

/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function nm_register_front_page_options_metabox() {
  $prefix = 'nm_';

  $banner_options = array(
      false                                              => 'None',
      'partials/support-section'                         => 'Support section',
      'partials/specials/banners/support-video'          => 'Support Video',
      'partials/specials/banners/podcast-committed'      => 'Podcast: Committed',
      'partials/specials/banners/podcast-if-i-speak'     => 'Podcast: If I Speak',
      'partials/specials/banners/focus-pro-rev-soccer'   => 'Focus: Pro Rev Soccer',
      'partials/specials/banners/podcast-foreign-agent'  => 'Podcast: Foreign Agent',
      'partials/specials/banners/focus-doing-it-right-sex-on-the-left' => 'Focus: Doing It Right: Sex On The Left',
      'partials/specials/banners/focus-breaking-britain' => 'Focus: Breaking Britain',
      'partials/specials/banners/focus-disability-its-political' => 'Focus: Disability: It’s Political',
      'partials/specials/banners/podcast-planet-b'       => 'Podcast: Planet B',
  );

  // Get all the newsletter pages and create signup options for all with correct settings

  $child_pages_wp_query = new WP_Query();
  $all_wp_pages = $child_pages_wp_query->query( array( 'post_type' => 'page' ) );
  $newsletter_page = get_page_by_title( 'Newsletters' );
  $newsletter_pages = get_page_children( $newsletter_page->ID, $all_wp_pages );

  if ( $newsletter_pages ) {
    foreach ( $newsletter_pages as $newsletter ) {
      $meta = get_post_meta( $newsletter->ID );
      $mailchimp_key = ! empty( $meta['_nm_mailchimp_key'] ) ? $meta['_nm_mailchimp_key'][0] : false;

      if ( $mailchimp_key ) {
        $banner_options[ 'newsletter-signup-' . $newsletter->ID ] = 'Newsletter signup: ' . $newsletter->post_title;
      }
    }
  }

  /**
   * Registers main options page menu item and form.
   */
    $main_options = new_cmb2_box(
        array(
            'id'           => 'nm_front_page_options',
            'title'        => 'Front Page',
            'object_types' => array( 'options-page' ),

            /*
            * The following parameters are specific to the options-page box
            * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
            */

        'option_key'       => 'nm_front_page_options', // The option key and admin menu page slug.
            'icon_url'     => 'dashicons-layout', // Menu icon. Only applicable if 'parent_slug' is left empty.
        // 'menu_title'      => esc_html__( 'Options', 'cmb2' ), // Falls back to 'title' (above).
        // 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
            'capability'   => 'edit_posts', // Cap required to view options-page.
        // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
        // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
        // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
        // 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
        // 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
        // 'message_cb'      => 'nm_options_page_message_callback',
        )
    );

  /**
   * Options fields ids only need
   * to be unique within this box.
   * Prefix is not needed.
   */
    $main_options->add_field(
        array(
            'name' => 'Front Page Settings',
            'desc' => 'This is where the various settings for the Front Page can be found and set. There are some subpages to these settings for specific features',
            'id'   => $prefix . 'front_page_settings_title',
            'type' => 'title',
        )
    );

    $main_options->add_field(
        array(
            'name' => 'Adverts and banners',
            'id'   => $prefix . 'front_page_settings_banners_title',
            'type' => 'title',
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'First banner',
            'desc'    => 'Select the content of the banner.',
            'id'      => $prefix . 'front_page_banner_option_1',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'Second banner',
            'id'      => $prefix . 'front_page_banner_option_2',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'Third banner',
            'id'      => $prefix . 'front_page_banner_option_3',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'Forth banner',
            'id'      => $prefix . 'front_page_banner_option_4',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name' => 'Live Schedule',
            'desc' => 'This is where overrides for the live schedule can be set. Can be deleted once in the past',
            'id'   => $prefix . 'front_page_settings_live_schedule_title',
            'type' => 'title',
        )
    );

    $schedule_overrides_group_field_id = $main_options->add_field(
        array(
            'id'          => $prefix . 'front_page_settings_live_schedule_overrides',
            'type'        => 'group',
            'description' => 'Overrides for the live schedule',
            'options'     => array(
                'group_title'   => __( 'Override {#}', 'nm' ),
                'add_button'    => __( 'Add Another Override', 'nm' ),
                'remove_button' => __( 'Remove Override', 'nm' ),
                'sortable'      => false,
            ),
        )
    );

    $main_options->add_group_field(
        $schedule_overrides_group_field_id,
        array(
            'name' => 'Start time',
            'id'   => 'start',
            'type' => 'text_datetime_timestamp',
        )
    );

    $main_options->add_group_field(
        $schedule_overrides_group_field_id,
        array(
            'name' => 'End time',
            'id'   => 'end',
            'type' => 'text_datetime_timestamp',
        )
    );

    $main_options->add_group_field(
        $schedule_overrides_group_field_id,
        array(
            'name'             => 'Status',
            'id'               => 'status',
            'type'             => 'select',
            'show_option_none' => false,
            'default'          => 'false',
            'options'          => array(
                'false' => __( 'Offline', 'cmb2' ),
                'true'  => __( 'Live', 'cmb2' ),
            ),
        )
    );

    $main_options->add_field(
        array(
            'name' => 'Offline messages',
            'id'   => $prefix . 'front_page_settings_live_schedule_offline_messages_title',
            'type' => 'title',
        )
    );

    $offline_messages_group_field_id = $main_options->add_field(
        array(
            'id'          => $prefix . 'front_page_settings_live_schedule_offline_messages',
            'type'        => 'group',
            'description' => 'Randomised messages that will be shown in the top bar of the desktop site when the livestream is offline',
            'options'     => array(
                'group_title'   => __( 'Message {#}', 'nm' ),
                'add_button'    => __( 'Add Another Message', 'nm' ),
                'remove_button' => __( 'Remove Message', 'nm' ),
                'sortable'      => false,
            ),
        )
    );

    $main_options->add_group_field(
        $offline_messages_group_field_id,
        array(
            'name' => 'Quote text',
            'id'   => 'text',
            'type' => 'text',
        )
    );

    $main_options->add_group_field(
        $offline_messages_group_field_id,
        array(
            'name' => 'Link',
            'id'   => 'link',
            'type' => 'text_url',
        )
    );

  /**
   * Register child page for above the fold featured.
   */
    $above_the_fold_featured = new_cmb2_box(
        array(
            'id'           => 'nm_above_the_fold_featured_options_page',
            'title'        => 'Above the Fold: Featured',
            'object_types' => array( 'options-page' ),
            'option_key'   => 'nm_front_page_above_the_fold_featured_options',
            'parent_slug'  => 'nm_front_page_options',
            'capability'   => 'edit_posts',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Above the fold: featured',
            'desc' => 'This is where the settings for the above the fold featured section can be set',
            'id'   => $prefix . 'above_the_fold_featured_options_title',
            'type' => 'title',
        )
    );

  // First featured post: The big one

    $above_the_fold_featured->add_field(
        array(
            'name' => 'First block: Main featured post',
            'desc' => 'This is the first featured post',
            'id'   => $prefix . 'above_the_fold_featured_1_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => 'Main featured post',
            'desc'            => 'Select the post to be primary featured post',
            'id'              => $prefix . 'above_the_fold_featured_1',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Show See Also section (optional)',
            'id'   => $prefix . 'above_the_fold_featured_1_show_related',
            'type' => 'checkbox',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'             => 'More On section to link (optional)',
            'desc'             => 'Select the thematic section to be linked to in the More On section.',
            'id'               => $prefix . 'above_the_fold_featured_1_more_on_section',
            'type'             => 'select',
            'show_option_none' => false,
            'options'          => get_all_theme_sections_array(),
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Link More On section to product (optional)',
            'desc' => 'If selected, the More On section will link to the product page of the selected product. This will override the above selection.',
            'id'   => $prefix . 'above_the_fold_featured_1_is_product_linked',
            'type' => 'checkbox',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Allow video embed (very optional)',
            'desc' => 'If selected, the featured post will allow for a video embed. This is only for very rare things like ElectionSesh',
            'id'   => $prefix . 'above_the_fold_featured_1_has_embed',
            'type' => 'checkbox',
        )
    );

    // Next 3 featured posts: The small ones

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Rest of first featured section',
            'desc' => 'These are the 3 other featured posts that will be shown in the first featured section',
            'id'   => $prefix . 'above_the_fold_featured_234_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '2nd featured post',
            'desc'            => 'Will display with thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_2',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '3rd featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_3',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '4th featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_4',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

  // Second featured block: First big one

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Second block: main featured post',
            'desc' => 'This is the first featured post in the second block. The 5th featured post overall',
            'id'   => $prefix . 'above_the_fold_featured_5_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => 'Main featured post',
            'desc'            => 'Select the post to be primary featured post',
            'id'              => $prefix . 'above_the_fold_featured_5',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Show See Also section (optional)',
            'id'   => $prefix . 'above_the_fold_featured_5_show_related',
            'type' => 'checkbox',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'             => 'More On section to link (optional)',
            'desc'             => 'Select the thematic section to be linked to in the More On section.',
            'id'               => $prefix . 'above_the_fold_featured_5_more_on_section',
            'type'             => 'select',
            'show_option_none' => false,
            'options'          => get_all_theme_sections_array(),
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Link More On section to product (optional)',
            'desc' => 'If selected, the More On section will link to the product page of the selected product. This will override the above selection.',
            'id'   => $prefix . 'above_the_fold_featured_5_is_product_linked',
            'type' => 'checkbox',
        )
    );

  // Next 3 featured posts: The small ones

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Rest of second featured section',
            'desc' => 'These are the 3 other featured posts that will be shown in the second featured section',
            'id'   => $prefix . 'above_the_fold_featured_678_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '2nd featured post',
            'desc'            => 'Will display with thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_6',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '3rd featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_7',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '4th featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_8',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

  /**
   * Register child page for the highlight section
   */
    $highlight_section = new_cmb2_box(
        array(
            'id'           => 'nm_highlight_section_options_page',
            'title'        => 'Highlight Section',
            'object_types' => array( 'options-page' ),
            'option_key'   => $prefix . 'front_page_highlight_section_options',
            'parent_slug'  => $prefix . 'front_page_options',
            'capability'   => 'edit_posts',
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Highlight Section',
            'desc' => 'This is where the settings for the front page Highlight Section can be set',
            'id'   => $prefix . 'front_page_highlight_section_options_title',
            'type' => 'title',
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Show section',
            'desc' => 'If selected, the Highlight Section will be displayed below the fold immediately after the support signup section',
            'id'   => $prefix . 'front_page_highlight_section_options_is_displayed',
            'type' => 'checkbox',
        )
    );

    $highlight_section->add_field(
        array(
            'name'             => 'Content thematic Section to display',
            'desc'             => 'Select the thematic section to display in the Highlight Section. Only posts in this Section will be shown',
            'id'               => $prefix . 'front_page_highlight_section_options_section',
            'type'             => 'select',
            'show_option_none' => false,
            'options'          => get_all_theme_sections_array(),
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Section title (optional)',
            'desc' => 'The text shown above the posts in the Highlight Section. If not set will default to the name of the Section',
            'id'   => $prefix . 'front_page_highlight_section_options_display_title',
            'type' => 'text',
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Section description (optional)',
            'desc' => 'The text shown after the title. This should be no longer than 2 sentences',
            'id'   => $prefix . 'front_page_highlight_section_options_description',
            'type' => 'textarea_small',
        )
    );

    /**
     * Registers secondary options page, and set main item as parent.
     */
    $secondary_options = new_cmb2_box(
        array(
            'id'           => 'nm_secondary_options_page',
            'title'        => 'Products Bar',
            'object_types' => array( 'options-page' ),
            'option_key'   => 'nm_front_page_links_bar_options',
            'parent_slug'  => 'nm_front_page_options',
            'capability'   => 'edit_posts',
        )
    );

    $secondary_options->add_field(
        array(
            'name' => 'Front Page Product Links',
            'desc' => 'Blocks displayed in the top bar of the desktop site. Used to directly promote product offerings',
            'id'   => $prefix . 'home_signups_title',
            'type' => 'title',
        )
    );

    $group_field_id = $secondary_options->add_field(
        array(
            'id'          => $prefix . 'front_page_links_bar',
            'type'        => 'group',
            'description' => 'Product sections on the Front Page',
            'options'     => array(
                'group_title'   => __( 'Product Block {#}', 'nm' ),
                'add_button'    => __( 'Add Another Product Block', 'nm' ),
                'remove_button' => __( 'Remove Product Block', 'nm' ),
                'sortable'      => true,
            ),
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name' => 'Title',
            'id'   => 'title',
            'type' => 'text',
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name' => 'Link',
            'id'   => 'link',
            'type' => 'text_url',
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name'        => 'Copy',
            'description' => 'Short copy. Needs to fit on 2 lines in the small box. This will likely be a short snappy sentence that is a call to action',
            'id'          => 'description',
            'type'        => 'textarea_small',
            'attributes'  => array(
                'data-validation'             => 'true',
                'data-validation-word-length' => 12,
            ),
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name'         => 'Image',
            'id'           => 'image',
            'type'         => 'file',
            'preview_size' => 'thumbnail',
        )
    );

  /**
 * Registers tertiary options page and sets the main item as parent.
 *
 * The following code is an example of how to create a tertiary options page using CMB2.
 * Uncomment the code below to use it in your implementation.
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
      'desc' => esc_html__( 'Field description (optional)', 'nm' ),
      'id'   => 'textarea_code',
      'type' => 'textarea_code',
  ) );
  */
}
add_action( 'cmb2_admin_init', 'nm_register_front_page_options_metabox' );

// phpcs:disable WordPress.NamingConventions.ValidFunctionName

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  0.1.0
 * @param  string $key     Options array key.
 * @param  string $key_group Options group key (default: 'nm_front_page_options').
 * @return mixed           Option value
 */
function NM_get_option( $key = '', $key_group = 'nm_front_page_options', $default_value = false ) {
  if ( function_exists( 'cmb2_get_option' ) ) {
    // Use cmb2_get_option as it passes through some key filters.
    return cmb2_get_option( $key_group, $key, $default_value );
  }

  // Fallback to get_option if CMB2 is not loaded yet.
  $opts = get_option( $key_group, $default_value );

  $val = $default_value;

  if ( $key === 'all' ) {
    $val = $opts;
  } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && $opts[ $key ] !== false ) {
    $val = $opts[ $key ];
  }

  return $val;
}
