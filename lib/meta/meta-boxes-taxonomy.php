<?php

add_action('cmb2_init', 'nm_cmb_taxonomy_metaboxes');

function nm_cmb_taxonomy_metaboxes()
{
    $prefix = '_nm_';

    // Category

    $cmb_term = new_cmb2_box(array(
    'id'               => $prefix . 'edit',
    'title'            => esc_html__('Category Metabox', 'cmb2'), // Doesn't output for term boxes
    'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
    'taxonomies'       => array( 'category' ), // Tells CMB2 which taxonomies should have these fields
    'new_term_section' => true, // Will display in the "Add New Category" section
  ));

    $cmb_term->add_field(array(
    'name'     => esc_html__('Extra Info', 'cmb2'),
    'id'       => $prefix . 'extra_info',
    'type'     => 'title',
    'on_front' => false,
  ));

    $cmb_term->add_field(array(
    'name' => esc_html__('Microbrand Logo', 'cmb2'),
    'desc' => esc_html__('if this category is for a microbrand with a logo set it here (optional)', 'cmb2'),
    'id'   => $prefix . 'category_logo',
    'type' => 'file',
  ));

    $cmb_term->add_field(array(
    'name' => esc_html__('Open Graph share image', 'cmb2'),
    'desc' => esc_html__('shown as the image when sharing on socials (optional)', 'cmb2'),
    'id'   => $prefix . 'category_og_image',
    'type' => 'file',
  ));

    $cmb_term->add_field(array(
    'name' => esc_html__('Podcast subscribe link', 'cmb2'),
    'desc' => esc_html__('url for signup to podcast (optional)', 'cmb2'),
    'id'   => $prefix . 'podcast_url',
    'type' => 'text_url',
  ));

    $cmb_term->add_field(array(
    'name' => esc_html__('Podcast subscribe copy', 'cmb2'),
    'desc' => esc_html__('override copy for podcast signup links (optional)', 'cmb2'),
    'id'   => $prefix . 'podcast_text',
    'type' => 'text',
  ));

    $cmb_term->add_field(array(
    'name' => esc_html__('Youtube subscribe copy', 'cmb2'),
    'desc' => esc_html__('override copy for Youtube subscribe links (video categories only) (optional)', 'cmb2'),
    'id'   => $prefix . 'youtube_text',
    'type' => 'text',
  ));

    // Focus

    $cmb_term_focus = new_cmb2_box(array(
    'id'               => $prefix . 'tax_focus',
    'title'            => esc_html__('Focus Metabox', 'cmb2'), // Doesn't output for term boxes
    'object_types'     => array( 'term' ),
    'taxonomies'       => array( 'focus' ),
    'new_term_section' => true, // Will display in the "Add New Category" section
  ));

    $cmb_term_focus->add_field(array(
    'name' => esc_html__('Splash image. Also Open Graph share image', 'cmb2'),
    'desc' => esc_html__('(optional)', 'cmb2'),
    'id'   => $prefix . 'focus_splash',
    'type' => 'file',
  ));

    $cmb_term_focus->add_field(array(
    'name' => esc_html__('Override Splash image', 'cmb2'),
    'desc' => esc_html__('When the splash wants to differ from the Open Graph image(optional)', 'cmb2'),
    'id'   => $prefix . 'focus_splash_override',
    'type' => 'file',
  ));

    $cmb_term_focus_quote_group = $cmb_term_focus->add_field(array(
      'id'          => $prefix . 'focus_quotes',
      'type'        => 'group',
      'options'     => array(
          'group_title'       => __('Quote {#}', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
          'add_button'        => __('Add Another Quote', 'cmb2'),
          'remove_button'     => __('Remove Quote', 'cmb2'),
          'sortable'          => true,
      ),
  ));

    $cmb_term_focus->add_group_field($cmb_term_focus_quote_group, array(
      'name' => 'Quote copy',
      'id'   => 'copy',
      'type' => 'text',
  ));

    $cmb_term_focus->add_group_field($cmb_term_focus_quote_group, array(
      'name' => 'Quote attribution',
      'id'   => 'attribution',
      'type' => 'text',
  ));

    $cmb_term_focus->add_group_field($cmb_term_focus_quote_group, array(
      'name' => 'Image',
      'desc' => esc_html__('(optional)', 'cmb2'),
      'id'   => 'image',
      'type' => 'file',
  ));

    $cmb_term_focus->add_field(array(
    'name' => esc_html__('Credits & footer', 'cmb2'),
    'desc' => esc_html__('shown below all the content on the focus archive page (optional)', 'cmb2'),
    'id'   => $prefix . 'focus_credits',
    'type' => 'wysiwyg',
  ));
}
