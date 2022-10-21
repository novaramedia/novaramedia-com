<?php

add_action( 'cmb2_init', 'nm_cmb_page_text_copy_template' );

function nm_cmb_page_text_copy_template() {
  $prefix = '_nm_';

  $cmb_term = new_cmb2_box( array(
    'id'         => $prefix . 'page_text-copy',
    'title'      => esc_html__( 'Newsletters Metabox', 'cmb2' ),
    'object_types' => array( 'page' ), // Post type
    'show_on'    => array('key' => 'page-template', 'value' => 'page__text-copy.php'),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'Page subtitle', 'cmb2' ),
    'desc' => esc_html__( 'Shows top left of page above the title (optional)', 'cmb2' ),
    'id'   => $prefix . 'subtitle',
    'type' => 'text',
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'CTA link text', 'cmb2' ),
    'desc' => esc_html__( 'Copy for CTA button (optional)', 'cmb2' ),
    'id'   => $prefix . 'cta-copy',
    'type' => 'text',
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'CTA link', 'cmb2' ),
    'desc' => esc_html__( 'Link for CTA button (optional)', 'cmb2' ),
    'id'   => $prefix . 'cta-link',
    'type' => 'text_url',
  ) );
}

