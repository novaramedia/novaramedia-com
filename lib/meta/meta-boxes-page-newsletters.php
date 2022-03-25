<?php

add_action( 'cmb2_init', 'nm_cmb_page_newsletters_metaboxes' );

function nm_cmb_page_newsletters_metaboxes() {
  $prefix = '_nm_';

  $cmb_term = new_cmb2_box( array(
    'id'         => $prefix . 'page_newsletters',
    'title'      => esc_html__( 'Newsletters Metabox', 'cmb2' ),
    'object_types' => array( 'page' ), // Post type
    'show_on'    => array('key' => 'slug', 'value' => 'the-pick'),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'Youtube embed code', 'cmb2' ),
    'desc' => esc_html__( 'Id of Youtube video. for example if this is the url https://www.youtube.com/watch?v=CmuDcXfBqTg&feature=c4-overview&list=UUOzMAa6IhV6uwYQATYG_2kg then the Id is the value after the ?v= and before the &, for this link CmuDcXfBqTg. Overrides featured image with Youtube embed for promo video (optional)', 'cmb2' ),
    'id'   => $prefix . 'youtube_id',
    'type' => 'text',
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'Support section override text', 'cmb2' ),
    'desc' => esc_html__( 'override copy for the support section below the email signup (optional)', 'cmb2' ),
    'id'   => $prefix . 'support_text',
    'type' => 'textarea_small',
  ) );
}
