<?php

/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function nm_register_fundraising_options_metabox() {
  $prefix = 'nm_';

  /**
   * Registers main options page menu item and form.
   */
    $main_options = new_cmb2_box(
        array(
            'id'           => 'nm_fundraising_options',
            'title'        => 'Fundraising Options',
            'object_types' => array( 'options-page' ),

            /*
            * The following parameters are specific to the options-page box
            * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
            */

            'option_key'   => 'nm_fundraising_options', // The option key and admin menu page slug.
            'icon_url'     => 'dashicons-money-alt', // Menu icon. Only applicable if 'parent_slug' is left empty.
            'menu_title'   => esc_html__( 'Fundraising', 'cmb2' ), // Falls back to 'title' (above).
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
            'name' => 'Support section',
            'desc' => 'This is the common element with the form to start the donate process',
            'id'   => $prefix . 'fundraising_settings_support_section_title',
            'type' => 'title',
        )
    );

    $main_options->add_field(
        array(
            'name'    => __( 'Support section title', 'NM' ),
            'desc'    => false,
            'id'      => $prefix . 'fundraising_settings_support_section_title',
            'type'    => 'text',
            'default' => 'Support Us',
        )
    );

        $main_options->add_field(
            array(
                'name' => __( 'Support section text', 'NM' ),
                'desc' => false,
                'id'   => $prefix . 'fundraising_settings_support_section_text',
                'type' => 'textarea_small',
            )
        );

        $main_options->add_field(
            array(
                'name' => 'Support Section Dynamic Copy',
                'desc' => 'These fields dynamically update the form text based on whether the user selects a one-off or regular donation. Note: All fields in this section must be completed for the overrides to take effect. Leave blank if you do not want to override the text above.',
                'id'   => $prefix . 'fundraising_settings_regular_override_title',
                'type' => 'title',
            )
        );

    // Regular heading and text for overrides
    $main_options->add_field(
        array(
            'name' => 'Regular Heading Override',
            'desc' => 'NOTE: fill in all override fields (one-off and regular) for this to work.',
            'id'   => $prefix . 'fundraising_settings_regular_heading_override',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => 'Regular Text Override',
            'desc' => 'NOTE: fill in all override fields (one-off and regular) for this to work.',
            'id'   => $prefix . 'fundraising_settings_regular_text_override',
            'type' => 'textarea_small',
        )
    );

    // One-off heading and text for overrides
    $main_options->add_field(
        array(
            'name' => 'One-off Heading Override',
            'desc' => 'NOTE: fill in all override fields (one-off and regular) for this to work.',
            'id'   => $prefix . 'fundraising_settings_oneoff_heading_override',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => 'One-off Text Override',
            'desc' => 'NOTE: fill in all override fields (one-off and regular) for this to work.',
            'id'   => $prefix . 'fundraising_settings_oneoff_text_override',
            'type' => 'textarea_small',
        )
    );

    $main_options->add_field(
        array(
            'name' => 'Support section auto values',
            'desc' => 'These fields override the default support section when the user selects a regular donation schedule.',
            'id'   => $prefix . 'fundraising_settings_support_section_override_title',
            'type' => 'title',
        )
    );

    $support_section_group = $main_options->add_field(
        array(
            'name'        => __( 'Support section auto values', 'NM' ),
            'description' => __( 'Sets the default values for the support section form. Also sets the url variable alternative values. First set will be the default and ignore the url code.', 'cmb2' ),
            'id'          => $prefix . 'fundraising_settings_support_section_autovalues',
            'type'        => 'group',
            'options'     => array(
                'group_title'   => __( 'Entry {#}', 'cmb2' ),
                'add_button'    => __( 'Add Another Entry', 'cmb2' ),
                'remove_button' => __( 'Remove Entry', 'cmb2' ),
                'closed'        => true,
            ),
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'        => 'URL code',
            'description' => 'Unique code for URL. Normally a random 6 character string.',
            'id'          => 'url_code',
            'type'        => 'text_small',
            'attributes'  => array(
                'data-validation'          => 'true',
                'data-validation-required' => 'true',
            ),
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'        => 'Show first',
            'description' => 'Show regular or one off option first?',
            'id'          => 'show_first',
            'type'        => 'radio_inline',
            'options'     => array(
                'regular' => __( 'Regular', 'cmb2' ),
                'oneoff'  => __( 'One off', 'cmb2' ),
            ),
            'default'     => 'regular',
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'       => 'Default low regular donation',
            'id'         => 'regular_low',
            'type'       => 'text_small',
            'attributes' => array(
                'data-validation'          => 'true',
                'data-validation-required' => 'true',
            ),
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'       => 'Default medium regular donation',
            'id'         => 'regular_medium',
            'type'       => 'text_small',
            'attributes' => array(
                'data-validation'          => 'true',
                'data-validation-required' => 'true',
            ),
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'       => 'Default high regular donation',
            'id'         => 'regular_high',
            'type'       => 'text_small',
            'attributes' => array(
                'data-validation'          => 'true',
                'data-validation-required' => 'true',
            ),
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'       => 'Default low one-off donation',
            'id'         => 'oneoff_low',
            'type'       => 'text_small',
            'attributes' => array(
                'data-validation'          => 'true',
                'data-validation-required' => 'true',
            ),
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'       => 'Default medium one-off donation',
            'id'         => 'oneoff_medium',
            'type'       => 'text_small',
            'attributes' => array(
                'data-validation'          => 'true',
                'data-validation-required' => 'true',
            ),
        )
    );

    $main_options->add_group_field(
        $support_section_group,
        array(
            'name'       => 'Default high one-off donation',
            'id'         => 'oneoff_high',
            'type'       => 'text_small',
            'attributes' =>
            array(
                'data-validation'          => 'true',
                'data-validation-required' => 'true',
            ),
        )
    );
    // Bottom of window support bar

    $main_options->add_field(
        array(
            'name' => 'Sticky support bar',
            'desc' => 'This is the bar at the bottom of the window that has an open and closed state',
            'id'   => $prefix . 'fundraising_settings_support_bar_title',
            'type' => 'title',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'Open state first paragraph of copy', 'NM' ),
            'desc' => 'Single paragraph of copy.',
            'id'   => $prefix . 'fundraising_settings_support_bar_open_paragraph',
            'type' => 'textarea_small',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'Open state bold CTA', 'NM' ),
            'desc' => 'Single sentence that shows below the main copy and in bold',
            'id'   => $prefix . 'fundraising_settings_support_bar_open_cta',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'Open state button text', 'NM' ),
            'desc' => 'Text shown on the button linking to support page',
            'id'   => $prefix . 'fundraising_settings_support_bar_open_button',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'Closed state, desktop only copy', 'NM' ),
            'desc' => 'Single sentence shown after slogan',
            'id'   => $prefix . 'fundraising_settings_support_bar_desktop_closed_copy',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'Closed state, desktop only CTA text', 'NM' ),
            'desc' => 'Text shown on the underlined link to support page',
            'id'   => $prefix . 'fundraising_settings_support_bar_desktop_closed_cta',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'Closed state, mobile only CTA', 'NM' ),
            'desc' => 'Text shown on the underlined link to support page',
            'id'   => $prefix . 'fundraising_settings_support_bar_mobile_closed_cta',
            'type' => 'text',
        )
    );

    // Front page video banner block
    $main_options->add_field(
        array(
            'name' => 'Front page video block CTA',
            'desc' => 'This is the video embed CTA block used as a banner option on the front page',
            'id'   => $prefix . 'fundraising_settings_video_banner_cta_title',
            'type' => 'title',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'YouTube embed ID', 'NM' ),
            'desc' => false,
            'id'   => $prefix . 'fundraising_settings_video_banner_cta_youtube_id',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'CTA title', 'NM' ),
            'desc' => 'Add a custom title here, if left blank it will be populated with the standard support section title.',
            'id'   => $prefix . 'fundraising_settings_video_banner_cta_headline',
            'type' => 'text',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'CTA text', 'NM' ),
            'desc' => 'Add custom text here, if left blank it will be populated with the standard support section text.',
            'id'   => $prefix . 'fundraising_settings_video_banner_cta_custom_text',
            'type' => 'textarea_small',
        )
    );

    // Misc

    $main_options->add_field(
        array(
            'name' => 'Misc options',
            'id'   => $prefix . 'fundraising_misc_title',
            'type' => 'title',
        )
    );

    $main_options->add_field(
        array(
            'name' => __( 'Article support box text', 'NM' ),
            'desc' => __( 'This will be the default text shown in the red outlined box at the top of articles.', 'NM' ),
            'id'   => $prefix . 'articles_support_box_text',
            'type' => 'textarea_small',
        )
    );
    /**
     * Registers secondary options page, and set main item as parent.
     */

    /*
    $secondary_options = new_cmb2_box( array(
      'id'           => 'nm_secondary_options_page',
      'title'        => 'Links Bar',
      'object_types' => array( 'options-page' ),
      'option_key'   => 'nm_fundraising_links_bar_options',
      'parent_slug'  => 'nm_fundraising_options',
      'capability'      => 'edit_posts',
    ) );
  */
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
  */
}
add_action( 'cmb2_admin_init', 'nm_register_fundraising_options_metabox' );
