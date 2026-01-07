<?php
/**
 * CMB2 Meta Boxes for How We Are Funded Page Template
 */

add_action( 'cmb2_init', 'nm_how_we_are_funded_page_metaboxes' );

/**
 * Define the metabox and field configurations for the How We Are Funded page.
 */
function nm_how_we_are_funded_page_metaboxes() {

  // Funding Sources Section
  $nm_how_we_are_funded_funding_sources = new_cmb2_box(
    array(
      'id'           => '_nm_how_we_are_funded_funding_sources',
      'title'        => 'Funding Sources',
      'object_types' => array( 'page' ),
      'show_on'      => array(
        'key'   => 'slug',
        'value' => 'how-we-are-funded',
      ),
      'context'      => 'normal',
      'priority'     => 'high',
    )
  );

  $funding_sources_group = $nm_how_we_are_funded_funding_sources->add_field(
    array(
      'id'          => '_nm_funding_sources',
      'type'        => 'group',
      'description' => 'Add funding sources content sections',
      'options'     => array(
        'group_title'   => 'Funding Source {#}',
        'add_button'    => 'Add Another Funding Source',
        'remove_button' => 'Remove Funding Source',
        'sortable'      => true,
        'closed'        => true,
      ),
    )
  );

  $nm_how_we_are_funded_funding_sources->add_group_field(
    $funding_sources_group,
    array(
      'name' => 'Title',
      'id'   => 'title',
      'type' => 'text',
      'desc' => 'Main title for this funding source',
    )
  );

  $nm_how_we_are_funded_funding_sources->add_group_field(
    $funding_sources_group,
    array(
      'name' => 'Subtitle',
      'id'   => 'subtitle',
      'type' => 'text',
      'desc' => 'Subtitle or brief description',
    )
  );

  $nm_how_we_are_funded_funding_sources->add_group_field(
    $funding_sources_group,
    array(
      'name' => 'Content Text',
      'id'   => 'text',
      'type' => 'textarea',
      'desc' => 'Main content text for this funding source',
    )
  );

  $nm_how_we_are_funded_funding_sources->add_group_field(
    $funding_sources_group,
    array(
      'name' => 'Call to Action Text',
      'id'   => 'cta',
      'type' => 'text',
      'desc' => 'Text for the call-to-action button (optional)',
    )
  );

  $nm_how_we_are_funded_funding_sources->add_group_field(
    $funding_sources_group,
    array(
      'name' => 'Call to Action URL',
      'id'   => 'cta_url',
      'type' => 'text_url',
      'desc' => 'URL for the call-to-action button (optional)',
    )
  );

  $nm_how_we_are_funded_funding_sources->add_group_field(
    $funding_sources_group,
    array(
      'name'    => 'Featured Image',
      'id'      => 'image',
      'type'    => 'file',
      'desc'    => 'Upload an image for this funding source (optional)',
      'options' => array(
        'url' => false,
      ),
      'text'    => array(
        'add_upload_file_text' => 'Add Image',
      ),
      'query_args' => array(
        'type' => array(
          'image/gif',
          'image/jpeg',
          'image/png',
        ),
      ),
      'preview_size' => 'medium',
    )
  );

  // Page Settings Section
  $nm_how_we_are_funded_settings = new_cmb2_box(
    array(
      'id'           => 'nm_how_we_are_funded_settings',
      'title'        => 'Page Settings',
      'object_types' => array( 'page' ),
      'show_on'      => array(
        'key'   => 'slug',
        'value' => 'how-we-are-funded',
      ),
      'context'      => 'normal',
      'priority'     => 'high',
    )
  );

  $nm_how_we_are_funded_settings->add_field(
    array(
      'name' => 'YouTube Video ID',
      'id'   => '_nm_support_youtube',
      'type' => 'text',
      'desc' => 'YouTube video ID for the embedded video (leave blank to use default)',
    )
  );
}
