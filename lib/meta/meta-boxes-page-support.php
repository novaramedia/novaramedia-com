<?php

add_action( 'cmb2_init', 'nm_cmb_page_support_metaboxes' );

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
        'name' => __( 'Header title first', 'cmb' ),
        'desc' => __( 'Main title on page', 'cmb' ),
        'id'   => $prefix . 'support_header_first_line',
        'type' => 'text',
    )
  );

  $support_page_meta_boxes->add_field(
    array(
        'name' => __( 'Header title second', 'cmb' ),
        'id'   => $prefix . 'support_header_second_line',
        'type' => 'text',
    )
  );

  $support_page_meta_boxes->add_field(
    array(
        'name' => __( 'How we are funded heading', 'cmb' ),
        'id'   => $prefix . 'support_how_we_are_funded_heading',
        'type' => 'text',
    )
  );

  $support_page_meta_boxes->add_field(
    array(
        'name' => __( 'How we are funded text', 'cmb' ),
        'id'   => $prefix . 'support_how_we_are_funded_text',
        'type' => 'textarea_small',
    )
  );

    $support_page_meta_boxes->add_field(
        array(
            'name' => __( 'How we spend our funds heading', 'cmb' ),
            'id'   => $prefix . 'support_how_we_spend_our_funds_heading',
            'type' => 'text',
        )
    );

    $support_page_meta_boxes->add_field(
        array(
            'id'          => $prefix . 'how_we_spend_our_funds_lines',
            'name' => __( 'How we spend our funds lines', 'nm' ),
            'type'        => 'group',
            'description' => __( 'Add each line that explains how funds are spent. (max 5 lines)', 'nm' ),
            'options'     => array(
                'group_title'   => __( 'Line {#}', 'nm' ), // {#} gets replaced by row number
                'add_button'    => __( 'Add Another Line', 'nm' ),
                'remove_button' => __( 'Remove Line', 'nm' ),
                'sortable'      => true,
                'closed'        => true, // Start closed
            ),
        )
    );

    // Add a text field inside each group item for the line text
    $support_page_meta_boxes->add_group_field(
        $prefix . 'how_we_spend_our_funds_lines',
        array(
            'name' => __( 'Text', 'nm' ),
            'id'   => 'text',
            'type' => 'textarea_small',
        )
    );
}
