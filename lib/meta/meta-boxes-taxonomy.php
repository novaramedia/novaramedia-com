<?php

add_action( 'cmb2_init', 'nm_cmb_taxonomy_metaboxes' );

function nm_cmb_taxonomy_metaboxes() {
  $prefix = '_nm_';

  $cmb_term = new_cmb2_box( array(
    'id'               => $prefix . 'edit',
    'title'            => esc_html__( 'Category Metabox', 'cmb2' ), // Doesn't output for term boxes
    'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
    'taxonomies'       => array( 'category' ), // Tells CMB2 which taxonomies should have these fields
    'new_term_section' => true, // Will display in the "Add New Category" section
  ) );

  $cmb_term->add_field( array(
    'name'     => esc_html__( 'Extra Info', 'cmb2' ),
    'id'       => $prefix . 'extra_info',
    'type'     => 'title',
    'on_front' => false,
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'Microbrand Logo', 'cmb2' ),
    'desc' => esc_html__( 'if this category is for a microbrand with a logo set it here (optional)', 'cmb2' ),
    'id'   => $prefix . 'category_logo',
    'type' => 'file',
  ) );
  
  $cmb_term->add_field( array(
    'name' => esc_html__( 'Podcast subscribe link', 'cmb2' ),
    'desc' => esc_html__( 'url for signup to podcast (optional)', 'cmb2' ),
    'id'   => $prefix . 'podcast_url',
    'type' => 'text_url',
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'Podcast subscribe copy', 'cmb2' ),
    'desc' => esc_html__( 'override copy for podcast signup links (optional)', 'cmb2' ),
    'id'   => $prefix . 'podcast_text',
    'type' => 'text',
  ) );

  $cmb_term->add_field( array(
    'name' => esc_html__( 'Youtube subscribe copy', 'cmb2' ),
    'desc' => esc_html__( 'override copy for Youtube subscribe links (video categories only) (optional)', 'cmb2' ),
    'id'   => $prefix . 'youtube_text',
    'type' => 'text',
  ) );
}
