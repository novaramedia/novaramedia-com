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

    // Add a section title
    $support_page_meta_boxes->add_field(
        array(
            'id'   => $prefix . 'how_we_spend_our_funds_title',
            'name' => __( 'How we spend our funds lines', 'nm' ),
            'type' => 'title',
        )
    );

  for ( $i = 1; $i <= 6; $i++ ) {
        $support_page_meta_boxes->add_field(
            array(
                'id'   => $prefix . 'support_funds_line_' . $i,
                // translators: %d refers to the line number (e.g., Line 1, Line 2, etc.)
                'name' => sprintf( __( 'Line %d', 'nm' ), $i ),
                'type' => 'text',
            )
        );
  }
  $support_page_meta_boxes->add_field(
    array(
        'name' => __( 'Our Story heading', 'cmb' ),
        'id'   => $prefix . 'support_our_story_heading',
        'type' => 'text',
    )
  );

  $support_page_meta_boxes->add_field(
    array(
        'name' => __( 'Our Story Bold text', 'cmb' ),
        'id'   => $prefix . 'support_our_story_bold_text',
        'type' => 'textarea_small',
    )
  );
  $support_page_meta_boxes->add_field(
    array(
        'name' => __( 'Our Story regular text', 'cmb' ),
        'id'   => $prefix . 'support_our_story_regular_text',
        'type' => 'textarea_small',
    )
  );
  for ( $i = 1; $i <= 4; $i++ ) {
    $support_page_meta_boxes->add_field(
        array(
            'id'              => $prefix . 'support_carousel_quote_' . $i,
            // translators: %d refers to the quote number (e.g., Quote 1, Quote 2, etc.)
            'name'            => sprintf( __( 'Supporters say quote %d', 'nm' ), $i ),
            'type'            => 'textarea_small',
            'sanitization_cb' => 'nm_limit_carousel_quote_length',
            'desc'            => __( 'Max 95 characters.', 'nm' ),
        )
    );
  }
}
