<?php

add_action( 'cmb2_init', 'nm_cmb_page_support_metaboxes' );

/**
 * Registers custom meta boxes for the Support page in the WordPress admin.
 */
function nm_cmb_page_support_metaboxes() {
  $prefix = '_nm_';

    $support_page_meta_boxes = new_cmb2_box(
        array(
            'id'           => 'support_page_metabox',
            'title'        => __( 'Support Page Meta', 'cmb' ),
            'object_types' => array( 'page' ), // Post type
            'show_on'      => array(
                'key'   => 'slug',
                'value' => 'support',
            ),
            'context'      => 'normal',
            'priority'     => 'high',
            'show_names'   => true, // Show field names on the left
        )
    );

    $support_page_meta_boxes->add_field(
        array(
            'name' => __( 'Youtube video', 'cmb' ),
            'desc' => __( '(optional)', 'cmb' ),
            'id'   => $prefix . 'support_youtube',
            'type' => 'text',
        )
    );

    $support_page_meta_boxes->add_field(
        array(
            'name' => __( 'Header title (first line)', 'cmb' ),
            'desc' => __( 'Main title on page. First line is in black', 'cmb' ),
            'id'   => $prefix . 'support_header_first_line',
            'type' => 'text',
        )
    );

    $support_page_meta_boxes->add_field(
        array(
            'name' => __( 'Header title (second line)', 'cmb' ),
            'desc' => __( 'Main title on page. Second line is in white', 'cmb' ),
            'id'   => $prefix . 'support_header_second_line',
            'type' => 'text',
        )
    );

    $support_page_meta_boxes->add_field(
        array(
            'name' => __( 'How we spend our funds heading', 'cmb' ),
            'id'   => $prefix . 'support_how_we_spend_our_funds_heading',
            'type' => 'text',
        )
    );

    // Repeatable "how we spend our funds" lines
    $support_page_meta_boxes->add_field(
        array(
            'id'         => $prefix . 'support_funds_lines',
            'name'       => __( 'How we spend our funds lines', 'nm' ),
            'type'       => 'text',
            'repeatable' => true,
            'options'    => array(
                'sortable'     => true,
                'add_row_text' => __( 'Add Another Line', 'nm' ),
            ),
            'desc'       => __( 'Add lines explaining how funds are spent, maximum 6 lines. You can reorder them.', 'nm' ),
        )
    );

  // Repeatable carousel quotes
    $support_page_meta_boxes->add_field(
        array(
            'id'         => $prefix . 'support_carousel_quotes',
            'name'       => __( 'Supporters Say Quotes', 'nm' ),
            'type'       => 'textarea_small',
            'repeatable' => true,
            'options'    => array(
                'sortable'     => true,
                'add_row_text' => __( 'Add Another Quote', 'nm' ),
            ),
            'desc'       => __( 'Add a maximum of 4 quotes from supporters. Max 95 characters each. You can reorder them.', 'nm' ),
        )
    );
}
